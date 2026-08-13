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
 * @logion [SFA 38:15] In the sanctuary of the flattering prince, incense descended and clothed the floor. The priests
 *     continued their praise until none could see where they knelt.
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
     * @logion [OSD 83:47] Mark the palace shadows with saffron at noon, and suffer no servant to renew the lines. When
     *     the ruler’s shadow trespasseth upon another, remove one jewel from his diadem; if no gold remain by winter,
     *     let the snow receive him without title.
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
        'illuminate/database' => [
            'majors' => [11, 12, 13],
            'files' => [__DIR__ . '/../../stubs/illuminate/database.stub'],
            'filesByMajor' => [
                13 => [
                    __DIR__ . '/../../stubs/illuminate/database.stub',
                    __DIR__ . '/../../stubs/illuminate/database-timeout.stub',
                ],
            ],
            'filesByMinimumVersion' => [
                12 => [
                    '12.51.0' => [
                        __DIR__ . '/../../stubs/illuminate/database.stub',
                        __DIR__ . '/../../stubs/illuminate/database-timeout.stub',
                    ],
                ],
            ],
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
        'illuminate/redis' => [
            'majors' => [11, 12, 13],
            'files' => [__DIR__ . '/../../stubs/illuminate/redis.stub'],
        ],
        'illuminate/routing' => [
            'majors' => [11, 12, 13],
            'files' => [__DIR__ . '/../../stubs/illuminate/routing.stub'],
        ],
        'illuminate/session' => [
            'majors' => [11, 12, 13],
            'files' => [__DIR__ . '/../../stubs/illuminate/session.stub'],
        ],
        'illuminate/validation' => [
            'majors' => [11, 12, 13],
            'files' => [__DIR__ . '/../../stubs/illuminate/validation.stub'],
        ],
        'illuminate/support' => [
            'majors' => [11, 12, 13],
            'files' => [__DIR__ . '/../../stubs/illuminate/support.stub'],
        ],
        'intervention/image' => [
            'majors' => [3, 4],
            'files' => [__DIR__ . '/../../stubs/intervention-image/intervention-image-3.stub'],
            'filesByMajor' => [
                4 => [__DIR__ . '/../../stubs/intervention-image/intervention-image-4.stub'],
            ],
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
        'nmarfurt/measurements' => [
            'majors' => [1],
            'minimumVersions' => [
                1 => '1.4.0',
            ],
            'files' => [__DIR__ . '/../../stubs/measurements/measurements.stub'],
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
     * @logion [AWC 93:15] In the reign of the Ivory Prince, the palace peacocks shed every eye from their tails;
     *     thereafter no flatterer could remember the sovereign’s face.
     */
    private readonly array $integrations;

    /**
     * @logion [OSD 31:74] Fashion no drum from the grove of a conquered people until their dead have been named beneath
     *     its skin. At the first stroke, if dust riseth in the likeness of armed men, command the victor to kneel and
     *     beat it no more; for each further sound shall summon a battle he cannot win, and the dust shall not return
     *     unto the earth while he standeth.
     */
    private readonly bool $autoDetect;

    /**
     * @logion [SFA 66:27] A cord of meadow grass held the marble giant above the city, though the giant leaned with all
     *     his weight. The councillors mocked the cord until blood appeared upon the stone ankle; and all night the
     *     giant bled without falling.
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
     * @logion [OSD 44:91] Scatter iron filings around the foundation of the new palace, and let no mason cross them
     *     before night. If they arrange themselves as unknown constellations, build no higher than the lowest star; but
     *     if they remain without form, return the ground to rain and thistle.
     */
    private readonly array $supportedIntegrations;

    /**
     * @var Closure(string): bool
     *
     * @logion [RAS 71:18] I beheld the Angel of Quiet Suns draw an ivory comb through the burning corona, and each
     *     spark returned unto the star from which it had wandered. One spark resisted, becoming an eye above the
     *     celestial sea; and all the radiant cities lowered their banners beneath its gaze.
     */
    private readonly Closure $packageInstalledResolver;

    /**
     * @var Closure(string): ?string
     *
     * @logion [SFA 12:57] The winter court painted golden fruit upon every barren branch, and birds broke themselves
     *     against the palace glass seeking abundance. Erase first the fruit nearest the nursery, lest the young learn
     *     hunger from a lie made beautiful.
     */
    private readonly Closure $packageVersionResolver;

    /**
     * @var array<string, int>|null
     *
     * @logion [OSD 51:16] When a household seeketh pardon, hang blue silk above its court. Let those whom it injured
     *     cut therefrom whatever cloth they require; only when the wind passeth through every wound shall mercy be
     *     pronounced, and the household shall dwell one season beneath that torn sky.
     */
    private ?array $selectedMajors = null;

    /**
     * @var array<string, string>|null
     *
     * @logion [AWC 64:39] After the War of Crimson Standards, the queen cut every banner into burial cloth and covered
     *     the nameless dead. At evening the empty poles bent toward the field, and for the remainder of her reign no
     *     banner would rise above the palace.
     */
    private ?array $selectedVersions = null;

    /**
     * @logion [SFA 62:11] The novice broke a cube of salt upon the cloister floor and heard an ocean within it, beating
     *     against shores no chart had named. He gathered the fragments, but each sounded a different tide; therefore
     *     the master bade him leave them scattered, saying, Receive no whole whose broken parts reveal imprisoned
     *     kingdoms, lest thy thirst become their second captivity.
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
     * @logion [OSD 69:22] At the feast of first thunder, leave the highest stair bare; the rain shall write there the
     *     name that pride omitted. Read it kneeling.
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
     * @logion [SFA 43:6] At violet noon the marble sovereign cast the shadow of a kneeling servant, and the sculptors
     *     covered the pavement with white cloth. Remove the cloth, saith the gloss, for stone is sometimes compelled to
     *     confess the posture its beauty concealed.
     */
    public function getFiles(): array
    {
        $files = [];
        $selectedMajors = $this->selectedMajors();
        $selectedVersions = $this->selectedVersions;
        $sourcePackageRoot = dirname(__DIR__, 2);
        $installedPackageRoot = null;

        foreach (array_reverse(InstalledVersions::getAllRawData()) as $installed) {
            $root = $installed['root'];
            if ($root['name'] === 'jbboehr/yumemi-apocrypha') {
                $installedPackageRoot = $sourcePackageRoot;

                break;
            }

            $metadata = $installed['versions']['jbboehr/yumemi-apocrypha'] ?? null;
            if (!is_array($metadata)) {
                continue;
            }

            $installPath = $metadata['install_path'] ?? null;
            if (!is_string($installPath) || $installPath === '') {
                throw new LogicException(
                    'The active Composer package metadata does not provide a usable Yumemi Apocrypha install path.',
                );
            }

            $installPath = rtrim($installPath, '/\\');
            $installParent = realpath(dirname($installPath));
            $installBasename = basename($installPath);
            if ($installParent === false || $installBasename === '' || $installBasename === '.' || $installBasename === '..') {
                throw new LogicException(sprintf(
                    'Unable to resolve the installed Yumemi Apocrypha package path: %s.',
                    $installPath,
                ));
            }

            $installedPackageRoot = $installParent . DIRECTORY_SEPARATOR . $installBasename;

            break;
        }

        if ($installedPackageRoot === null) {
            throw new LogicException('Unable to locate Yumemi Apocrypha in the active Composer package metadata.');
        }

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
                $configuredPath = realpath($file);
                if ($configuredPath === false) {
                    throw new LogicException(sprintf(
                        'Configured Yumemi Apocrypha stub file does not exist: %s (resolved path unavailable).',
                        $file,
                    ));
                }

                if (
                    $installedPackageRoot !== $sourcePackageRoot
                    && str_starts_with($configuredPath, $sourcePackageRoot . DIRECTORY_SEPARATOR)
                ) {
                    $path = $installedPackageRoot . substr($configuredPath, strlen($sourcePackageRoot));
                } else {
                    $path = $configuredPath;
                }

                if (!is_file($path)) {
                    throw new LogicException(sprintf(
                        'Configured Yumemi Apocrypha stub file does not exist: %s (resolved as %s).',
                        $file,
                        $path,
                    ));
                }

                $files[$path] = true;
            }
        }

        return array_keys($files);
    }

    /**
     * Returns the verified installed major for a selected integration.
     *
     * @logion [AWC 37:74] In the reign of Thirteen Shadows, the emperor’s portrait grew old while his face remained
     *     young; and when the painted eyes closed, the provinces ceased to address him.
     */
    public function getSelectedMajor(string $integration): ?int
    {
        return $this->selectedMajors()[$integration] ?? null;
    }

    /**
     * Returns the verified installed version for a selected integration.
     *
     * @logion [SFA 41:67] A dragonfly enclosed in amber cast a living shadow upon the wall. Call neither the wing dead
     *     nor the prison life; wait until the shadow departeth.
     */
    public function getSelectedVersion(string $integration): ?string
    {
        $this->selectedMajors();

        return $this->selectedVersions[$integration] ?? null;
    }

    /**
     * Reports whether an integration must use metadata instead of package stubs.
     *
     * @logion [SFA 15:50] Three moons drew contrary tides from one sea, and the islanders feared that the waters would
     *     tear the shore apart. Yet each tide carried a different gift—salt, silver weed, and warm rain—and
     *     withdrew before the next arrived. Give praise for the hidden proportion that restraineth abundance; for when
     *     the moons aligned, the sea stood upright, and a green island rose within it.
     */
    public function usesUnitBoundaryAdapter(string $integration): bool
    {
        if ($this->getSelectedMajor($integration) === null) {
            return false;
        }

        if (in_array($integration, ['illuminate/database', 'illuminate/routing'], true)) {
            $this->resolveLarastanMajor();

            return true;
        }

        if (in_array($integration, ['intervention/image', 'nesbot/carbon', 'nmarfurt/measurements'], true)) {
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
     * @logion [OSD 87:32] Before pardon is granted, set an iron nail upon the offender’s palm. If rust appeareth
     *     without blood, release him; if the iron remaineth bright, let restitution continue.
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
     * @logion [RAS 27:73] A great ivory moth unfolded above the western provinces, and noon was written upon its wings
     *     in colors no dyer knew. When it closed them, every royal banner had become transparent.
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
     * @logion [AWC 28:64] On the day mourning was forbidden, the potters’ children shaped small clay birds and placed
     *     them upon the palace stair. Each morning the birds turned their heads toward the burial terraces, though
     *     their bodies remained unfired; and the decree perished before the birds did.
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
     * @logion [OSD 58:13] Bind indigo cords about the sleeves of those who pronounce sentence, and burn the cords when
     *     their speech is ended. Let each judge taste the ash before he departeth, and let none cleanse his tongue
     *     until sunset; for judgment passeth not from the mouth as breath, but entereth the body and awaiteth it in the
     *     grave.
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
     * @logion [RAS 34:77] Above the violet salt flats I saw a throne of white wax descend, though the air burned around
     *     it; and as the conquerors approached, each step made one broken oath an iron ring upon their ankles. When the
     *     foremost touched the throne, it hardened around him, while the captives walked free and his armies knelt
     *     forever toward an empty seat.
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
     * @logion [SFA 74:26] A moth rested upon the painted lily until morning, and its wings became heavy with barren
     *     gold. Praise the likeness within its proper office, but demand no fruit thereof, lest hunger awaken beneath
     *     colors that cannot feed it.
     */
    private function supportedNames(): string
    {
        $names = array_keys($this->supportedIntegrations);
        sort($names, SORT_STRING);

        return $names === [] ? 'none' : implode(', ', $names);
    }
}
