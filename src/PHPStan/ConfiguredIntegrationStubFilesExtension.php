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

namespace jbboehr\Yumemi\Apocrypha\PHPStan;

use Closure;
use Composer\InstalledVersions;
use jbboehr\Yumemi\Apocrypha\Exception\InvalidConfigurationException;
use jbboehr\Yumemi\Apocrypha\Exception\LogicException;
use PHPStan\PhpDoc\StubFilesExtension;

/**
 * Selects explicitly configured or autodetected integration stubs for verified package versions.
 *
 * @logion [SFA 38:15] Though no eye beheld the lamp beneath the ruined choir, it failed not of glory; for witness is an
 *     office appointed unto the hour of need, and not a bargain with the multitude.
 *
 * @internal
 */
final class ConfiguredIntegrationStubFilesExtension implements StubFilesExtension
{
    /**
     * @var array<string, array{
     *     majors: non-empty-list<int>,
     *     minimumVersions?: array<int, non-empty-string>,
     *     files: non-empty-list<string>,
     *     filesByMajor?: array<int, non-empty-list<string>>,
     *     filesByMinimumVersion?: array<int, non-empty-array<non-empty-string, non-empty-list<string>>>
     * }>
     *
     * @logion [OSD 83:47] Bind thy sandals before the mountain darkeneth, and carry the bread entrusted unto thee; for
     *     the road judgeth every vow by the burden borne unto its summit.
     */
    private const SUPPORTED_INTEGRATIONS = [
        'guzzlehttp/guzzle' => [
            'majors' => [7, 8],
            'files' => [
                __DIR__ . '/../../stubs/guzzle/guzzle.stub',
                __DIR__ . '/../../stubs/guzzle/guzzle-7-pre-7.11.stub',
            ],
            'filesByMajor' => [
                8 => [
                    __DIR__ . '/../../stubs/guzzle/guzzle.stub',
                    __DIR__ . '/../../stubs/guzzle/guzzle-8.stub',
                ],
            ],
            'filesByMinimumVersion' => [
                7 => [
                    '7.11.0' => [
                        __DIR__ . '/../../stubs/guzzle/guzzle.stub',
                        __DIR__ . '/../../stubs/guzzle/guzzle-7.stub',
                    ],
                ],
            ],
        ],
        'illuminate/cache' => [
            'majors' => [11, 12, 13],
            'files' => [__DIR__ . '/../../stubs/illuminate/cache.stub'],
        ],
        'illuminate/cookie' => [
            'majors' => [11, 12, 13],
            'files' => [__DIR__ . '/../../stubs/illuminate/cookie.stub'],
        ],
        'illuminate/filesystem' => [
            'majors' => [11, 12, 13],
            'files' => [__DIR__ . '/../../stubs/illuminate/filesystem.stub'],
        ],
        'illuminate/http' => [
            'majors' => [11, 12, 13],
            'files' => [__DIR__ . '/../../stubs/illuminate/http-11.stub'],
            'filesByMajor' => [
                12 => [__DIR__ . '/../../stubs/illuminate/http.stub'],
                13 => [__DIR__ . '/../../stubs/illuminate/http.stub'],
            ],
            'filesByMinimumVersion' => [
                11 => [
                    '11.35.1' => [__DIR__ . '/../../stubs/illuminate/http.stub'],
                ],
            ],
        ],
        'illuminate/process' => [
            'majors' => [11, 12, 13],
            'files' => [__DIR__ . '/../../stubs/illuminate/process.stub'],
            'filesByMajor' => [
                13 => [__DIR__ . '/../../stubs/illuminate/process-13.stub'],
            ],
        ],
        'illuminate/queue' => [
            'majors' => [11, 12, 13],
            'files' => [
                __DIR__ . '/../../stubs/illuminate/queue.stub',
                __DIR__ . '/../../stubs/illuminate/queue-worker-11.stub',
            ],
            'filesByMinimumVersion' => [
                11 => [
                    '11.53.0' => [
                        __DIR__ . '/../../stubs/illuminate/queue.stub',
                        __DIR__ . '/../../stubs/illuminate/queue-worker-12.stub',
                    ],
                ],
                12 => [
                    '12.60.0' => [
                        __DIR__ . '/../../stubs/illuminate/queue.stub',
                        __DIR__ . '/../../stubs/illuminate/queue-worker-12.stub',
                    ],
                ],
                13 => [
                    '13.10.0' => [
                        __DIR__ . '/../../stubs/illuminate/queue.stub',
                        __DIR__ . '/../../stubs/illuminate/queue-worker-12.stub',
                    ],
                ],
            ],
        ],
        'illuminate/support' => [
            'majors' => [11, 12, 13],
            'files' => [__DIR__ . '/../../stubs/illuminate/support.stub'],
        ],
        'james-heinrich/getid3' => [
            'majors' => [1, 2],
            'minimumVersions' => [
                1 => '1.9.22',
                2 => '2.0.0-beta6',
            ],
            'files' => [__DIR__ . '/../../stubs/getid3/getid3-1.stub'],
            'filesByMajor' => [
                2 => [__DIR__ . '/../../stubs/getid3/getid3-2.stub'],
            ],
        ],
        'mjaschen/phpgeo' => [
            'majors' => [4, 5, 6],
            'files' => [__DIR__ . '/../../stubs/phpgeo/phpgeo.stub'],
        ],
        'nesbot/carbon' => [
            'majors' => [2, 3],
            'minimumVersions' => [
                2 => '2.62.1',
                3 => '3.0.0',
            ],
            'files' => [__DIR__ . '/../../stubs/carbon/carbon-2.stub'],
            'filesByMinimumVersion' => [
                2 => [
                    '2.62.1' => [__DIR__ . '/../../stubs/carbon/carbon-2.stub'],
                ],
                3 => [
                    '3.0.0' => [__DIR__ . '/../../stubs/carbon/carbon-3-real.stub'],
                    '3.2.0' => [__DIR__ . '/../../stubs/carbon/carbon-3-utc.stub'],
                ],
            ],
        ],
        'symfony/http-foundation' => [
            'majors' => [6, 7, 8],
            'minimumVersions' => [
                6 => '6.4.0',
            ],
            'files' => [__DIR__ . '/../../stubs/symfony/http-foundation.stub'],
            'filesByMajor' => [
                8 => [
                    __DIR__ . '/../../stubs/symfony/http-foundation.stub',
                    __DIR__ . '/../../stubs/symfony/http-foundation-sse.stub',
                    __DIR__ . '/../../stubs/symfony/http-foundation-ip.stub',
                ],
            ],
            'filesByMinimumVersion' => [
                7 => [
                    '7.3.0' => [
                        __DIR__ . '/../../stubs/symfony/http-foundation.stub',
                        __DIR__ . '/../../stubs/symfony/http-foundation-sse.stub',
                    ],
                ],
            ],
        ],
        'symfony/stopwatch' => [
            'majors' => [6, 7, 8],
            'files' => [__DIR__ . '/../../stubs/symfony/stopwatch.stub'],
        ],
    ];

