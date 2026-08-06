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
 * @phpstan-import-type FindingRecord from Schema
 * @phpstan-import-type RepositoryRecord from Schema
 */
final class ReportWriter
{
    /**
     * @param list<RepositoryRecord> $repositories
     * @param list<FindingRecord> $findings
     * @param array<mixed> $manifest
     */
    public function write(string $path, array $repositories, array $findings, array $manifest): void
    {
        $repositoryByKey = [];
        foreach ($repositories as $repository) {
            $repositoryByKey[$repository['key']] = $repository;
        }

        usort($findings, function (array $left, array $right) use ($repositoryByKey): int {
            $leftRepository = $repositoryByKey[$left['repositoryKey']];
            $rightRepository = $repositoryByKey[$right['repositoryKey']];
            $leftDownloads = $leftRepository['stats']['downloadsMonthly'] ?? 0;
            $rightDownloads = $rightRepository['stats']['downloadsMonthly'] ?? 0;

            return [
                $this->localityRank($left['locality']),
                -$left['distinctScaleCount'],
                -$left['evidenceCount'],
                -$leftDownloads,
                $left['package'],
            ] <=> [
                $this->localityRank($right['locality']),
                -$right['distinctScaleCount'],
                -$right['evidenceCount'],
                -$rightDownloads,
                $right['package'],
            ];
        });

        $snapshot = isset($manifest['snapshot']) && is_string($manifest['snapshot']) ? $manifest['snapshot'] : 'unknown';
        $profile = isset($manifest['profile']) && is_string($manifest['profile']) ? $manifest['profile'] : 'general';
        $collectedAt = isset($manifest['collectedAt']) && is_string($manifest['collectedAt']) ? $manifest['collectedAt'] : 'unknown';
        $counts = isset($manifest['counts']) && is_array($manifest['counts']) ? $manifest['counts'] : [];
        $lines = [
            '# Stub Candidate Survey',
            '',
            sprintf('- Snapshot: `%s`', $snapshot),
            sprintf('- Profile: `%s`', $profile),
            sprintf('- Collected at: `%s`', $collectedAt),
            sprintf('- Selected repositories: %d', count($repositories)),
            sprintf('- Successfully inspected: %d', count($findings)),
            sprintf('- Repository cap: %d', Config::HARD_REPOSITORY_LIMIT),
        ];
        $selectedNew = $counts['selectedNewRepositories'] ?? null;
        $selectedBaseline = $counts['selectedBaselineRepositories'] ?? null;
        if (is_int($selectedNew) && is_int($selectedBaseline)) {
            $lines[] = sprintf('- New repositories: %d', $selectedNew);
            $lines[] = sprintf('- Baseline overlap: %d', $selectedBaseline);
        }
        $lines[] = '';
        $lines[] = '## Yield by stratum';
        $lines[] = '';
        $lines[] = '| Stratum | Selected | Inspected | Collision candidates | Single-unit | No evidence |';
        $lines[] = '|---|---:|---:|---:|---:|---:|';

        foreach (['curated', 'focused', 'noisy', 'popular'] as $stratum) {
            $selected = count(array_filter($repositories, static fn (array $repository): bool => $repository['stratum'] === $stratum));
            $stratumFindings = array_values(array_filter($findings, static fn (array $finding): bool => $finding['stratum'] === $stratum));
            $collisions = count(array_filter($stratumFindings, fn (array $finding): bool => $this->isCollision($finding)));
            $single = count(array_filter($stratumFindings, static fn (array $finding): bool => 'single-unit' === $finding['locality']));
            $none = count(array_filter($stratumFindings, static fn (array $finding): bool => 'none' === $finding['locality']));
            $lines[] = sprintf('| %s | %d | %d | %d | %d | %d |', $stratum, $selected, count($stratumFindings), $collisions, $single, $none);
        }

        $this->appendTagYield($lines, $repositories, $findings, $manifest);

        $lines[] = '';
        $lines[] = '## Ranked findings';
        $lines[] = '';
        foreach ($findings as $index => $finding) {
            $repository = $repositoryByKey[$finding['repositoryKey']];
            $dimensionText = [];
            foreach ($finding['dimensions'] as $dimension => $scales) {
                $dimensionText[] = sprintf('%s: %s', $dimension, implode(', ', $scales));
            }
            $lines[] = sprintf(
                '%d. **`%s` (`%s`)** — %s; %s; %d distinct units; %d monthly downloads',
                $index + 1,
                $this->escape($finding['package']),
                $this->escape($finding['version']),
                $finding['stratum'],
                $finding['locality'],
                $finding['distinctScaleCount'],
                $repository['stats']['downloadsMonthly'] ?? 0,
            );
            if ([] !== $dimensionText) {
                $lines[] = sprintf('   - Units: %s', $this->escape(implode('; ', $dimensionText)));
            }
        }

        $lines[] = '';
        $lines[] = '## Reading the results';
        $lines[] = '';
        $lines[] = 'These findings are broad discovery leads, not verified integration recommendations. A `signature` locality means that multiple units appeared within one public declaration across its signature, documentation, or inspected implementation; it does not prove that the upstream signature exposes each unit as a branded scalar boundary.';
        $lines[] = '';
        $lines[] = 'Unit-conversion libraries, runtime-selected units, ordinary-language matches, and implementation-only conversions require human rejection or reclassification. Apply the evaluation gates in `docs/development/planning.md` before promoting any finding to the roadmap.';

        $lines[] = '';
        $lines[] = '## Manual verification queue';
        $lines[] = '';
        $lines[] = 'The first 25 entries follow the overall collision ranking. Five additional entries preserve the highest-ranked noisy-tag results.';
        $lines[] = '';
        $queue = $this->manualQueue($findings);
        foreach ($queue as $index => $finding) {
            $lines[] = sprintf('%d. `%s` — %s', $index + 1, $finding['package'], $finding['locality']);
        }

        $lines[] = '';
        $lines[] = '> Automated findings are discovery leads. Verify units and scales against upstream source, tests, and documentation before adding an integration.';
        $lines[] = '';

        JsonStorage::ensureDirectory(dirname($path));
        if (false === file_put_contents($path, implode("\n", $lines))) {
            throw new RuntimeException(sprintf('Unable to write survey report: %s', $path));
        }
    }

