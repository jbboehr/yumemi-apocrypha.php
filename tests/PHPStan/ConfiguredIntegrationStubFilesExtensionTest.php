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

use jbboehr\Yumemi\Apocrypha\Exception\ExceptionInterface;
use jbboehr\Yumemi\Apocrypha\Exception\InvalidConfigurationException;
use jbboehr\Yumemi\Apocrypha\Exception\LogicException;
use jbboehr\Yumemi\Apocrypha\PHPStan\ConfiguredIntegrationStubFilesExtension;
use PHPUnit\Framework\Attributes\DataProvider;
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

    /**
     * @param list<string> $integrations
     * @param array<string, array{
     *     majors: non-empty-list<int>,
     *     files: non-empty-list<string>,
     *     filesByMajor?: array<int, non-empty-list<string>>
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
     *     files: non-empty-list<string>,
     *     filesByMajor?: array<int, non-empty-list<string>>
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
}