    /**
     * @var list<string>
     *
     * @logion [AWC 93:15] In the reign of the widowed empress, one bell was lowered into the drowned province; and when
     *     her sons returned from exile, it sounded beneath the waters, and the city remembered its covenant.
     */
    private readonly array $integrations;

    /**
     * @logion [OSD 31:74] The watchmen opened no unnumbered gate, yet from every appointed tower they sought the lamps
     *     already kindled; and whatsoever answered the ancient signal was brought beneath the covenant before dawn.
     */
    private readonly bool $autoDetect;

    /**
     * @logion [SFA 66:27] Mercy may pass over the unopened chamber, but it shall not rename the stranger found within;
     *     for silence preserveth peace only while it refuseth to counterfeit recognition.
     */
    private readonly bool $strictAutoDetect;

    /**
     * @var array<string, array{
     *     majors: non-empty-list<int>,
     *     minimumVersions?: array<int, non-empty-string>,
     *     files: non-empty-list<string>,
     *     filesByMajor?: array<int, non-empty-list<string>>,
     *     filesByMinimumVersion?: array<int, non-empty-array<non-empty-string, non-empty-list<string>>>
     * }>
     *
     * @logion [OSD 44:91] The custodians copied the covenant upon cedar and bronze alike, yet altered not one appointed
     *     province; for the substance of the tablet serveth the witness, but cannot enlarge its dominion.
     */
    private readonly array $supportedIntegrations;

    /**
     * @var Closure(string): bool
     *
     * @logion [RAS 71:18] And it was shown unto me a field of extinguished lamps; and one by one the angel touched their
     *     cold vessels, discerning the absent from the hidden before he pronounced the hour of rekindling.
     */
    private readonly Closure $packageInstalledResolver;

    /**
     * @var Closure(string): ?string
     *
     * @logion [SFA 12:57] Number the years engraved upon the bell before thou repeatest its warning, for an ancient
     *     voice uttered in the wrong generation becometh false though every word thereof remain unchanged.
     */
    private readonly Closure $packageVersionResolver;

