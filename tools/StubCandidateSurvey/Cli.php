<?php

/**
 * +--------------------------------------------------------------------------------------------------------------+
 * |        *                 .                         *                  .                         *            |
 * |   .              *                      .                    *                      .                        |
 * |             .                 .                  *                         .                 *               |
 * -      *                    .             *                    .                         .                     -
 *
 *                          Yumemi Apocrypha『夢見外典』〜ＹＵＭＥＭＩ　ＡＰＯＣＲＹＰＨＡ〜
 *
 * -                                          .----------------.                                                  -
 * |                                      .--'        __        '--.                                              |
 * |                                  .--'          .'  '.          '--.                                          |
 * |                             .---'            .'      '.            '---.                                     |
 * +--------------------------------------------------------------------------------------------------------------+
 *
 * Copyright (c) anno Domini nostri Jesu Christi MMXXVI, John Boehr & contributors
 *
 * SPDX-License-Identifier: AGPL-3.0-only WITH romic-exception
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License version 3,
 * as published by the Free Software Foundation, together with the Romic
 * Exception (an additional permission under section 7 of that license).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * and the Romic Exception along with this program.  If not, see
 * <http://www.gnu.org/licenses/> and the LICENSE_EXCEPTION file.
 */

declare(strict_types=1);

namespace jbboehr\Yumemi\Apocrypha\Tools\StubCandidateSurvey;

use RuntimeException;
use Throwable;

final class Cli
{
    public function __construct(private readonly string $root)
    {
    }

    /** @param list<string> $arguments */
    public function run(array $arguments): int
    {
        try {
            [$command, $options] = $this->parse($arguments);
            if ('help' === $command) {
                $this->help();

                return 0;
            }

            $config = Config::fromFile($this->configPath($options['profile']));
            if ($config->profile !== $options['profile']) {
                throw new RuntimeException(sprintf('Survey configuration declares profile %s, expected %s.', $config->profile, $options['profile']));
            }
            $limit = $options['limit'];
            if ($limit < 1 || $limit > Config::HARD_REPOSITORY_LIMIT) {
                throw new RuntimeException(sprintf('The repository limit must be between 1 and %d.', Config::HARD_REPOSITORY_LIMIT));
            }
            $workspace = $this->workspace($options['workspace'], $command, $options['offline']);
            $runner = $this->runner($config, $workspace, $options['offline']);

            if ('collect' === $command || 'run' === $command) {
                fwrite(STDOUT, sprintf("Collecting candidate data in %s\n", $workspace));
                $runner->collect($config, $workspace, $limit, $options['offline'], $options['seedsOnly']);
            }
            if ('scan' === $command || 'run' === $command) {
                fwrite(STDOUT, "Scanning public unit evidence\n");
                $runner->scan($config, $workspace);
            }
            if ('report' === $command || 'run' === $command) {
                fwrite(STDOUT, "Writing survey report\n");
                $manifest = $runner->report($workspace);
                if ('run' === $command) {
                    $runner->assertCoverage($manifest);
                }
            }

            fwrite(STDOUT, sprintf("Survey workspace: %s\n", $workspace));

            return 0;
        } catch (Throwable $exception) {
            fwrite(STDERR, sprintf("Survey failed: %s\n", $exception->getMessage()));

            return 1;
        }
    }

