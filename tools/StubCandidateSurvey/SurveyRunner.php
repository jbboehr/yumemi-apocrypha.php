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

/**
 * @phpstan-import-type DiscoveryRecord from Schema
 * @phpstan-import-type FindingRecord from Schema
 * @phpstan-import-type PackageRecord from Schema
 * @phpstan-import-type RepositoryRecord from Schema
 */
final class SurveyRunner
{
    public function __construct(
        private readonly PackagistClient $packagist,
        private readonly Selector $selector,
        private readonly ArchiveManager $archives,
        private readonly Scanner $scanner,
        private readonly ReportWriter $reportWriter,
    ) {
    }

    /** @return array<mixed> */
    public function collect(
        Config $config,
        string $workspace,
        int $limit,
        bool $offline,
        bool $seedsOnly,
    ): array {
        JsonStorage::ensureDirectory($workspace);
        $startedAt = gmdate(DATE_ATOM);
        $discoveries = $this->packagist->discover($config, $seedsOnly);
        JsonStorage::writeLines($workspace . '/discoveries.jsonl', $discoveries);

        $packages = $this->packagist->resolvePackages($discoveries);
        JsonStorage::writeLines($workspace . '/packages.jsonl', $packages);

        /** @var array<string, true> $excludedPackages */
        $excludedPackages = [];
        /** @var array<string, array{downloadsTotal: int, downloadsMonthly: int, downloadsDaily: int, dependents: int, favers: int, abandoned: bool|string}> $stats */
        $stats = [];
        $selected = [];

        for ($iteration = 0; $iteration < 10; ++$iteration) {
            $selected = $this->selector->select($packages, $config, $limit, $excludedPackages);
            $unknown = [];
            foreach ($selected as $repository) {
                if (!isset($stats[$repository['package']])) {
                    $unknown[] = $repository['package'];
                }
            }
            if ([] !== $unknown) {
                $stats += $this->packagist->fetchStats(array_values(array_unique($unknown)));
            }

            $newExclusion = false;
            foreach ($selected as $repository) {
                $abandoned = $stats[$repository['package']]['abandoned'] ?? false;
                if (false !== $abandoned && !isset($excludedPackages[$repository['package']])) {
                    $excludedPackages[$repository['package']] = true;
                    $newExclusion = true;
                }
            }
            if (!$newExclusion) {
                break;
            }
        }

        foreach ($selected as &$repository) {
            $repository['stats'] = $stats[$repository['package']] ?? null;
        }
        unset($repository);

        if (count($selected) > Config::HARD_REPOSITORY_LIMIT) {
            throw new RuntimeException('Repository selection exceeded the hard safety cap.');
        }

        $repositories = $this->archives->downloadAll($selected, $workspace, $config, $offline);
        JsonStorage::writeLines($workspace . '/repositories.jsonl', $repositories);
        $downloaded = array_values(array_filter(
            $repositories,
            static fn (array $repository): bool => 'downloaded' === $repository['archiveStatus'],
        ));
        $downloadedBytes = array_sum(array_map(
            static fn (array $repository): int => $repository['archiveBytes'] ?? 0,
            $downloaded,
        ));
        /** @var array<string, true> $baselineRepositories */
        $baselineRepositories = array_fill_keys($config->repositoryBlacklist, true);
        $selectedBaselineRepositories = count(array_filter(
            $repositories,
            static fn (array $repository): bool => isset($baselineRepositories[$repository['key']]),
        ));

        $manifest = [
            'schemaVersion' => 1,
            'snapshot' => basename($workspace),
            'profile' => $config->profile,
            'startedAt' => $startedAt,
            'collectedAt' => gmdate(DATE_ATOM),
            'status' => 'collected',
            'requestedLimit' => $limit,
            'hardRepositoryLimit' => Config::HARD_REPOSITORY_LIMIT,
            'seedsOnly' => $seedsOnly,
            'offline' => $offline,
            'backfillFromPopular' => $config->backfillFromPopular,
            'focusedTags' => $config->focusedTags,
            'noisyTags' => $config->noisyTags,
            'tagDiscoveries' => $this->tagDiscoveryCounts($discoveries, $config),
            'repositoryBlacklistCount' => count($config->repositoryBlacklist),
            'repositoryWhitelistCount' => count($config->repositoryWhitelist),
            'counts' => [
                'discoveries' => count($discoveries),
                'eligiblePackages' => count($packages),
                'excludedAbandonedPackages' => count($excludedPackages),
                'selectedRepositories' => count($repositories),
                'selectedBaselineRepositories' => $selectedBaselineRepositories,
                'selectedNewRepositories' => count($repositories) - $selectedBaselineRepositories,
                'downloadedRepositories' => count($downloaded),
                'downloadedBytes' => $downloadedBytes,
            ],
        ];
        JsonStorage::write($workspace . '/manifest.json', $manifest);

        return $manifest;
    }