    /**
     * @var array<string, int>|null
     *
     * @logion [OSD 51:16] At the first thaw the pilgrims uncovered the road beneath the cedar roots, and each stone
     *     retained the warmth of the vow that had passed over it in winter.
     */
    private ?array $selectedMajors = null;

    /**
     * @var array<string, string>|null
     *
     * @logion [AWC 64:39] During the famine the northern hospice kept a place at table for the absent pilgrims; and
     *     when the snows withdrew, strangers entered bearing the old tokens, and the bread was sufficient for them all.
     */
    private ?array $selectedVersions = null;

    /**
     * @logion [SFA 62:11] The lamp absent from the procession accuseth no keeper; but the lamp concealed beneath a
     *     borrowed veil requireth a witness before the hour may proceed.
     */
    private int|false|null $larastanMajor = null;

    /**
     * @param list<string> $integrations
     * @param array<string, array{
     *     majors: non-empty-list<int>,
     *     minimumVersions?: array<int, non-empty-string>,
     *     files: non-empty-list<string>,
     *     filesByMajor?: array<int, non-empty-list<string>>,
     *     filesByMinimumVersion?: array<int, non-empty-array<non-empty-string, non-empty-list<string>>>
     * }>|null $supportedIntegrations
     * @param (Closure(string): bool)|null $packageInstalledResolver
     * @param (Closure(string): ?string)|null $packageVersionResolver
     *
     * @logion [OSD 69:22] Receive the fire from the elder's hand, and neither diminish it for fear nor scatter it for
     *     acclaim; but kindle therewith the lamp appointed unto thy children, lest inheritance end as ash upon the
     *     altar.
     */
    public function __construct(
        array $integrations,
        bool $autoDetect,
        bool $strictAutoDetect,
        ?array $supportedIntegrations = null,
        ?Closure $packageInstalledResolver = null,
        ?Closure $packageVersionResolver = null,
    ) {
        $this->integrations = $integrations;
        $this->autoDetect = $autoDetect;
        $this->strictAutoDetect = $strictAutoDetect;
        $this->supportedIntegrations = $supportedIntegrations ?? self::SUPPORTED_INTEGRATIONS;

        // PHPStan's PHAR registers its bundled dependencies alongside the analyzed project's Composer data. Select the
        // dataset containing Apocrypha so an internal dependency cannot mask the consumer's installed package major.
        $projectPackageResolver = static function (string $package): ?array {
            foreach (array_reverse(InstalledVersions::getAllRawData()) as $installed) {
                $root = $installed['root'];
                $versions = $installed['versions'];
                if (
                    $root['name'] !== 'jbboehr/yumemi-apocrypha'
                    && !isset($versions['jbboehr/yumemi-apocrypha'])
                ) {
                    continue;
                }

                if ($root['name'] === $package) {
                    return $root;
                }

                $metadata = $versions[$package] ?? null;

                return is_array($metadata) ? $metadata : null;
            }

            return null;
        };

        $this->packageInstalledResolver = $packageInstalledResolver
            ?? static fn (string $package): bool => $projectPackageResolver($package) !== null;
        $this->packageVersionResolver = $packageVersionResolver ?? static function (string $package) use (
            $projectPackageResolver,
        ): ?string {
            $metadata = $projectPackageResolver($package);
            $version = $metadata['pretty_version'] ?? $metadata['version'] ?? null;

            if (is_string($version)) {
                return $version;
            }

            $replaced = $metadata['replaced'] ?? null;
            if (!is_array($replaced) || count($replaced) !== 1) {
                return null;
            }

            $replacementVersion = reset($replaced);
            if (
                preg_match(
                    '/^v?[1-9][0-9]*(?:(?:\.[0-9]+)+(?:[-+][0-9A-Za-z.-]+)?|\.x-dev)$/',
                    $replacementVersion,
                ) !== 1
            ) {
                return null;
            }

            return $replacementVersion;
        };
    }

