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

use Composer\MetadataMinifier\MetadataMinifier;
use Composer\Semver\VersionParser;
use JsonException;
use RuntimeException;

/**
 * @phpstan-import-type DiscoveryRecord from Schema
 * @phpstan-import-type PackageRecord from Schema
 * @phpstan-import-type SourceRecord from Schema
 * @phpstan-import-type StatsRecord from Schema
 * @phpstan-type EligibleRelease array{version: string, versionNormalized: string, type: string, sourceUrl: string, distUrl: string, distType: string}
 */
final class PackagistClient
{
    public function __construct(
        private readonly CachedHttpClient $http,
        private readonly RepositoryNormalizer $normalizer,
        private readonly string $cacheDirectory,
        private readonly bool $offline,
    ) {
    }

    /** @return list<DiscoveryRecord> */
    public function discover(Config $config, bool $seedsOnly = false): array
    {
        if ($seedsOnly) {
            return $this->seedDiscoveries($config);
        }

        /** @var array<string, non-empty-string> $urls */
        $urls = [];
        $popularPages = (int) ceil($config->popularPackageCount / 100);
        for ($page = 1; $page <= $popularPages; ++$page) {
            $urls['popular:' . $page] = sprintf(
                'https://packagist.org/explore/popular.json?page=%d&per_page=100',
                $page,
            );
        }
        foreach ($config->focusedTags as $tag) {
            $urls['focused:' . $tag] = sprintf(
                'https://packagist.org/search.json?tags=%s&per_page=%d',
                rawurlencode($tag),
                $config->searchResultsPerTag,
            );
        }
        foreach ($config->noisyTags as $tag) {
            $urls['noisy:' . $tag] = sprintf(
                'https://packagist.org/search.json?tags=%s&per_page=%d',
                rawurlencode($tag),
                $config->searchResultsPerTag,
            );
        }

        $responses = $this->http->fetchMany($urls, $this->cacheDirectory, $this->offline);

        return $this->parseDiscoveryResponses($responses, $config);
    }

    /**
     * @param array<string, string> $responses
     * @return list<DiscoveryRecord>
     */
    public function parseDiscoveryResponses(array $responses, Config $config): array
    {
        $discoveries = $this->indexedSeedDiscoveries($config);

        $popularRank = 0;
        $popularPages = (int) ceil($config->popularPackageCount / 100);
        for ($page = 1; $page <= $popularPages; ++$page) {
            $body = $responses['popular:' . $page] ?? throw new RuntimeException(sprintf('Missing popularity page %d.', $page));
            $data = $this->decodeObject($body, 'popular package response');
            $packages = $data['packages'] ?? null;
            if (!is_array($packages)) {
                throw new RuntimeException(sprintf('Popularity page %d has no package list.', $page));
            }
            foreach ($packages as $package) {
                if (++$popularRank > $config->popularPackageCount) {
                    break 2;
                }
                if (!is_array($package) || !isset($package['name']) || !is_string($package['name']) || '' === $package['name']) {
                    throw new RuntimeException(sprintf('Popularity page %d contains an invalid package.', $page));
                }
                $discoveries = $this->addDiscoverySource($discoveries, $package['name'], [
                    'stratum' => 'popular',
                    'tag' => null,
                    'rank' => $popularRank,
                    'role' => null,
                ]);
            }
        }

        foreach (['focused' => $config->focusedTags, 'noisy' => $config->noisyTags] as $stratum => $tags) {
            foreach ($tags as $tag) {
                $body = $responses[$stratum . ':' . $tag] ?? throw new RuntimeException(sprintf('Missing %s tag response for %s.', $stratum, $tag));
                $data = $this->decodeObject($body, sprintf('%s tag response', $stratum));
                $results = $data['results'] ?? null;
                if (!is_array($results)) {
                    throw new RuntimeException(sprintf('Tag %s has no result list.', $tag));
                }
                $rank = 0;
                foreach ($results as $result) {
                    if (++$rank > $config->searchResultsPerTag) {
                        break;
                    }
                    if (!is_array($result) || !isset($result['name']) || !is_string($result['name']) || '' === $result['name']) {
                        throw new RuntimeException(sprintf('Tag %s contains an invalid package.', $tag));
                    }
                    $discoveries = $this->addDiscoverySource($discoveries, $result['name'], [
                        'stratum' => $stratum,
                        'tag' => $tag,
                        'rank' => $rank,
                        'role' => null,
                    ]);
                }
            }
        }

        ksort($discoveries);
        foreach ($discoveries as &$discovery) {
            usort($discovery['sources'], fn (array $left, array $right): int => $this->compareSources($left, $right));
        }
        unset($discovery);

        return array_values($discoveries);
    }