    /**
     * @param list<string> $arguments
     * @return array{'collect'|'scan'|'report'|'run'|'help', array{workspace: string|null, profile: string, limit: int, offline: bool, seedsOnly: bool}}
     */
    private function parse(array $arguments): array
    {
        $command = $arguments[0] ?? 'help';
        if (in_array($command, ['-h', '--help'], true)) {
            $command = 'help';
        }
        if (!in_array($command, ['collect', 'scan', 'report', 'run', 'help'], true)) {
            throw new RuntimeException(sprintf('Unknown command: %s', $command));
        }

        $options = ['workspace' => null, 'profile' => 'general', 'limit' => Config::HARD_REPOSITORY_LIMIT, 'offline' => false, 'seedsOnly' => false];
        foreach (array_slice($arguments, 1) as $argument) {
            if ('--offline' === $argument) {
                $options['offline'] = true;
            } elseif ('--seeds-only' === $argument) {
                $options['seedsOnly'] = true;
            } elseif (str_starts_with($argument, '--workspace=')) {
                $options['workspace'] = substr($argument, strlen('--workspace='));
            } elseif (str_starts_with($argument, '--profile=')) {
                $profile = substr($argument, strlen('--profile='));
                if (1 !== preg_match('/^[a-z][a-z0-9-]*$/', $profile)) {
                    throw new RuntimeException('The --profile option must use lowercase letters, digits, and hyphens.');
                }
                $options['profile'] = $profile;
            } elseif (str_starts_with($argument, '--limit=')) {
                $value = substr($argument, strlen('--limit='));
                if (!ctype_digit($value)) {
                    throw new RuntimeException('The --limit option must be a positive integer.');
                }
                $options['limit'] = (int) $value;
            } else {
                throw new RuntimeException(sprintf('Unknown option: %s', $argument));
            }
        }

        /** @var 'collect'|'scan'|'report'|'run'|'help' $command */
        return [$command, $options];
    }

    private function configPath(string $profile): string
    {
        $suffix = 'general' === $profile ? '' : '-' . $profile;
        $path = $this->root . '/tools/stub-candidate-survey' . $suffix . '.json';
        if (!is_file($path)) {
            throw new RuntimeException(sprintf('Unknown survey profile: %s', $profile));
        }

        return $path;
    }

    private function workspace(?string $requested, string $command, bool $offline): string
    {
        $base = $this->root . '/tmp/stub-candidate-survey';
        if (null !== $requested && '' !== $requested) {
            if (str_contains($requested, '..')) {
                throw new RuntimeException('Survey workspace must not contain parent-directory segments.');
            }
            $workspace = str_starts_with($requested, '/') ? $requested : $base . '/' . trim($requested, '/');
            if (!str_starts_with($workspace, $base . '/')) {
                throw new RuntimeException(sprintf('Survey workspace must be beneath %s.', $base));
            }

            return $workspace;
        }

        if (in_array($command, ['scan', 'report'], true) || $offline) {
            $matches = glob($base . '/*', GLOB_ONLYDIR);
            $workspaces = false === $matches ? [] : $matches;
            rsort($workspaces);
            if ([] === $workspaces) {
                throw new RuntimeException('No existing survey workspace is available.');
            }

            return $workspaces[0];
        }

        return $base . '/' . gmdate('Ymd\THis\Z');
    }

    private function runner(Config $config, string $workspace, bool $offline): SurveyRunner
    {
        $composer = JsonStorage::read($this->root . '/composer.json');
        $authors = $composer['authors'] ?? [];
        $email = 'jbboehr@gmail.com';
        if (is_array($authors) && isset($authors[0]) && is_array($authors[0]) && isset($authors[0]['email']) && is_string($authors[0]['email'])) {
            $email = $authors[0]['email'];
        }
        $http = new CachedHttpClient(
            $config->http['concurrency'],
            $config->http['retries'],
            sprintf('yumemi-apocrypha-stub-candidate-survey/1.0 mailto=%s', $email),
        );
        $packagist = new PackagistClient(
            $http,
            new RepositoryNormalizer(),
            $workspace . '/cache/http',
            $offline,
        );

        return new SurveyRunner(
            $packagist,
            new Selector(),
            new ArchiveManager($http),
            new Scanner(new UnitLexicon()),
            new ReportWriter(),
        );
    }

    private function help(): void
    {
        fwrite(STDOUT, <<<'HELP'
Usage: php tools/stub-candidate-survey.php <command> [options]

Commands:
  collect   Collect Packagist metadata and guarded source archives
  scan      Scan an existing workspace for public unit evidence
  report    Render an existing workspace as Markdown
  run       Collect, scan, report, and enforce the 90% coverage threshold

Options:
  --workspace=NAME  Use a named workspace beneath tmp/stub-candidate-survey
  --profile=NAME    Select a survey profile (default: general)
  --limit=N         Reduce the repository cap (maximum: 250)
  --offline         Use only an existing workspace and cached responses
  --seeds-only      Restrict collection to curated seeds; useful for smoke tests
  --help            Show this help

HELP);
    }
}