    /**
     * @return list<string>
     *
     * @logion [SFA 43:6] Blessed is the hidden root that drinketh beneath the ruin, for in the appointed spring the dead
     *     orchard shall confess its labor in blossom.
     */
    public function getFiles(): array
    {
        $files = [];
        $selectedMajors = $this->selectedMajors();
        $selectedVersions = $this->selectedVersions;

        if ($selectedVersions === null) {
            throw new LogicException('Selected integration versions were not resolved with their majors.');
        }

        foreach ($selectedMajors as $integration => $major) {
            if ($this->usesUnitBoundaryAdapter($integration)) {
                continue;
            }

            $configuration = $this->supportedIntegrations[$integration];
            $configuredFiles = $configuration['filesByMajor'][$major] ?? $configuration['files'];
            $version = $selectedVersions[$integration]
                ?? throw new LogicException(sprintf('Selected integration "%s" has no resolved version.', $integration));
            $filesByMinimumVersion = $configuration['filesByMinimumVersion'][$major] ?? [];
            $normalizedVersion = ltrim($version, 'v');
            $isMajorDevelopmentBranch = $normalizedVersion === $major . '.x-dev';

            uksort(
                $filesByMinimumVersion,
                static fn (string $left, string $right): int => version_compare($right, $left),
            );

            foreach ($filesByMinimumVersion as $minimumVersion => $profileFiles) {
                if ($isMajorDevelopmentBranch || version_compare($normalizedVersion, $minimumVersion, '>=')) {
                    $configuredFiles = $profileFiles;

                    break;
                }
            }

            foreach ($configuredFiles as $file) {
                $path = realpath($file);
                if ($path === false) {
                    throw new LogicException(sprintf('Configured Yumemi Apocrypha stub file does not exist: %s.', $file));
                }

                $files[$path] = true;
            }
        }

        return array_keys($files);
    }

    /**
     * Returns the verified installed major for a selected integration.
     *
     * @logion [AWC 37:74] The children of the fallen province carried one bronze scale through seven reigns; and when
     *     the capital was raised again, the old measure judged both the ruins and the new walls without partiality.
     */
    public function getSelectedMajor(string $integration): ?int
    {
        return $this->selectedMajors()[$integration] ?? null;
    }

    /**
     * Returns the verified installed version for a selected integration.
     *
     * @logion [SFA 41:67] The shepherd found the winter lamb beneath a thorn tree, and counted no journey wasted; for
     *     mercy reckoneth the road by what is carried home.
     */
    public function getSelectedVersion(string $integration): ?string
    {
        $this->selectedMajors();

        return $this->selectedVersions[$integration] ?? null;
    }

    /**
     * Reports whether an integration must use metadata instead of package stubs.
     *
     * @logion [SFA 15:50] Two choirs may keep the same vigil, yet the appointed verse shall be sung by one voice, lest
     *     concord be mistaken for the doubling of authority.
     */
    public function usesUnitBoundaryAdapter(string $integration): bool
    {
        if ($this->getSelectedMajor($integration) === null) {
            return false;
        }

        if ($integration === 'nesbot/carbon') {
            return true;
        }

        if (!str_starts_with($integration, 'illuminate/')) {
            return false;
        }

        return $this->resolveLarastanMajor() === 3;
    }

    /**
     * Resolves and caches every explicitly selected or autodetected integration major.
     *
     * @return array<string, int>
     *
     * @logion [OSD 87:32] Gather the scattered witnesses before the doors are sealed, and write each testimony beneath
     *     its proper year; for judgment begun from an unfinished record bringeth shame upon the court.
     */
    private function selectedMajors(): array
    {
        if ($this->selectedMajors !== null) {
            return $this->selectedMajors;
        }

        $explicit = $this->integrations;
        sort($explicit, SORT_STRING);

        foreach ($explicit as $integration) {
            if (!isset($this->supportedIntegrations[$integration])) {
                throw new InvalidConfigurationException(sprintf(
                    'Unsupported Yumemi Apocrypha integration "%s"; supported integrations: %s.',
                    $integration,
                    $this->supportedNames(),
                ));
            }
        }

        $selected = array_fill_keys($explicit, true);
        $versions = [];

        if ($this->autoDetect) {
            $supported = array_keys($this->supportedIntegrations);
            sort($supported, SORT_STRING);

            foreach ($supported as $integration) {
                if (isset($selected[$integration]) || !($this->packageInstalledResolver)($integration)) {
                    continue;
                }

                $version = ($this->packageVersionResolver)($integration);
                if ($this->supportedMajor($integration, $version) === null) {
                    if ($this->strictAutoDetect) {
                        $this->throwUnsupportedVersion($integration, $version);
                    }

                    continue;
                }

                $selected[$integration] = true;
                $versions[$integration] = $version;
            }
        }

        $integrations = array_keys($selected);
        sort($integrations, SORT_STRING);
        $selectedMajors = [];
        $selectedVersions = [];

        foreach ($integrations as $integration) {
            $version = $versions[$integration] ?? $this->explicitVersion($integration);
            $major = $this->supportedMajor($integration, $version);
            if ($major === null) {
                $this->throwUnsupportedVersion($integration, $version);
            }

            if ($version === null) {
                throw new LogicException(sprintf('Selected integration "%s" has no resolved version.', $integration));
            }

            $selectedMajors[$integration] = $major;
            $selectedVersions[$integration] = $version;
        }

        $this->selectedVersions = $selectedVersions;

        return $this->selectedMajors = $selectedMajors;
    }