    /**
     * @param list<DiscoveryRecord> $discoveries
     * @return array{focused: array<string, int>, noisy: array<string, int>}
     */
    private function tagDiscoveryCounts(array $discoveries, Config $config): array
    {
        $counts = [
            'focused' => array_fill_keys($config->focusedTags, 0),
            'noisy' => array_fill_keys($config->noisyTags, 0),
        ];
        foreach ($discoveries as $discovery) {
            foreach ($discovery['sources'] as $source) {
                $tag = $source['tag'];
                if (null !== $tag && isset($counts[$source['stratum']][$tag])) {
                    ++$counts[$source['stratum']][$tag];
                }
            }
        }

        return $counts;
    }

    /** @return array<mixed> */
    public function scan(Config $config, string $workspace): array
    {
        /** @var list<RepositoryRecord> $repositories */
        $repositories = JsonStorage::readLines($workspace . '/repositories.jsonl');
        $findings = $this->scanner->scanAll($repositories, $config);
        JsonStorage::writeLines($workspace . '/findings.jsonl', $findings);
        $manifest = JsonStorage::read($workspace . '/manifest.json');
        $manifest['status'] = 'scanned';
        $manifest['scannedAt'] = gmdate(DATE_ATOM);
        if (!isset($manifest['counts']) || !is_array($manifest['counts'])) {
            $manifest['counts'] = [];
        }
        $manifest['counts']['inspectedRepositories'] = count($findings);
        $manifest['counts']['collisionCandidates'] = count(array_filter(
            $findings,
            static fn (array $finding): bool => in_array($finding['locality'], ['signature', 'class', 'package', 'repository'], true),
        ));
        JsonStorage::write($workspace . '/manifest.json', $manifest);

        return $manifest;
    }

    /** @return array<mixed> */
    public function report(string $workspace): array
    {
        /** @var list<RepositoryRecord> $repositories */
        $repositories = JsonStorage::readLines($workspace . '/repositories.jsonl');
        /** @var list<FindingRecord> $findings */
        $findings = JsonStorage::readLines($workspace . '/findings.jsonl');
        $manifest = JsonStorage::read($workspace . '/manifest.json');
        $this->reportWriter->write($workspace . '/report.md', $repositories, $findings, $manifest);
        $manifest['status'] = 'reported';
        $manifest['reportedAt'] = gmdate(DATE_ATOM);
        JsonStorage::write($workspace . '/manifest.json', $manifest);

        return $manifest;
    }

    /** @param array<mixed> $manifest */
    public function assertCoverage(array $manifest): void
    {
        $counts = $manifest['counts'] ?? null;
        if (!is_array($counts)) {
            throw new RuntimeException('Survey manifest contains no coverage counts.');
        }
        $selected = $counts['selectedRepositories'] ?? 0;
        $inspected = $counts['inspectedRepositories'] ?? 0;
        if (!is_int($selected) || !is_int($inspected)) {
            throw new RuntimeException('Survey manifest contains invalid coverage counts.');
        }
        $required = (int) ceil($selected * 0.9);
        if ($inspected < $required) {
            throw new RuntimeException(sprintf(
                'Only %d of %d selected repositories were inspected; at least %d are required.',
                $inspected,
                $selected,
                $required,
            ));
        }
    }
}