    /** @return list<DiscoveryRecord> */
    private function seedDiscoveries(Config $config): array
    {
        return array_values($this->indexedSeedDiscoveries($config));
    }

    /** @return array<string, DiscoveryRecord> */
    private function indexedSeedDiscoveries(Config $config): array
    {
        /** @var array<string, DiscoveryRecord> $discoveries */
        $discoveries = [];
        $seedRank = 0;
        foreach ($config->seeds as $seed) {
            ++$seedRank;
            $discoveries = $this->addDiscoverySource($discoveries, $seed['package'], [
                'stratum' => 'curated',
                'tag' => null,
                'rank' => $seedRank,
                'role' => $seed['role'],
            ]);
        }

        return $discoveries;
    }

    /**
     * @param list<DiscoveryRecord> $discoveries
     * @return list<PackageRecord>
     */
    public function resolvePackages(array $discoveries): array
    {
        $urls = [];
        foreach ($discoveries as $discovery) {
            $urls[$discovery['name']] = $this->p2Url($discovery['name']);
        }
        $responses = $this->http->fetchMany($urls, $this->cacheDirectory, $this->offline, true);

        $packages = [];
        foreach ($discoveries as $discovery) {
            $body = $responses[$discovery['name']] ?? null;
            if (null === $body) {
                continue;
            }
            $package = $this->releaseFromP2($discovery, $body);
            if (null !== $package) {
                $packages[] = $package;
            }
        }

        return $packages;
    }

    /**
     * @param DiscoveryRecord $discovery
     * @return PackageRecord|null
     */
    public function releaseFromP2(array $discovery, string $body): ?array
    {
        $data = $this->decodeObject($body, sprintf('package metadata for %s', $discovery['name']));
        $packages = $data['packages'] ?? null;
        if (!is_array($packages)) {
            return null;
        }
        $versions = $packages[$discovery['name']] ?? null;
        if (!is_array($versions) || [] === $versions) {
            return null;
        }

        $expanded = MetadataMinifier::expand($this->versionList($versions));
        /** @var list<EligibleRelease> $eligible */
        $eligible = [];
        $curated = $this->hasStratum($discovery['sources'], 'curated');
        foreach ($expanded as $version) {
            $candidate = $this->eligibleRelease($version, $curated);
            if (null !== $candidate) {
                $eligible[] = $candidate;
            }
        }
        if ([] === $eligible) {
            return null;
        }

        usort($eligible, static function (array $left, array $right): int {
            return version_compare($right['versionNormalized'], $left['versionNormalized']);
        });
        $release = $eligible[0];
        $vendor = explode('/', $discovery['name'], 2)[0];
        $repository = $this->normalizer->normalize($release['sourceUrl'], $vendor);

        return [
            'name' => $discovery['name'],
            'repositoryKey' => $repository['key'],
            'repositoryUrl' => $repository['url'],
            'owner' => $repository['owner'],
            'version' => $release['version'],
            'versionNormalized' => $release['versionNormalized'],
            'packageType' => $release['type'],
            'distUrl' => $release['distUrl'],
            'distType' => $release['distType'],
            'sources' => $discovery['sources'],
        ];
    }

    /**
     * @param list<string> $packageNames
     * @return array<string, StatsRecord>
     */
    public function fetchStats(array $packageNames): array
    {
        $urls = [];
        foreach ($packageNames as $packageName) {
            [$vendor, $package] = $this->splitPackageName($packageName);
            $urls[$packageName] = sprintf(
                'https://packagist.org/packages/%s/%s.json',
                rawurlencode($vendor),
                rawurlencode($package),
            );
        }
        $responses = $this->http->fetchMany($urls, $this->cacheDirectory, $this->offline);
        $stats = [];
        foreach ($responses as $packageName => $body) {
            $stats[$packageName] = $this->statsFromPackageBody($body);
        }

        return $stats;
    }

