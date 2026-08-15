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

namespace jbboehr\Yumemi\Apocrypha\Tests\PHPStan;

use Composer\InstalledVersions;
use jbboehr\Yumemi\Apocrypha\Exception\ExceptionInterface;
use jbboehr\Yumemi\Apocrypha\Exception\InvalidConfigurationException;
use jbboehr\Yumemi\Apocrypha\Exception\LogicException;
use jbboehr\Yumemi\Apocrypha\PHPStan\ConfiguredIntegrationStubFilesExtension;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

final class ConfiguredIntegrationStubFilesExtensionTest extends TestCase
{
    public function testDefaultConfigurationDoesNotInspectComposerPackages(): void
    {
        $extension = $this->extension(
            installed: static function (): never {
                self::fail('The installed-package resolver must not run for the default configuration.');
            },
            version: static function (): never {
                self::fail('The version resolver must not run for the default configuration.');
            },
        );

        self::assertSame([], $extension->getFiles());
    }

    public function testEmptySupportedIntegrationRegistryIsReportedExplicitly(): void
    {
        $extension = $this->extension(
            integrations: ['vendor/missing'],
            supported: [],
        );

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage(
            'Unsupported Yumemi Apocrypha integration "vendor/missing"; supported integrations: none.',
        );

        $extension->getFiles();
    }

    /** @return iterable<string, array{string}> */
    public static function concreteReplacementVersions(): iterable
    {
        yield 'stable release' => ['12.4.0'];
        yield 'major development branch' => ['11.x-dev'];
    }

