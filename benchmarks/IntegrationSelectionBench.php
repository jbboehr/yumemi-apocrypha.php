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

use jbboehr\Yumemi\Apocrypha\PHPStan\ConfiguredIntegrationStubFilesExtension;
use PhpBench\Attributes as Bench;

#[Bench\Iterations(5)]
#[Bench\Warmup(2)]
#[Bench\Groups(['selection'])]
final class IntegrationSelectionBench
{
    /**
     * Deliberately pinned so results remain comparable when the supported integration matrix changes.
     *
     * @var array<non-empty-string, non-empty-string>
     */
    private const PINNED_VERSIONS = [
        'guzzlehttp/guzzle' => '8.0.0',
        'illuminate/cache' => '13.10.0',
        'illuminate/cookie' => '13.10.0',
        'illuminate/filesystem' => '13.10.0',
        'illuminate/http' => '13.10.0',
        'illuminate/process' => '13.10.0',
        'illuminate/queue' => '13.10.0',
        'illuminate/support' => '13.10.0',
        'james-heinrich/getid3' => '2.0.0-beta6',
        'mjaschen/phpgeo' => '6.0.0',
        'nesbot/carbon' => '3.2.0',
        'nmarfurt/measurements' => '1.4.0',
        'symfony/http-foundation' => '8.0.0',
        'symfony/stopwatch' => '8.0.0',
    ];

    /** @var list<non-empty-string> */
    private const PINNED_INTEGRATIONS = [
        'guzzlehttp/guzzle',
        'illuminate/cache',
        'illuminate/cookie',
        'illuminate/filesystem',
        'illuminate/http',
        'illuminate/process',
        'illuminate/queue',
        'illuminate/support',
        'james-heinrich/getid3',
        'mjaschen/phpgeo',
        'nesbot/carbon',
        'nmarfurt/measurements',
        'symfony/http-foundation',
        'symfony/stopwatch',
    ];

    private ConfiguredIntegrationStubFilesExtension $cachedAdapterSelection;

    public function setUpCachedAdapterSelection(): void
    {
        $this->cachedAdapterSelection = self::selection(
            ['illuminate/cache', 'illuminate/queue'],
            false,
            true,
        );
        $this->cachedAdapterSelection->getFiles();
    }

    /** @return list<string> */
    #[Bench\Revs(10)]
    public function benchColdExplicitStubSelection(): array
    {
        return self::selection(self::PINNED_INTEGRATIONS, false, false)->getFiles();
    }

    /** @return list<string> */
    #[Bench\Revs(10)]
    public function benchColdAutodetectedSelection(): array
    {
        return self::selection([], true, true)->getFiles();
    }

    #[Bench\BeforeMethods('setUpCachedAdapterSelection')]
    #[Bench\Revs(1000)]
    public function benchCachedSelectedMajorLookup(): ?int
    {
        return $this->cachedAdapterSelection->getSelectedMajor('illuminate/queue');
    }

    #[Bench\BeforeMethods('setUpCachedAdapterSelection')]
    #[Bench\Revs(1000)]
    public function benchCachedAdapterDecision(): bool
    {
        return $this->cachedAdapterSelection->usesUnitBoundaryAdapter('illuminate/queue');
    }

    /**
     * @param list<string> $integrations
     */
    private static function selection(
        array $integrations,
        bool $autoDetect,
        bool $withLarastan,
    ): ConfiguredIntegrationStubFilesExtension {
        return new ConfiguredIntegrationStubFilesExtension(
            $integrations,
            $autoDetect,
            true,
            packageInstalledResolver: static fn (string $package): bool => isset(self::PINNED_VERSIONS[$package])
                || ($withLarastan && $package === 'larastan/larastan'),
            packageVersionResolver: static fn (string $package): ?string => self::PINNED_VERSIONS[$package]
                ?? ($withLarastan && $package === 'larastan/larastan' ? '3.0.0' : null),
        );
    }
}
