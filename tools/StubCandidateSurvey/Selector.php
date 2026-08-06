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

/**
 * @phpstan-import-type PackageRecord from Schema
 * @phpstan-import-type RepositoryRecord from Schema
 * @phpstan-import-type SourceRecord from Schema
 */
final class Selector
{
    /**
     * @param list<PackageRecord> $packages
     * @param array<string, true> $excludedPackages
     * @return list<RepositoryRecord>
     */
    public function select(array $packages, Config $config, int $limit, array $excludedPackages = []): array
    {
        /** @var array<string, true> $repositoryBlacklist */
        $repositoryBlacklist = array_fill_keys($config->repositoryBlacklist, true);
        /** @var array<string, true> $repositoryWhitelist */
        $repositoryWhitelist = array_fill_keys($config->repositoryWhitelist, true);
        $repositories = $this->groupRepositories($packages, $excludedPackages, $repositoryBlacklist, $repositoryWhitelist);
        $quotas = $config->quotasForLimit($limit);
        $selected = [];
        $selectedKeys = [];
        $generalOwnerCounts = [];
        $noisyOwnerCounts = [];

        $curated = $this->orderedForStratum($repositories, 'curated', []);
        $focused = $this->roundRobinForTags($repositories, 'focused', $config->focusedTags);
        $noisy = $this->roundRobinForTags($repositories, 'noisy', $config->noisyTags);
        $popular = $this->orderedForStratum($repositories, 'popular', []);

        $this->take(
            $curated,
            $quotas['curated'],
            $selected,
            $selectedKeys,
            $generalOwnerCounts,
            $noisyOwnerCounts,
            $config,
            false,
            'curated',
        );
        $this->take(
            $focused,
            $quotas['focused'],
            $selected,
            $selectedKeys,
            $generalOwnerCounts,
            $noisyOwnerCounts,
            $config,
            true,
            'focused',
        );
        $this->take(
            $noisy,
            $quotas['noisy'],
            $selected,
            $selectedKeys,
            $generalOwnerCounts,
            $noisyOwnerCounts,
            $config,
            true,
            'noisy',
        );
        $this->take(
            $popular,
            $quotas['popular'],
            $selected,
            $selectedKeys,
            $generalOwnerCounts,
            $noisyOwnerCounts,
            $config,
            true,
            'popular',
        );

        if ($config->backfillFromPopular && count($selected) < $limit) {
            $this->take(
                $popular,
                $limit - count($selected),
                $selected,
                $selectedKeys,
                $generalOwnerCounts,
                $noisyOwnerCounts,
                $config,
                true,
                'popular',
            );
        }

        if (count($selected) < $limit) {
            $fallback = array_merge($focused, $noisy, $curated);
            $this->take(
                $fallback,
                $limit - count($selected),
                $selected,
                $selectedKeys,
                $generalOwnerCounts,
                $noisyOwnerCounts,
                $config,
                true,
                null,
            );
        }

        return $selected;
    }

    /**
     * @param list<PackageRecord> $packages
     * @param array<string, true> $excludedPackages
     * @param array<string, true> $repositoryBlacklist
     * @param array<string, true> $repositoryWhitelist
     * @return list<RepositoryRecord>
     */
    private function groupRepositories(
        array $packages,
        array $excludedPackages,
        array $repositoryBlacklist,
        array $repositoryWhitelist,
    ): array {
        /** @var array<string, list<PackageRecord>> $grouped */
        $grouped = [];
        foreach ($packages as $package) {
            if (isset($excludedPackages[$package['name']])) {
                continue;
            }
            if (isset($repositoryBlacklist[$package['repositoryKey']]) && !isset($repositoryWhitelist[$package['repositoryKey']])) {
                continue;
            }
            $grouped[$package['repositoryKey']][] = $package;
        }

        $repositories = [];
        foreach ($grouped as $repositoryPackages) {
            if ([] === $repositoryPackages) {
                continue;
            }
            usort($repositoryPackages, fn (array $left, array $right): int => $this->comparePackages($left, $right));
            $representative = $repositoryPackages[0];
            $sources = [];
            $packageNames = [];
            foreach ($repositoryPackages as $package) {
                $packageNames[] = $package['name'];
                foreach ($package['sources'] as $source) {
                    $sources[$this->sourceKey($source)] = $source;
                }
            }

            $sourceList = array_values($sources);
            $packageNames = array_values(array_unique($packageNames));
            usort($sourceList, fn (array $left, array $right): int => $this->compareSources($left, $right));
            $stratum = $sourceList[0]['stratum'];

            $repositories[] = [
                'key' => $representative['repositoryKey'],
                'url' => $representative['repositoryUrl'],
                'owner' => $representative['owner'],
                'package' => $representative['name'],
                'packages' => $packageNames,
                'version' => $representative['version'],
                'stratum' => $stratum,
                'sources' => $sourceList,
                'distUrl' => $representative['distUrl'],
                'distType' => $representative['distType'],
                'stats' => null,
                'archivePath' => null,
                'archiveSha256' => null,
                'archiveBytes' => null,
                'archiveStatus' => 'pending',
                'archiveError' => null,
            ];
        }

        return $repositories;
    }

    /**
     * @param list<RepositoryRecord> $repositories
     * @param 'curated'|'focused'|'noisy'|'popular' $stratum
     * @param list<string> $tags
     * @return list<RepositoryRecord>
     */
    private function orderedForStratum(array $repositories, string $stratum, array $tags): array
    {
        $filtered = array_values(array_filter(
            $repositories,
            static fn (array $repository): bool => self::hasStratum($repository, $stratum),
        ));
        usort($filtered, function (array $left, array $right) use ($stratum, $tags): int {
            $leftRank = $this->bestRank($left, $stratum, $tags);
            $rightRank = $this->bestRank($right, $stratum, $tags);

            return [$leftRank, $left['package']] <=> [$rightRank, $right['package']];
        });

        return $filtered;
    }