    /** @return StatsRecord */
    public function statsFromPackageBody(string $body): array
    {
        $data = $this->decodeObject($body, 'package detail response');
        $package = $data['package'] ?? null;
        if (!is_array($package)) {
            throw new RuntimeException('Package detail response has no package object.');
        }
        $downloads = isset($package['downloads']) && is_array($package['downloads']) ? $package['downloads'] : [];
        $abandoned = $package['abandoned'] ?? false;
        if (!is_bool($abandoned) && !is_string($abandoned)) {
            $abandoned = false;
        }

        return [
            'downloadsTotal' => $this->integer($downloads['total'] ?? 0),
            'downloadsMonthly' => $this->integer($downloads['monthly'] ?? 0),
            'downloadsDaily' => $this->integer($downloads['daily'] ?? 0),
            'dependents' => $this->integer($package['dependents'] ?? 0),
            'favers' => $this->integer($package['favers'] ?? 0),
            'abandoned' => $abandoned,
        ];
    }

    /**
     * @param array<string, DiscoveryRecord> $discoveries
     * @param SourceRecord $source
     * @return array<string, DiscoveryRecord>
     */
    private function addDiscoverySource(array $discoveries, string $packageName, array $source): array
    {
        if (!isset($discoveries[$packageName])) {
            $discoveries[$packageName] = ['name' => $packageName, 'sources' => [$source]];

            return $discoveries;
        }
        $discoveries[$packageName]['sources'][] = $source;

        return $discoveries;
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

    /** @param list<SourceRecord> $sources */
    private function hasStratum(array $sources, string $stratum): bool
    {
        foreach ($sources as $source) {
            if ($source['stratum'] === $stratum) {
                return true;
            }
        }

        return false;
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

    /** @return array<array-key, mixed> */
    private function decodeObject(string $body, string $description): array
    {
        try {
            $data = json_decode($body, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException(sprintf('Invalid %s: %s', $description, $exception->getMessage()), 0, $exception);
        }
        if (!is_array($data)) {
            throw new RuntimeException(sprintf('Invalid %s.', $description));
        }

        return $data;
    }

    /** @return EligibleRelease|null */
    private function eligibleRelease(mixed $version, bool $curated): ?array
    {
        if (!is_array($version)) {
            return null;
        }
        $pretty = $version['version'] ?? null;
        $normalized = $version['version_normalized'] ?? null;
        $type = $version['type'] ?? 'library';
        $source = $version['source'] ?? null;
        $dist = $version['dist'] ?? null;
        if (!is_string($pretty) || '' === $pretty || !is_string($normalized) || '' === $normalized) {
            return null;
        }
        if ('stable' !== VersionParser::parseStability($pretty)) {
            return null;
        }
        if (!is_string($type) || (!$curated && 'library' !== $type)) {
            return null;
        }
        if (!is_array($source) || !isset($source['url']) || !is_string($source['url']) || '' === $source['url']) {
            return null;
        }
        if (!is_array($dist) || !isset($dist['url'], $dist['type']) || !is_string($dist['url']) || !is_string($dist['type']) || '' === $dist['url']) {
            return null;
        }
        if ('zip' !== strtolower($dist['type'])) {
            return null;
        }

        return [
            'version' => $pretty,
            'versionNormalized' => $normalized,
            'type' => $type,
            'sourceUrl' => $source['url'],
            'distUrl' => $dist['url'],
            'distType' => $dist['type'],
        ];
    }

    private function p2Url(string $packageName): string
    {
        [$vendor, $package] = $this->splitPackageName($packageName);

        return sprintf('https://repo.packagist.org/p2/%s/%s.json', rawurlencode($vendor), rawurlencode($package));
    }

    /** @return array{non-empty-string, non-empty-string} */
    private function splitPackageName(string $packageName): array
    {
        $parts = explode('/', $packageName, 2);
        if (2 !== count($parts) || '' === $parts[0] || '' === $parts[1]) {
            throw new RuntimeException(sprintf('Invalid Composer package name: %s', $packageName));
        }

        /** @var non-empty-string $vendor */
        $vendor = $parts[0];
        /** @var non-empty-string $package */
        $package = $parts[1];

        return [$vendor, $package];
    }

    private function integer(mixed $value): int
    {
        return is_int($value) ? $value : (is_numeric($value) ? (int) $value : 0);
    }

    /** @return list<mixed> */
    private function versionList(mixed $versions): array
    {
        if (!is_array($versions) || !array_is_list($versions)) {
            throw new RuntimeException('Package version metadata must be a list.');
        }

        return $versions;
    }
}
