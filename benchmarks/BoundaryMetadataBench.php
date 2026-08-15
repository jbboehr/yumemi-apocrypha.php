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

namespace jbboehr\Yumemi\Apocrypha\Benchmarks;

use jbboehr\Yumemi\Apocrypha\PHPStan\PackageIntegrationUnitBoundaryMetadata;
use LogicException;
use PhpBench\Attributes as Bench;

/** @phpstan-import-type IntegrationBoundaries from PackageIntegrationUnitBoundaryMetadata */
#[Bench\Iterations(5)]
#[Bench\Warmup(2)]
#[Bench\Groups(['metadata', 'version-profile'])]
final class BoundaryMetadataBench
{
    /**
     * Deliberately pinned so results remain comparable; every catalog integration still requires an explicit profile.
     *
     * @var array<non-empty-string, array{int, non-empty-string}>
     */
    private const PINNED_PROFILES = [
        'illuminate/auth' => [13, '13.25.0'],
        'illuminate/cache' => [13, '13.10.0'],
        'illuminate/cookie' => [13, '13.10.0'],
        'illuminate/database' => [13, '13.25.0'],
        'illuminate/filesystem' => [13, '13.10.0'],
        'illuminate/http' => [13, '13.10.0'],
        'illuminate/process' => [13, '13.10.0'],
        'illuminate/queue' => [13, '13.10.0'],
        'illuminate/redis' => [13, '13.25.0'],
        'illuminate/routing' => [13, '13.25.0'],
        'illuminate/session' => [13, '13.25.0'],
        'illuminate/support' => [13, '13.10.0'],
        'illuminate/validation' => [13, '13.25.0'],
        'intervention/image' => [4, '4.0.0'],
        'nesbot/carbon' => [3, '3.2.0'],
        'nmarfurt/measurements' => [1, '1.4.0'],
    ];

    /**
     * @var array{
     *     majors: non-empty-list<int>,
     *     minimumVersions: non-empty-array<int, non-empty-string>
     * }
     */
    private const MINIMUM_VERSION_BOUNDARY = [
        'majors' => [11, 12, 13],
        'minimumVersions' => [11 => '11.53.0', 12 => '12.60.0', 13 => '13.10.0'],
    ];

    /** @var array<non-empty-string, IntegrationBoundaries> */
    private array $catalog;

    public function setUpCatalog(): void
    {
        $this->catalog = PackageIntegrationUnitBoundaryMetadata::all();
        $missingProfiles = array_keys(array_diff_key($this->catalog, self::PINNED_PROFILES));
        $staleProfiles = array_keys(array_diff_key(self::PINNED_PROFILES, $this->catalog));

        if ($missingProfiles !== [] || $staleProfiles !== []) {
            throw new LogicException(sprintf(
                'Boundary metadata benchmark profiles are out of sync (missing: %s; stale: %s).',
                $missingProfiles === [] ? 'none' : implode(', ', $missingProfiles),
                $staleProfiles === [] ? 'none' : implode(', ', $staleProfiles),
            ));
        }
    }

    #[Bench\Revs(1000)]
    public function benchMatchingMinimumVersionProfile(): bool
    {
        return PackageIntegrationUnitBoundaryMetadata::supportsVersion(
            self::MINIMUM_VERSION_BOUNDARY,
            13,
            '13.10.0',
        );
    }

    #[Bench\Revs(1000)]
    public function benchRejectedMinimumVersionProfile(): bool
    {
        return PackageIntegrationUnitBoundaryMetadata::supportsVersion(
            self::MINIMUM_VERSION_BOUNDARY,
            13,
            '13.9.0',
        );
    }

    #[Bench\BeforeMethods('setUpCatalog')]
    #[Bench\Revs(10)]
    public function benchFullCatalogVersionFilter(): int
    {
        $supported = 0;

        foreach ($this->catalog as $integration => $metadata) {
            [$major, $version] = self::PINNED_PROFILES[$integration];

            foreach (['arguments', 'properties', 'returns'] as $kind) {
                foreach ($metadata[$kind] as $boundary) {
                    if (PackageIntegrationUnitBoundaryMetadata::supportsVersion($boundary, $major, $version)) {
                        ++$supported;
                    }
                }
            }
        }

        return $supported;
    }
}
