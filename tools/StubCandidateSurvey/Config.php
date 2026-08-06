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

use JsonException;
use RuntimeException;

final class Config
{
    public const HARD_REPOSITORY_LIMIT = 250;

    /**
     * @param array{curated: int, focused: int, noisy: int, popular: int} $quotas
     * @param array{general: int, noisy: int} $ownerLimits
     * @param array{concurrency: int, retries: int} $http
     * @param array{compressedBytes: int, uncompressedBytes: int, entries: int, textFileBytes: int, totalDownloadBytes: int} $archiveLimits
     * @param list<non-empty-string> $focusedTags
     * @param list<non-empty-string> $noisyTags
     * @param list<array{package: non-empty-string, role: non-empty-string}> $seeds
     */
    private function __construct(
        public readonly int $schemaVersion,
        public readonly int $hardRepositoryLimit,
        public readonly int $popularPackageCount,
        public readonly int $searchResultsPerTag,
        public readonly array $quotas,
        public readonly array $ownerLimits,
        public readonly array $http,
        public readonly array $archiveLimits,
        public readonly array $focusedTags,
        public readonly array $noisyTags,
        public readonly array $seeds,
    ) {
    }

    public static function fromFile(string $path): self
    {
        $contents = file_get_contents($path);
        if (false === $contents) {
            throw new RuntimeException(sprintf('Unable to read survey configuration: %s', $path));
        }

        try {
            $data = json_decode($contents, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException(sprintf('Invalid survey configuration: %s', $exception->getMessage()), 0, $exception);
        }

        if (!is_array($data)) {
            throw new RuntimeException('Survey configuration must decode to an object.');
        }

        /** @var array{
         *     schemaVersion: int,
         *     hardRepositoryLimit: int,
         *     popularPackageCount: int,
         *     searchResultsPerTag: int,
         *     quotas: array{curated: int, focused: int, noisy: int, popular: int},
         *     ownerLimits: array{general: int, noisy: int},
         *     http: array{concurrency: int, retries: int},
         *     archiveLimits: array{compressedBytes: int, uncompressedBytes: int, entries: int, textFileBytes: int, totalDownloadBytes: int},
         *     focusedTags: list<non-empty-string>,
         *     noisyTags: list<non-empty-string>,
         *     seeds: list<array{package: non-empty-string, role: non-empty-string}>
         * } $data
         */
        $config = new self(
            $data['schemaVersion'],
            $data['hardRepositoryLimit'],
            $data['popularPackageCount'],
            $data['searchResultsPerTag'],
            $data['quotas'],
            $data['ownerLimits'],
            $data['http'],
            $data['archiveLimits'],
            $data['focusedTags'],
            $data['noisyTags'],
            $data['seeds'],
        );
        $config->validate();

        return $config;
    }

    /** @return array{curated: int, focused: int, noisy: int, popular: int} */
    public function quotasForLimit(int $limit): array
    {
        if ($limit < 1 || $limit > self::HARD_REPOSITORY_LIMIT) {
            throw new RuntimeException(sprintf('Repository limit must be between 1 and %d.', self::HARD_REPOSITORY_LIMIT));
        }

        if ($limit === $this->hardRepositoryLimit) {
            return $this->quotas;
        }

        $weights = $this->quotas;
        $totalWeight = array_sum($weights);
        $scaled = ['curated' => 0, 'focused' => 0, 'noisy' => 0, 'popular' => 0];
        $remainders = [];

        foreach ($weights as $stratum => $weight) {
            $raw = $limit * $weight / $totalWeight;
            $scaled[$stratum] = (int) floor($raw);
            $remainders[$stratum] = $raw - $scaled[$stratum];
        }

        $scaled['curated'] = max(1, $scaled['curated']);
        if ($limit >= 2) {
            $scaled['focused'] = max(1, $scaled['focused']);
        }
        if ($limit >= 3) {
            $scaled['popular'] = max(1, $scaled['popular']);
        }
        if ($limit >= 4) {
            $scaled['noisy'] = max(1, $scaled['noisy']);
        }

        while (array_sum($scaled) > $limit) {
            foreach (['popular', 'focused', 'noisy', 'curated'] as $stratum) {
                $minimum = match ($stratum) {
                    'curated' => 1,
                    'focused' => $limit >= 2 ? 1 : 0,
                    'popular' => $limit >= 3 ? 1 : 0,
                    'noisy' => $limit >= 4 ? 1 : 0,
                };
                if ($scaled[$stratum] > $minimum) {
                    --$scaled[$stratum];
                    continue 2;
                }
            }
        }

        arsort($remainders);
        while (array_sum($scaled) < $limit) {
            foreach (array_keys($remainders) as $stratum) {
                ++$scaled[$stratum];
                if (array_sum($scaled) === $limit) {
                    break 2;
                }
            }
        }

        return $scaled;
    }

    private function validate(): void
    {
        if (1 !== $this->schemaVersion) {
            throw new RuntimeException(sprintf('Unsupported survey configuration schema: %d', $this->schemaVersion));
        }
        if (self::HARD_REPOSITORY_LIMIT !== $this->hardRepositoryLimit) {
            throw new RuntimeException(sprintf('The repository hard limit must remain %d.', self::HARD_REPOSITORY_LIMIT));
        }
        if ($this->hardRepositoryLimit !== array_sum($this->quotas)) {
            throw new RuntimeException('Survey quotas must add up to the repository hard limit.');
        }
        if ([] !== array_intersect($this->focusedTags, $this->noisyTags)) {
            throw new RuntimeException('Focused and noisy tag lists must not overlap.');
        }
        if ($this->http['concurrency'] < 1 || $this->http['concurrency'] > 10) {
            throw new RuntimeException('HTTP concurrency must be between 1 and 10.');
        }
    }
}