    /**
     * @param list<FindingRecord> $findings
     * @return list<FindingRecord>
     */
    private function manualQueue(array $findings): array
    {
        $findings = array_values(array_filter($findings, fn (array $finding): bool => $this->isCollision($finding)));
        $queue = [];
        $seen = [];
        foreach (array_slice($findings, 0, 25) as $finding) {
            $queue[] = $finding;
            $seen[$finding['repositoryKey']] = true;
        }

        $noisyAdded = 0;
        foreach ($findings as $finding) {
            if ('noisy' !== $finding['stratum'] || isset($seen[$finding['repositoryKey']])) {
                continue;
            }
            $queue[] = $finding;
            $seen[$finding['repositoryKey']] = true;
            if (++$noisyAdded >= 5 || count($queue) >= 30) {
                break;
            }
        }
        foreach ($findings as $finding) {
            if (count($queue) >= 30) {
                break;
            }
            if (!isset($seen[$finding['repositoryKey']])) {
                $queue[] = $finding;
                $seen[$finding['repositoryKey']] = true;
            }
        }

        return $queue;
    }

    /**
     * @param list<string> $lines
     * @param list<RepositoryRecord> $repositories
     * @param list<FindingRecord> $findings
     * @param array<mixed> $manifest
     */
    private function appendTagYield(array &$lines, array $repositories, array $findings, array $manifest): void
    {
        $tagDiscoveries = $manifest['tagDiscoveries'] ?? null;
        if (!is_array($tagDiscoveries)) {
            return;
        }
        /** @var array<string, FindingRecord> $findingByRepository */
        $findingByRepository = [];
        foreach ($findings as $finding) {
            $findingByRepository[$finding['repositoryKey']] = $finding;
        }

        $rows = [];
        foreach (['focused', 'noisy'] as $stratum) {
            $tags = $tagDiscoveries[$stratum] ?? null;
            if (!is_array($tags)) {
                continue;
            }
            foreach ($tags as $tag => $discovered) {
                if (!is_string($tag) || !is_int($discovered)) {
                    continue;
                }
                $selected = 0;
                $inspected = 0;
                $collisions = 0;
                foreach ($repositories as $repository) {
                    if (!$this->hasTag($repository, $stratum, $tag)) {
                        continue;
                    }
                    ++$selected;
                    $finding = $findingByRepository[$repository['key']] ?? null;
                    if (null !== $finding) {
                        ++$inspected;
                        if ($this->isCollision($finding)) {
                            ++$collisions;
                        }
                    }
                }
                $rows[] = sprintf('| %s | `%s` | %d | %d | %d | %d |', $stratum, $tag, $discovered, $selected, $inspected, $collisions);
            }
        }

        if ([] === $rows) {
            return;
        }

        $lines[] = '';
        $lines[] = '## Yield by tag';
        $lines[] = '';
        $lines[] = 'Repositories can appear under more than one tag, so rows are not additive.';
        $lines[] = '';
        $lines[] = '| Stratum | Tag | Discovered | Selected | Inspected | Collision candidates |';
        $lines[] = '|---|---|---:|---:|---:|---:|';
        array_push($lines, ...$rows);
    }

    /** @param RepositoryRecord $repository */
    private function hasTag(array $repository, string $stratum, string $tag): bool
    {
        foreach ($repository['sources'] as $source) {
            if ($source['stratum'] === $stratum && $source['tag'] === $tag) {
                return true;
            }
        }

        return false;
    }

    /** @param FindingRecord $finding */
    private function isCollision(array $finding): bool
    {
        return $this->localityRank($finding['locality']) <= 3;
    }

    private function localityRank(string $locality): int
    {
        return match ($locality) {
            'signature' => 0,
            'class' => 1,
            'package' => 2,
            'repository' => 3,
            'single-unit' => 4,
            default => 5,
        };
    }

    private function escape(string $value): string
    {
        return str_replace('|', '\\|', $value);
    }
}