    /**
     * @param list<RepositoryRecord> $repositories
     * @param 'focused'|'noisy' $stratum
     * @param list<string> $tags
     * @return list<RepositoryRecord>
     */
    private function roundRobinForTags(array $repositories, string $stratum, array $tags): array
    {
        /** @var array<string, list<RepositoryRecord>> $byTag */
        $byTag = [];
        foreach ($tags as $tag) {
            $byTag[$tag] = array_values(array_filter(
                $repositories,
                static fn (array $repository): bool => self::hasTag($repository, $stratum, $tag),
            ));
            usort($byTag[$tag], function (array $left, array $right) use ($stratum, $tag): int {
                return [$this->bestRank($left, $stratum, [$tag]), $left['package']]
                    <=> [$this->bestRank($right, $stratum, [$tag]), $right['package']];
            });
        }

        $result = [];
        $seen = [];
        for ($rank = 0; ; ++$rank) {
            $added = false;
            foreach ($tags as $tag) {
                $repository = $byTag[$tag][$rank] ?? null;
                if (null === $repository) {
                    continue;
                }
                $added = true;
                if (isset($seen[$repository['key']])) {
                    continue;
                }
                $seen[$repository['key']] = true;
                $result[] = $repository;
            }
            if (!$added) {
                break;
            }
        }

        return $result;
    }

    /**
     * @param list<RepositoryRecord> $candidates
     * @param list<RepositoryRecord> $selected
     * @param array<string, true> $selectedKeys
     * @param array<string, int> $generalOwnerCounts
     * @param array<string, int> $noisyOwnerCounts
     * @param 'curated'|'focused'|'noisy'|'popular'|null $selectedStratum
     */
    private function take(
        array $candidates,
        int $count,
        array &$selected,
        array &$selectedKeys,
        array &$generalOwnerCounts,
        array &$noisyOwnerCounts,
        Config $config,
        bool $enforceOwnerLimits,
        ?string $selectedStratum,
    ): void {
        $taken = 0;
        foreach ($candidates as $candidate) {
            if ($taken >= $count) {
                return;
            }
            if (isset($selectedKeys[$candidate['key']])) {
                continue;
            }

            $effectiveStratum = $selectedStratum ?? $candidate['stratum'];
            $owner = $candidate['owner'];
            if ($enforceOwnerLimits && ($generalOwnerCounts[$owner] ?? 0) >= $config->ownerLimits['general']) {
                continue;
            }
            if ('noisy' === $effectiveStratum && ($noisyOwnerCounts[$owner] ?? 0) >= $config->ownerLimits['noisy']) {
                continue;
            }

            $candidate['stratum'] = $effectiveStratum;
            $selectedKeys[$candidate['key']] = true;
            $selected[] = $candidate;
            ++$taken;
            if ($enforceOwnerLimits) {
                $generalOwnerCounts[$owner] = ($generalOwnerCounts[$owner] ?? 0) + 1;
            }
            if ('noisy' === $effectiveStratum) {
                $noisyOwnerCounts[$owner] = ($noisyOwnerCounts[$owner] ?? 0) + 1;
            }
        }
    }

    /** @param RepositoryRecord $repository */
    private static function hasStratum(array $repository, string $stratum): bool
    {
        foreach ($repository['sources'] as $source) {
            if ($source['stratum'] === $stratum) {
                return true;
            }
        }

        return false;
    }

    /** @param RepositoryRecord $repository */
    private static function hasTag(array $repository, string $stratum, string $tag): bool
    {
        foreach ($repository['sources'] as $source) {
            if ($source['stratum'] === $stratum && $source['tag'] === $tag) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param RepositoryRecord $repository
     * @param list<string> $tags
     */
    private function bestRank(array $repository, string $stratum, array $tags): int
    {
        $rank = PHP_INT_MAX;
        foreach ($repository['sources'] as $source) {
            if ($source['stratum'] !== $stratum) {
                continue;
            }
            if ([] !== $tags && (null === $source['tag'] || !in_array($source['tag'], $tags, true))) {
                continue;
            }
            $rank = min($rank, $source['rank']);
        }

        return $rank;
    }

    /**
     * @param PackageRecord $left
     * @param PackageRecord $right
     */
    private function comparePackages(array $left, array $right): int
    {
        $leftSource = $left['sources'][0];
        $rightSource = $right['sources'][0];

        return [$this->stratumPriority($leftSource['stratum']), $leftSource['rank'], $left['name']]
            <=> [$this->stratumPriority($rightSource['stratum']), $rightSource['rank'], $right['name']];
    }

    /**
     * @param SourceRecord $left
     * @param SourceRecord $right
     */
    private function compareSources(array $left, array $right): int
    {
        return [$this->stratumPriority($left['stratum']), $left['rank'], $left['tag'] ?? '']
            <=> [$this->stratumPriority($right['stratum']), $right['rank'], $right['tag'] ?? ''];
    }

    /** @param SourceRecord $source */
    private function sourceKey(array $source): string
    {
        return implode("\0", [$source['stratum'], $source['tag'] ?? '', (string) $source['rank'], $source['role'] ?? '']);
    }

    private function stratumPriority(string $stratum): int
    {
        return match ($stratum) {
            'curated' => 0,
            'focused' => 1,
            'noisy' => 2,
            'popular' => 3,
            default => 4,
        };
    }
}