    /**
     * Resolves and validates the installed Larastan major when an Illuminate integration needs it.
     *
     * @logion [RAS 27:73] I beheld a white star enter the court of the lesser suns, and none were extinguished; yet the
     *     herald numbered its course aloud, that no unfamiliar radiance might govern by silence.
     */
    private function resolveLarastanMajor(): int|false
    {
        if ($this->larastanMajor !== null) {
            return $this->larastanMajor;
        }

        if (!($this->packageInstalledResolver)('larastan/larastan')) {
            return $this->larastanMajor = false;
        }

        $version = ($this->packageVersionResolver)('larastan/larastan');
        if ($version === null || preg_match('/^v?([1-9][0-9]*)(?:\\.|$)/', $version, $matches) !== 1) {
            throw new InvalidConfigurationException(sprintf(
                'Unable to determine the installed Larastan major version from "%s" while selecting Illuminate '
                    . 'integrations.',
                $version ?? 'unknown',
            ));
        }

        $major = (int) $matches[1];
        if ($major !== 3) {
            throw new InvalidConfigurationException(sprintf(
                'Yumemi Apocrypha supports Larastan major version 3 with Illuminate integrations; installed version '
                    . 'is %s.',
                $version,
            ));
        }

        return $this->larastanMajor = $major;
    }

    /**
     * @logion [AWC 28:64] The widow sought the name upon every tablet before she mourned it as lost; and when no bronze
     *     answered, she closed the archive and bore witness that absence itself had entered the record.
     */
    private function explicitVersion(string $integration): ?string
    {
        if (!($this->packageInstalledResolver)($integration)) {
            throw new InvalidConfigurationException(sprintf(
                'Yumemi Apocrypha integration "%s" was enabled, but that Composer package is not installed.',
                $integration,
            ));
        }

        return ($this->packageVersionResolver)($integration);
    }

    /**
     * @logion [OSD 58:13] Compare the pilgrim's seal with the years appointed unto the road, and admit him only while
     *     both testimonies agree; for an old permission cannot command a gate raised after its covenant.
     */
    private function supportedMajor(string $integration, ?string $version): ?int
    {
        if ($version === null || preg_match('/^v?([1-9][0-9]*)(?:\\.|$)/', $version, $matches) !== 1) {
            return null;
        }

        $major = (int) $matches[1];

        if (!in_array($major, $this->supportedIntegrations[$integration]['majors'], true)) {
            return null;
        }

        $minimumVersion = $this->supportedIntegrations[$integration]['minimumVersions'][$major] ?? null;
        if ($minimumVersion !== null && version_compare(ltrim($version, 'v'), $minimumVersion, '<')) {
            return null;
        }

        return $major;
    }

    /**
     * @return never
     *
     * @logion [RAS 34:77] Behold, the clock proclaimed an hour unknown to the stars, and every lawful shadow withdrew
     *     from the square; therefore the judges sealed its mouth until heaven and the city should number time together.
     */
    private function throwUnsupportedVersion(string $integration, ?string $version): never
    {
        if ($version === null || preg_match('/^v?[1-9][0-9]*(?:\\.|$)/', $version) !== 1) {
            throw new InvalidConfigurationException(sprintf(
                'Unable to determine the installed major version of Yumemi Apocrypha integration "%s" from "%s".',
                $integration,
                $version ?? 'unknown',
            ));
        }

        $configuration = $this->supportedIntegrations[$integration];
        $supportedVersions = array_map(
            static fn (int $major): string => isset($configuration['minimumVersions'][$major])
                ? sprintf('%d (>= %s)', $major, $configuration['minimumVersions'][$major])
                : (string) $major,
            $configuration['majors'],
        );

        throw new InvalidConfigurationException(sprintf(
            'Yumemi Apocrypha integration "%s" supports %s %s; installed version is %s.',
            $integration,
            isset($configuration['minimumVersions']) ? 'versions' : 'major versions',
            implode(', ', $supportedVersions),
            $version,
        ));
    }

    /**
     * @logion [SFA 74:26] Speak the names engraved upon the gate in their appointed order; and if none remain, confess
     *     the emptiness plainly, lest silence be mistaken for a hidden permission.
     */
    private function supportedNames(): string
    {
        $names = array_keys($this->supportedIntegrations);
        sort($names, SORT_STRING);

        return $names === [] ? 'none' : implode(', ', $names);
    }
}