    #[DataProvider('concreteReplacementVersions')]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testDefaultVersionResolverUsesAConcreteReplacementVersion(string $version): void
    {
        $this->reloadInstalledPackageMetadata([
            'replaced' => [$version],
        ]);
        $extension = new ConfiguredIntegrationStubFilesExtension(['illuminate/cache'], false, true);

        self::assertSame([
            realpath(__DIR__ . '/../../stubs/illuminate/cache.stub'),
        ], $extension->getFiles());
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testMajorDevelopmentReplacementUsesTheLatestMinorVersionProfile(): void
    {
        $this->reloadInstalledPackageMetadata([
            'replaced' => ['11.x-dev'],
        ], 'illuminate/queue');
        $extension = new ConfiguredIntegrationStubFilesExtension(['illuminate/queue'], false, true);

        self::assertSame('11.x-dev', $extension->getSelectedVersion('illuminate/queue'));
        self::assertSame([
            realpath(__DIR__ . '/../../stubs/illuminate/queue.stub'),
            realpath(__DIR__ . '/../../stubs/illuminate/queue-worker-12.stub'),
        ], $extension->getFiles());
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testDefaultVersionResolverPrefersADirectVersionOverReplacementMetadata(): void
    {
        $this->reloadInstalledPackageMetadata([
            'pretty_version' => 'v12.4.0',
            'version' => '12.4.0.0',
            'replaced' => ['13.1.0'],
        ], 'illuminate/process');
        $extension = new ConfiguredIntegrationStubFilesExtension(['illuminate/process'], false, true);

        self::assertSame([
            realpath(__DIR__ . '/../../stubs/illuminate/process.stub'),
        ], $extension->getFiles());
        self::assertSame('v12.4.0', $extension->getSelectedVersion('illuminate/process'));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testStubFilesUseTheConsumerPackageInstallPathFromTheProjectDataset(): void
    {
        $temporaryPath = tempnam(sys_get_temp_dir(), 'yumemi-apocrypha-install-');
        self::assertNotFalse($temporaryPath);
        self::assertTrue(unlink($temporaryPath));
        $packageRoot = $temporaryPath . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'jbboehr'
            . DIRECTORY_SEPARATOR . 'yumemi-apocrypha';
        $stubRoot = $packageRoot . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'guzzle';
        $baseStub = $stubRoot . DIRECTORY_SEPARATOR . 'guzzle.stub';
        $majorStub = $stubRoot . DIRECTORY_SEPARATOR . 'guzzle-7.stub';
        self::assertTrue(mkdir($stubRoot, 0o777, true));
        self::assertTrue(copy(__DIR__ . '/../../stubs/guzzle/guzzle.stub', $baseStub));
        self::assertTrue(copy(__DIR__ . '/../../stubs/guzzle/guzzle-7.stub', $majorStub));

        try {
            $this->reloadInstalledPackageMetadata(
                [
                    'pretty_version' => '7.15.3',
                    'version' => '7.15.3.0',
                ],
                'guzzlehttp/guzzle',
                $packageRoot . DIRECTORY_SEPARATOR,
            );
            $extension = new ConfiguredIntegrationStubFilesExtension(['guzzlehttp/guzzle'], false, true);

            self::assertSame([
                $baseStub,
                $majorStub,
            ], $extension->getFiles());
        } finally {
            self::assertTrue(unlink($baseStub));
            self::assertTrue(unlink($majorStub));
            self::assertTrue(rmdir($stubRoot));
            self::assertTrue(rmdir(dirname($stubRoot)));
            self::assertTrue(rmdir(dirname($stubRoot, 2)));
            self::assertTrue(rmdir(dirname($stubRoot, 3)));
            self::assertTrue(rmdir(dirname($stubRoot, 4)));
            self::assertTrue(rmdir($temporaryPath));
        }
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testInvalidConsumerPackageInstallPathsAreReportedExplicitly(): void
    {
        $temporaryPath = tempnam(sys_get_temp_dir(), 'yumemi-apocrypha-invalid-install-');
        self::assertNotFalse($temporaryPath);
        self::assertTrue(unlink($temporaryPath));
        $metadataError =
            'The active Composer package metadata does not provide a usable Yumemi Apocrypha install path.';

        foreach ([false, ''] as $installPath) {
            $this->reloadInstalledPackageMetadata(
                ['pretty_version' => '7.15.3', 'version' => '7.15.3.0'],
                'guzzlehttp/guzzle',
                $installPath,
            );

            try {
                (new ConfiguredIntegrationStubFilesExtension(['guzzlehttp/guzzle'], false, true))->getFiles();
                self::fail('Invalid Composer package metadata was accepted.');
            } catch (LogicException $exception) {
                self::assertSame($metadataError, $exception->getMessage());
            }
        }

        foreach ([
            $temporaryPath . '/missing/yumemi-apocrypha',
            DIRECTORY_SEPARATOR,
            sys_get_temp_dir() . DIRECTORY_SEPARATOR . '.',
        ] as $installPath) {
            $this->reloadInstalledPackageMetadata(
                ['pretty_version' => '7.15.3', 'version' => '7.15.3.0'],
                'guzzlehttp/guzzle',
                $installPath,
            );

            try {
                (new ConfiguredIntegrationStubFilesExtension(['guzzlehttp/guzzle'], false, true))->getFiles();
                self::fail('An invalid Composer package install path was accepted.');
            } catch (LogicException $exception) {
                self::assertStringStartsWith(
                    'Unable to resolve the installed Yumemi Apocrypha package path:',
                    $exception->getMessage(),
                );
            }
        }
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testConsumerInstallPathDoesNotRebaseConfiguredFilesOutsideThePackage(): void
    {
        $temporaryPath = tempnam(sys_get_temp_dir(), 'yumemi-apocrypha-external-stub-');
        self::assertNotFalse($temporaryPath);
        self::assertTrue(unlink($temporaryPath));
        $packageRoot = $temporaryPath . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'jbboehr'
            . DIRECTORY_SEPARATOR . 'yumemi-apocrypha';
        self::assertTrue(mkdir($packageRoot, 0o777, true));
        $externalStub = $temporaryPath . DIRECTORY_SEPARATOR . 'external.stub';
        self::assertNotFalse(file_put_contents($externalStub, "<?php\n"));

        try {
            $this->reloadInstalledPackageMetadata(
                ['pretty_version' => '12.0.0', 'version' => '12.0.0.0'],
                'vendor/one',
                $packageRoot,
            );
            $extension = $this->extension(
                integrations: ['vendor/one'],
                supported: [
                    'vendor/one' => [
                        'majors' => [12],
                        'files' => [$externalStub],
                    ],
                ],
                installed: static fn (): bool => true,
                version: static fn (): string => '12.0.0',
            );

            self::assertSame([$externalStub], $extension->getFiles());
        } finally {
            self::assertTrue(unlink($externalStub));
            self::assertTrue(rmdir($packageRoot));
            self::assertTrue(rmdir(dirname($packageRoot)));
            self::assertTrue(rmdir(dirname($packageRoot, 2)));
            self::assertTrue(rmdir($temporaryPath));
        }
    }

    public function testIntegrationAndLarastanSelectionsAreCached(): void
    {
        $installedPackages = [];
        $versionedPackages = [];
        $extension = new ConfiguredIntegrationStubFilesExtension(
            ['illuminate/cache'],
            false,
            true,
            [
                'illuminate/cache' => [
                    'majors' => [11, 12, 13],
                    'files' => [__DIR__ . '/../../stubs/illuminate/cache.stub'],
                ],
            ],
            static function (string $package) use (&$installedPackages): bool {
                $installedPackages[] = $package;

                return true;
            },
            static function (string $package) use (&$versionedPackages): string {
                $versionedPackages[] = $package;

                return $package === 'larastan/larastan' ? '3.10.0' : '12.4.0';
            },
        );

        self::assertSame([], $extension->getFiles());
        self::assertSame(12, $extension->getSelectedMajor('illuminate/cache'));
        self::assertSame('12.4.0', $extension->getSelectedVersion('illuminate/cache'));
        self::assertTrue($extension->usesUnitBoundaryAdapter('illuminate/cache'));
        self::assertSame(['illuminate/cache', 'larastan/larastan'], $installedPackages);
        self::assertSame(['illuminate/cache', 'larastan/larastan'], $versionedPackages);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testDefaultVersionResolverStillRejectsAnUnsupportedConcreteReplacementVersion(): void
    {
        $this->reloadInstalledPackageMetadata([
            'replaced' => ['14.1.0'],
        ]);
        $extension = new ConfiguredIntegrationStubFilesExtension(['illuminate/cache'], false, true);

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage(
            'Yumemi Apocrypha integration "illuminate/cache" supports major versions 11, 12, 13; '
                . 'installed version is 14.1.0.',
        );

        $extension->getFiles();
    }

    /**
     * @return iterable<string, array{array<string, mixed>}>
     */
    public static function invalidReplacementMetadata(): iterable
    {
        yield 'replacement range' => [[
            'replaced' => ['^12.0'],
        ]];
        yield 'compound replacement constraint' => [[
            'replaced' => ['12.4.0 || 13.1.0'],
        ]];
        yield 'multiple replacement versions' => [[
            'replaced' => ['12.4.0', '13.1.0'],
        ]];
        yield 'provided version' => [[
            'provided' => ['12.4.0'],
        ]];
    }

    /** @param array{pretty_version?: string, version?: string, replaced?: list<string>, provided?: list<string>} $metadata */
    #[DataProvider('invalidReplacementMetadata')]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testDefaultVersionResolverRejectsUncertainReplacementMetadata(array $metadata): void
    {
        $this->reloadInstalledPackageMetadata($metadata);
        $extension = new ConfiguredIntegrationStubFilesExtension(['illuminate/cache'], false, true);

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage(
            'Unable to determine the installed major version of Yumemi Apocrypha integration "illuminate/cache" '
                . 'from "unknown".',
        );

        $extension->getFiles();
    }

    public function testExplicitIntegrationsAreSortedAndDeduplicated(): void
    {
        $resolved = [];
        $extension = $this->extension(
            integrations: ['vendor/two', 'vendor/one', 'vendor/two'],
            installed: static fn (): bool => true,
            version: static function (string $package) use (&$resolved): string {
                $resolved[] = $package;

                return '12.4.0';
            },
        );

        self::assertSame([
            realpath(__DIR__ . '/../../apocrypha.neon'),
            realpath(__DIR__ . '/../../extension.neon'),
        ], $extension->getFiles());
        self::assertSame(['vendor/one', 'vendor/two'], $resolved);
    }

    public function testAutodetectionAddsInstalledSupportedIntegrations(): void
    {
        $extension = $this->extension(
            autoDetect: true,
            installed: static fn (string $package): bool => $package === 'vendor/two',
            version: static fn (): string => 'v13.2.1',
        );

        self::assertSame([realpath(__DIR__ . '/../../extension.neon')], $extension->getFiles());
    }

    public function testAutodetectionInspectsSupportedIntegrationsInStableOrder(): void
    {
        $resolved = [];
        $extension = $this->extension(
            autoDetect: true,
            supported: array_reverse($this->supported(), true),
            installed: static fn (): bool => true,
            version: static function (string $package) use (&$resolved): string {
                $resolved[] = $package;

                return '12.0.0';
            },
        );

        $extension->getFiles();

        self::assertSame(['vendor/one', 'vendor/two'], $resolved);
    }

    public function testExplicitAndAutodetectedIntegrationsFormAUnion(): void
    {
        $extension = $this->extension(
            integrations: ['vendor/two'],
            autoDetect: true,
            installed: static fn (): bool => true,
            version: static fn (): string => '11.x-dev',
        );

        self::assertSame([
            realpath(__DIR__ . '/../../apocrypha.neon'),
            realpath(__DIR__ . '/../../extension.neon'),
        ], $extension->getFiles());
    }

    public function testUnknownExplicitIntegrationsAreValidatedInStableOrder(): void
    {
        $extension = $this->extension(
            integrations: ['vendor/zeta', 'vendor/alpha'],
            installed: static function (): never {
                self::fail('Unknown integrations must be rejected before package inspection.');
            },
        );

        $this->expectExceptionMessage('Unsupported Yumemi Apocrypha integration "vendor/alpha";');

        $extension->getFiles();
    }

    public function testSupportedNamesInConfigurationErrorAreSorted(): void
    {
        $supported = $this->supported();
        $extension = $this->extension(
            integrations: ['vendor/unknown'],
            supported: array_reverse($supported, true),
        );

        $this->expectExceptionMessage('supported integrations: vendor/one, vendor/two.');

        $extension->getFiles();
    }

    public function testUnknownExplicitIntegrationIsRejectedBeforePackageInspection(): void
    {
        $extension = $this->extension(
            integrations: ['vendor/unknown'],
            installed: static function (): never {
                self::fail('Unknown integrations must be rejected before package inspection.');
            },
            version: static function (): never {
                self::fail('Unknown integrations must be rejected before version resolution.');
            },
        );

        $this->expectException(InvalidConfigurationException::class);
        $this->expectException(ExceptionInterface::class);
        $this->expectExceptionMessage(
            'Unsupported Yumemi Apocrypha integration "vendor/unknown"; supported integrations: vendor/one, vendor/two.',
        );

        $extension->getFiles();
    }

    public function testMissingExplicitIntegrationIsRejected(): void
    {
        $extension = $this->extension(
            integrations: ['vendor/one'],
            installed: static fn (): bool => false,
            version: static function (): never {
                self::fail('A missing package has no version.');
            },
        );

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage(
            'Yumemi Apocrypha integration "vendor/one" was enabled, but that Composer package is not installed.',
        );

        $extension->getFiles();
    }

    /**
     * @return iterable<string, array{?string, string}>
     */
    public static function invalidVersions(): iterable
    {
        yield 'missing version metadata' => [
            null,
            'Unable to determine the installed major version of Yumemi Apocrypha integration "vendor/one" from "unknown".',
        ];
        yield 'unparseable development branch' => [
            'dev-main',
            'Unable to determine the installed major version of Yumemi Apocrypha integration "vendor/one" from "dev-main".',
        ];
        yield 'unsupported future major' => [
            '14.0.0',
            'Yumemi Apocrypha integration "vendor/one" supports major versions 11, 12, 13; installed version is 14.0.0.',
        ];
    }

    #[DataProvider('invalidVersions')]
    public function testInvalidExplicitVersionIsAlwaysRejected(?string $version, string $message): void
    {
        $extension = $this->extension(
            integrations: ['vendor/one'],
            strictAutoDetect: false,
            installed: static fn (): bool => true,
            version: static fn (): ?string => $version,
        );

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage($message);

        $extension->getFiles();
    }

    #[DataProvider('invalidVersions')]
    public function testStrictAutodetectionRejectsInvalidVersion(?string $version, string $message): void
    {
        $extension = $this->extension(
            autoDetect: true,
            installed: static fn (string $package): bool => $package === 'vendor/one',
            version: static fn (): ?string => $version,
        );

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage($message);

        $extension->getFiles();
    }

    #[DataProvider('invalidVersions')]
    public function testNonStrictAutodetectionSkipsInvalidVersion(?string $version): void
    {
        $extension = $this->extension(
            autoDetect: true,
            strictAutoDetect: false,
            installed: static fn (string $package): bool => $package === 'vendor/one',
            version: static fn (): ?string => $version,
        );

        self::assertSame([], $extension->getFiles());
    }

    public function testNonStrictAutodetectionContinuesAfterAnInvalidIntegration(): void
    {
        $extension = $this->extension(
            autoDetect: true,
            strictAutoDetect: false,
            installed: static fn (): bool => true,
            version: static fn (string $package): string => $package === 'vendor/one' ? '14.0.0' : '12.0.0',
        );

        self::assertSame([realpath(__DIR__ . '/../../extension.neon')], $extension->getFiles());
    }

    public function testVersionMajorMustBeginTheComposerVersion(): void
    {
        $extension = $this->extension(
            integrations: ['vendor/one'],
            installed: static fn (): bool => true,
            version: static fn (): string => 'dev-feature-12.0.0',
        );

        $this->expectExceptionMessage(
            'Unable to determine the installed major version of Yumemi Apocrypha integration "vendor/one" from '
                . '"dev-feature-12.0.0".',
        );

        $extension->getFiles();
    }

    public function testPackageSpecificMinimumVersionIsAccepted(): void
    {
        $supported = $this->supported();
        $supported['vendor/one'] = [
            'majors' => [11, 12, 13],
            'minimumVersions' => [11 => '11.4.2'],
            'files' => [__DIR__ . '/../../apocrypha.neon'],
        ];
        $extension = $this->extension(
            integrations: ['vendor/one'],
            supported: $supported,
            installed: static fn (): bool => true,
            version: static fn (): string => 'v11.4.2',
        );

        self::assertSame([realpath(__DIR__ . '/../../apocrypha.neon')], $extension->getFiles());
    }

    public function testExplicitVersionBelowPackageSpecificMinimumIsRejected(): void
    {
        $supported = $this->supported();
        $supported['vendor/one'] = [
            'majors' => [11, 12, 13],
            'minimumVersions' => [11 => '11.4.2'],
            'files' => [__DIR__ . '/../../apocrypha.neon'],
        ];
        $extension = $this->extension(
            integrations: ['vendor/one'],
            supported: $supported,
            installed: static fn (): bool => true,
            version: static fn (): string => '11.4.1',
        );

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage(
            'Yumemi Apocrypha integration "vendor/one" supports versions 11 (>= 11.4.2), 12, 13; '
                . 'installed version is 11.4.1.',
        );

        $extension->getFiles();
    }

    public function testStrictAutodetectionRejectsVersionBelowPackageSpecificMinimum(): void
    {
        $supported = $this->supported();
        $supported['vendor/one'] = [
            'majors' => [11, 12, 13],
            'minimumVersions' => [11 => '11.4.2'],
            'files' => [__DIR__ . '/../../apocrypha.neon'],
        ];
        $extension = $this->extension(
            autoDetect: true,
            supported: $supported,
            installed: static fn (string $package): bool => $package === 'vendor/one',
            version: static fn (): string => '11.4.1',
        );

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('supports versions 11 (>= 11.4.2), 12, 13; installed version is 11.4.1.');

        $extension->getFiles();
    }

    public function testNonStrictAutodetectionSkipsVersionBelowPackageSpecificMinimum(): void
    {
        $supported = $this->supported();
        $supported['vendor/one'] = [
            'majors' => [11, 12, 13],
            'minimumVersions' => [11 => '11.4.2'],
            'files' => [__DIR__ . '/../../apocrypha.neon'],
        ];
        $extension = $this->extension(
            autoDetect: true,
            strictAutoDetect: false,
            supported: $supported,
            installed: static fn (string $package): bool => $package === 'vendor/one',
            version: static fn (): string => '11.4.1',
        );

        self::assertSame([], $extension->getFiles());
    }

    public function testMissingStubFileIsAnInternalLogicError(): void
    {
        $supported = $this->supported();
        $supported['vendor/one'] = [
            'majors' => [11, 12, 13],
            'files' => [__DIR__ . '/missing.stub'],
        ];
        $extension = $this->extension(
            integrations: ['vendor/one'],
            supported: $supported,
            installed: static fn (): bool => true,
            version: static fn (): string => '12.0.0',
        );

        $this->expectException(LogicException::class);
        $this->expectException(ExceptionInterface::class);
        $this->expectExceptionMessage('Configured Yumemi Apocrypha stub file does not exist:');

        $extension->getFiles();
    }

    public function testMajorSpecificStubFilesOverrideCommonFiles(): void
    {
        $supported = $this->supported();
        $supported['vendor/one'] = [
            'majors' => [11, 12, 13],
            'files' => [__DIR__ . '/../../apocrypha.neon'],
            'filesByMajor' => [13 => [__DIR__ . '/../../extension.neon']],
        ];

        $extension = $this->extension(
            integrations: ['vendor/one'],
            supported: $supported,
            installed: static fn (): bool => true,
            version: static fn (): string => 'v13.2.1',
        );

        self::assertSame([realpath(__DIR__ . '/../../extension.neon')], $extension->getFiles());
    }

    public function testHighestMatchingMinimumVersionProfileOverridesMajorSpecificFiles(): void
    {
        $supported = $this->supported();
        $supported['vendor/one'] = [
            'majors' => [11, 12, 13],
            'files' => [__DIR__ . '/../../apocrypha.neon'],
            'filesByMajor' => [12 => [__DIR__ . '/../../apocrypha.neon']],
            'filesByMinimumVersion' => [
                12 => [
                    '12.0.0' => [__DIR__ . '/../../apocrypha.neon'],
                    '12.4.0' => [__DIR__ . '/../../extension.neon'],
                ],
            ],
        ];

        $extension = $this->extension(
            integrations: ['vendor/one'],
            supported: $supported,
            installed: static fn (): bool => true,
            version: static fn (): string => 'v12.4.0',
        );

        self::assertSame([realpath(__DIR__ . '/../../extension.neon')], $extension->getFiles());
        self::assertSame(12, $extension->getSelectedMajor('vendor/one'));
        self::assertSame('v12.4.0', $extension->getSelectedVersion('vendor/one'));
    }

    public function testVersionProfileFallsBackToTheEarlierMatchingThreshold(): void
    {
        $supported = $this->supported();
        $supported['vendor/one'] = [
            'majors' => [11, 12, 13],
            'files' => [__DIR__ . '/../../extension.neon'],
            'filesByMinimumVersion' => [
                12 => [
                    '12.0.0' => [__DIR__ . '/../../apocrypha.neon'],
                    '12.4.0' => [__DIR__ . '/../../extension.neon'],
                ],
            ],
        ];

        $extension = $this->extension(
            integrations: ['vendor/one'],
            supported: $supported,
            installed: static fn (): bool => true,
            version: static fn (): string => '12.3.9',
        );

        self::assertSame([realpath(__DIR__ . '/../../apocrypha.neon')], $extension->getFiles());
    }

    /** @return iterable<string, array{non-empty-string, list<non-empty-string>}> */
    public static function guzzleVersionProfiles(): iterable
    {
        yield 'before fractional request delay' => [
            '7.10.0',
            ['guzzle.stub', 'guzzle-7-pre-7.11.stub'],
        ];
        yield 'with fractional request delay' => [
            '7.11.0',
            ['guzzle.stub', 'guzzle-7.stub'],
        ];
    }

    /** @param list<non-empty-string> $expectedFiles */
    #[DataProvider('guzzleVersionProfiles')]
    public function testGuzzleSelectsItsVersionProfile(string $version, array $expectedFiles): void
    {
        $extension = new ConfiguredIntegrationStubFilesExtension(
            ['guzzlehttp/guzzle'],
            false,
            true,
            null,
            static fn (string $package): bool => $package === 'guzzlehttp/guzzle',
            static fn (): string => $version,
        );

        self::assertSame(
            array_map(
                static fn (string $file): string => (string) realpath(
                    __DIR__ . '/../../stubs/guzzle/' . $file,
                ),
                $expectedFiles,
            ),
            $extension->getFiles(),
        );
    }

    /** @return iterable<string, array{non-empty-string, non-empty-string, list<non-empty-string>}> */
    public static function illuminateVersionProfiles(): iterable
    {
        yield 'Auth 11 before the cache token repository' => [
            'illuminate/auth',
            '11.30.0',
            ['auth.stub', 'auth-session-guard.stub', 'auth-database-token-repository-11.stub'],
        ];
        yield 'Auth 11 with the cache token repository' => [
            'illuminate/auth',
            '11.31.0',
            [
                'auth.stub',
                'auth-session-guard.stub',
                'auth-database-token-repository-11.stub',
                'auth-cache-token-repository-with-prefix.stub',
            ],
        ];
        yield 'Auth 11 with authentication timeboxes' => [
            'illuminate/auth',
            '11.45.0',
            [
                'auth.stub',
                'auth-session-guard-timebox.stub',
                'auth-password-broker-timebox.stub',
                'auth-database-token-repository-11.stub',
                'auth-cache-token-repository-with-prefix.stub',
            ],
        ];
        yield 'Auth 12 before authentication timeboxes' => [
            'illuminate/auth',
            '12.13.0',
            [
                'auth.stub',
                'auth-session-guard.stub',
                'auth-database-token-repository-12.stub',
                'auth-cache-token-repository-with-prefix.stub',
            ],
        ];
        yield 'Auth 12 with authentication timeboxes' => [
            'illuminate/auth',
            '12.14.0',
            [
                'auth.stub',
                'auth-session-guard-timebox.stub',
                'auth-password-broker-timebox.stub',
                'auth-database-token-repository-12.stub',
                'auth-cache-token-repository-with-prefix.stub',
            ],
        ];
        yield 'Auth 12 without the cache-key prefix' => [
            'illuminate/auth',
            '12.20.0',
            [
                'auth.stub',
                'auth-session-guard-timebox.stub',
                'auth-password-broker-timebox.stub',
                'auth-database-token-repository-12.stub',
                'auth-cache-token-repository.stub',
            ],
        ];
        yield 'Auth 12 with the remember-cookie hash key' => [
            'illuminate/auth',
            '12.45.0',
            [
                'auth.stub',
                'auth-session-guard-timebox-hash-key.stub',
                'auth-password-broker-timebox.stub',
                'auth-database-token-repository-12.stub',
                'auth-cache-token-repository.stub',
            ],
        ];
        yield 'HTTP before fractional timeouts' => [
            'illuminate/http',
            '11.35.0',
            ['http-11.stub'],
        ];
        yield 'HTTP with fractional timeouts' => [
            'illuminate/http',
            '11.35.1',
            ['http.stub'],
        ];
        yield 'Queue 11 before stop-when-empty duration' => [
            'illuminate/queue',
            '11.52.0',
            ['queue.stub', 'queue-worker-11.stub'],
        ];
        yield 'Queue 11 with stop-when-empty duration' => [
            'illuminate/queue',
            '11.53.0',
            ['queue.stub', 'queue-worker-12.stub'],
        ];
        yield 'Queue 12 before stop-when-empty duration' => [
            'illuminate/queue',
            '12.59.0',
            ['queue.stub', 'queue-worker-11.stub'],
        ];
        yield 'Queue 12 with stop-when-empty duration' => [
            'illuminate/queue',
            '12.60.0',
            ['queue.stub', 'queue-worker-12.stub'],
        ];
        yield 'Queue 13 before stop-when-empty duration' => [
            'illuminate/queue',
            '13.9.0',
            ['queue.stub', 'queue-worker-11.stub'],
        ];
        yield 'Queue 13 with stop-when-empty duration' => [
            'illuminate/queue',
            '13.10.0',
            ['queue.stub', 'queue-worker-12.stub'],
        ];
    }

    /** @param list<non-empty-string> $expectedFiles */
    #[DataProvider('illuminateVersionProfiles')]
    public function testIlluminateSelectsItsVersionProfile(
        string $integration,
        string $version,
        array $expectedFiles,
    ): void {
        $extension = new ConfiguredIntegrationStubFilesExtension(
            [$integration],
            false,
            true,
            null,
            static fn (string $package): bool => $package === $integration,
            static fn (): string => $version,
        );

        self::assertSame(
            array_map(
                static fn (string $file): string => (string) realpath(
                    __DIR__ . '/../../stubs/illuminate/' . $file,
                ),
                $expectedFiles,
            ),
            $extension->getFiles(),
        );
    }

    /** @return iterable<string, array{non-empty-string, int}> */
    public static function databaseVersions(): iterable
    {
        yield 'before query timeout' => ['12.50.0', 12];
        yield 'with query timeout' => ['12.51.0', 12];
        yield 'Laravel 13' => ['13.25.0', 13];
    }

    #[DataProvider('databaseVersions')]
    public function testDatabaseAlwaysUsesTheAdapterInsteadOfItsReferenceStubs(
        string $version,
        int $major,
    ): void {
        $extension = new ConfiguredIntegrationStubFilesExtension(
            ['illuminate/database'],
            false,
            true,
            null,
            static fn (string $package): bool => $package === 'illuminate/database',
            static fn (): string => $version,
        );

        self::assertSame([], $extension->getFiles());
        self::assertSame($major, $extension->getSelectedMajor('illuminate/database'));
        self::assertSame($version, $extension->getSelectedVersion('illuminate/database'));
        self::assertTrue($extension->usesUnitBoundaryAdapter('illuminate/database'));
    }

    /** @return iterable<string, array{non-empty-string, int}> */
    public static function routingVersions(): iterable
    {
        yield 'Laravel 11' => ['11.51.0', 11];
        yield 'Laravel 12' => ['12.66.0', 12];
        yield 'Laravel 13' => ['13.25.0', 13];
    }

    #[DataProvider('routingVersions')]
    public function testRoutingAlwaysUsesTheAdapterInsteadOfItsReferenceStub(
        string $version,
        int $major,
    ): void {
        $extension = new ConfiguredIntegrationStubFilesExtension(
            ['illuminate/routing'],
            false,
            true,
            null,
            static fn (string $package): bool => $package === 'illuminate/routing',
            static fn (): string => $version,
        );

        self::assertSame([], $extension->getFiles());
        self::assertSame($major, $extension->getSelectedMajor('illuminate/routing'));
        self::assertSame($version, $extension->getSelectedVersion('illuminate/routing'));
        self::assertTrue($extension->usesUnitBoundaryAdapter('illuminate/routing'));
    }

    /** @return iterable<string, array{string, list<string>}> */
    public static function symfonyHttpFoundationProfiles(): iterable
    {
        yield 'Symfony 6.4 base profile' => [
            '6.4.0',
            ['http-foundation.stub'],
        ];
        yield 'Symfony 7 before SSE' => [
            '7.2.9',
            ['http-foundation.stub'],
        ];
        yield 'Symfony 7 with SSE' => [
            '7.3.0',
            ['http-foundation.stub', 'http-foundation-sse.stub'],
        ];
        yield 'Symfony 8 with SSE and IP byte counts' => [
            '8.0.0',
            ['http-foundation.stub', 'http-foundation-sse.stub', 'http-foundation-ip.stub'],
        ];
    }

    /** @param list<string> $expectedFiles */
    #[DataProvider('symfonyHttpFoundationProfiles')]
    public function testSymfonyHttpFoundationSelectsItsVersionProfile(string $version, array $expectedFiles): void
    {
        $extension = new ConfiguredIntegrationStubFilesExtension(
            ['symfony/http-foundation'],
            false,
            true,
            null,
            static fn (string $package): bool => $package === 'symfony/http-foundation',
            static fn (): string => $version,
        );

        self::assertSame(
            array_map(
                static fn (string $file): string => (string) realpath(__DIR__ . '/../../stubs/symfony/' . $file),
                $expectedFiles,
            ),
            $extension->getFiles(),
        );
    }

    public function testSymfonyHttpFoundationRejectsVersionsBeforeSixFour(): void
    {
        $extension = new ConfiguredIntegrationStubFilesExtension(
            ['symfony/http-foundation'],
            false,
            true,
            null,
            static fn (string $package): bool => $package === 'symfony/http-foundation',
            static fn (): string => '6.3.12',
        );

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage(
            'Yumemi Apocrypha integration "symfony/http-foundation" supports versions 6 (>= 6.4.0), 7, 8; '
                . 'installed version is 6.3.12.',
        );

        $extension->getFiles();
    }

    public function testLarastanThreeSelectsTheAdapterInsteadOfIlluminateStubs(): void
    {
        $extension = new ConfiguredIntegrationStubFilesExtension(
            ['illuminate/cache'],
            false,
            true,
            [
                'illuminate/cache' => [
                    'majors' => [11, 12, 13],
                    'files' => [__DIR__ . '/../../stubs/illuminate/cache.stub'],
                ],
            ],
            static fn (string $package): bool => in_array(
                $package,
                ['illuminate/cache', 'larastan/larastan'],
                true,
            ),
            static fn (string $package): string => $package === 'larastan/larastan' ? 'v3.10.0' : '12.4.0',
        );

        self::assertSame([], $extension->getFiles());
        self::assertSame(12, $extension->getSelectedMajor('illuminate/cache'));
        self::assertTrue($extension->usesUnitBoundaryAdapter('illuminate/cache'));
    }

    public function testCarbonAlwaysUsesTheAdapterInsteadOfPartialStubs(): void
    {
        $resolved = [];
        $extension = new ConfiguredIntegrationStubFilesExtension(
            ['nesbot/carbon'],
            false,
            true,
            null,
            static fn (string $package): bool => $package === 'nesbot/carbon',
            static function (string $package) use (&$resolved): string {
                $resolved[] = $package;

                return '3.13.2';
            },
        );

        self::assertSame([], $extension->getFiles());
        self::assertSame(['nesbot/carbon'], $resolved);
        self::assertTrue($extension->usesUnitBoundaryAdapter('nesbot/carbon'));
        self::assertFalse($extension->usesUnitBoundaryAdapter('illuminate/cache'));
    }

    public function testMeasurementsAlwaysUsesTheAdapterInsteadOfItsReferenceStub(): void
    {
        $extension = new ConfiguredIntegrationStubFilesExtension(
            ['nmarfurt/measurements'],
            false,
            true,
            null,
            static fn (string $package): bool => $package === 'nmarfurt/measurements',
            static fn (): string => 'v1.4.0',
        );

        self::assertSame([], $extension->getFiles());
        self::assertSame(1, $extension->getSelectedMajor('nmarfurt/measurements'));
        self::assertTrue($extension->usesUnitBoundaryAdapter('nmarfurt/measurements'));
    }

    /** @return iterable<string, array{non-empty-string, int}> */
    public static function interventionImageVersions(): iterable
    {
        yield 'version 3' => ['3.0.0', 3];
        yield 'version 4' => ['4.2.1', 4];
    }

    #[DataProvider('interventionImageVersions')]
    public function testInterventionImageAlwaysUsesTheAdapterInsteadOfItsReferenceStub(
        string $version,
        int $major,
    ): void {
        $extension = new ConfiguredIntegrationStubFilesExtension(
            ['intervention/image'],
            false,
            true,
            null,
            static fn (string $package): bool => $package === 'intervention/image',
            static fn (): string => $version,
        );

        self::assertSame([], $extension->getFiles());
        self::assertSame($major, $extension->getSelectedMajor('intervention/image'));
        self::assertTrue($extension->usesUnitBoundaryAdapter('intervention/image'));
    }

    public function testMeasurementsRejectsVersionsBeforeOneFour(): void
    {
        $extension = new ConfiguredIntegrationStubFilesExtension(
            ['nmarfurt/measurements'],
            false,
            true,
            null,
            static fn (string $package): bool => $package === 'nmarfurt/measurements',
            static fn (): string => '1.3.0',
        );

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage(
            'Yumemi Apocrypha integration "nmarfurt/measurements" supports versions 1 (>= 1.4.0); '
                . 'installed version is 1.3.0.',
        );

        $extension->getFiles();
    }

    public function testIlluminateStubsRemainSelectedWithoutLarastan(): void
    {
        $extension = new ConfiguredIntegrationStubFilesExtension(
            ['illuminate/cache'],
            false,
            true,
            [
                'illuminate/cache' => [
                    'majors' => [11, 12, 13],
                    'files' => [__DIR__ . '/../../stubs/illuminate/cache.stub'],
                ],
            ],
            static fn (string $package): bool => $package === 'illuminate/cache',
            static fn (): string => '12.4.0',
        );

        self::assertSame([
            realpath(__DIR__ . '/../../stubs/illuminate/cache.stub'),
        ], $extension->getFiles());
        self::assertFalse($extension->usesUnitBoundaryAdapter('illuminate/cache'));
    }

    public function testLarastanOnlyReplacesSelectedIlluminateStubs(): void
    {
        $supported = $this->supported() + [
            'illuminate/cache' => [
                'majors' => [11, 12, 13],
                'files' => [__DIR__ . '/../../stubs/illuminate/cache.stub'],
            ],
        ];
        $extension = $this->extension(
            integrations: ['illuminate/cache', 'vendor/one'],
            supported: $supported,
            installed: static fn (): bool => true,
            version: static fn (string $package): string => $package === 'larastan/larastan'
                ? '3.10.0'
                : '12.4.0',
        );

        self::assertSame([realpath(__DIR__ . '/../../apocrypha.neon')], $extension->getFiles());
        self::assertTrue($extension->usesUnitBoundaryAdapter('illuminate/cache'));
        self::assertFalse($extension->usesUnitBoundaryAdapter('vendor/one'));
    }

    public function testUnsupportedLarastanDoesNotAffectNonIlluminateIntegrations(): void
    {
        $extension = $this->extension(
            integrations: ['vendor/one'],
            installed: static fn (): bool => true,
            version: static fn (string $package): string => $package === 'larastan/larastan'
                ? '4.0.0'
                : '12.4.0',
        );

        self::assertSame([realpath(__DIR__ . '/../../apocrypha.neon')], $extension->getFiles());
    }

    /** @return iterable<string, array{?string, string}> */
    public static function unsupportedLarastanVersions(): iterable
    {
        yield 'unknown version' => [
            null,
            'Unable to determine the installed Larastan major version from "unknown" while selecting Illuminate '
                . 'integrations.',
        ];
        yield 'unanchored major' => [
            'prefix3.0.0',
            'Unable to determine the installed Larastan major version from "prefix3.0.0" while selecting Illuminate '
                . 'integrations.',
        ];
        yield 'future major' => [
            '4.0.0',
            'Yumemi Apocrypha supports Larastan major version 3 with Illuminate integrations; installed version is 4.0.0.',
        ];
    }

    #[DataProvider('unsupportedLarastanVersions')]
    public function testSelectedIlluminateIntegrationRejectsUnsupportedLarastanVersion(
        ?string $larastanVersion,
        string $message,
    ): void {
        $extension = new ConfiguredIntegrationStubFilesExtension(
            ['illuminate/cache'],
            false,
            true,
            [
                'illuminate/cache' => [
                    'majors' => [11, 12, 13],
                    'files' => [__DIR__ . '/../../stubs/illuminate/cache.stub'],
                ],
            ],
            static fn (string $package): bool => in_array(
                $package,
                ['illuminate/cache', 'larastan/larastan'],
                true,
            ),
            static fn (string $package): ?string => $package === 'larastan/larastan'
                ? $larastanVersion
                : '12.4.0',
        );

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage($message);

        $extension->getFiles();
    }

    /**
     * @param list<string> $integrations
     * @param array<string, array{
     *     majors: non-empty-list<int>,
     *     minimumVersions?: array<int, non-empty-string>,
     *     files: non-empty-list<string>,
     *     filesByMajor?: array<int, non-empty-list<string>>,
     *     filesByMinimumVersion?: array<int, non-empty-array<non-empty-string, non-empty-list<string>>>
     * }>|null $supported
     * @param (\Closure(string): bool)|null $installed
     * @param (\Closure(string): ?string)|null $version
     */
    private function extension(
        array $integrations = [],
        bool $autoDetect = false,
        bool $strictAutoDetect = true,
        ?array $supported = null,
        ?\Closure $installed = null,
        ?\Closure $version = null,
    ): ConfiguredIntegrationStubFilesExtension {
        return new ConfiguredIntegrationStubFilesExtension(
            $integrations,
            $autoDetect,
            $strictAutoDetect,
            $supported ?? $this->supported(),
            $installed,
            $version,
        );
    }

    /**
     * @return array<string, array{
     *     majors: non-empty-list<int>,
     *     minimumVersions?: array<int, non-empty-string>,
     *     files: non-empty-list<string>,
     *     filesByMajor?: array<int, non-empty-list<string>>,
     *     filesByMinimumVersion?: array<int, non-empty-array<non-empty-string, non-empty-list<string>>>
     * }>
     */
    private function supported(): array
    {
        return [
            'vendor/one' => [
                'majors' => [11, 12, 13],
                'files' => [__DIR__ . '/../../apocrypha.neon'],
            ],
            'vendor/two' => [
                'majors' => [11, 12, 13],
                'files' => [__DIR__ . '/../../extension.neon'],
            ],
        ];
    }

    /** @param array{pretty_version?: string, version?: string, replaced?: list<string>, provided?: list<string>} $metadata */
    private function reloadInstalledPackageMetadata(
        array $metadata,
        string $package = 'illuminate/cache',
        string|false|null $apocryphaInstallPath = null,
    ): void {
        $apocryphaMetadata = [
            'pretty_version' => 'dev-consumer',
            'version' => 'dev-consumer',
            'reference' => null,
            'type' => 'phpstan-extension',
            'aliases' => [],
            'dev_requirement' => true,
        ];
        if ($apocryphaInstallPath !== false) {
            $apocryphaMetadata['install_path'] = $apocryphaInstallPath ?? (string) realpath(__DIR__ . '/../..');
        }

        InstalledVersions::reload([
            'root' => [
                'name' => 'consumer/project',
                'pretty_version' => '1.0.0',
                'version' => '1.0.0.0',
                'reference' => null,
                'type' => 'project',
                'install_path' => __DIR__,
                'aliases' => [],
                'dev' => true,
            ],
            'versions' => [
                'consumer/project' => [
                    'pretty_version' => '1.0.0',
                    'version' => '1.0.0.0',
                    'reference' => null,
                    'type' => 'project',
                    'install_path' => __DIR__,
                    'aliases' => [],
                    'dev_requirement' => false,
                ],
                'jbboehr/yumemi-apocrypha' => $apocryphaMetadata,
                $package => ['dev_requirement' => false] + $metadata,
            ],
        ]);
    }
}
