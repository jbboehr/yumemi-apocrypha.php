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

namespace jbboehr\Yumemi\Apocrypha\Tests\Documentation;

use Composer\InstalledVersions;
use PHPUnit\Framework\TestCase;

final class DoctrineDocumentsTest extends TestCase
{
    public function testEveryAdoptedDocumentIsInstalled(): void
    {
        $packageDirectory = $this->doctrinePackageDirectory();
        $documents = [
            'DOCTRINE-STYLE-GUIDE.md',
            'DOCTRINE-CODING-GUIDE.md',
            'DOCTRINE-GENERATION-GUIDE.md',
            'DOCTRINE-GOLD-EXEMPLARS.md',
            'DOCTRINE-IMAGE-GUIDE.md',
            'MEASURE-OF-WORDS.md',
            'RUINENWERT.md',
            'CODE_OF_SOVEREIGNTY.md',
        ];

        foreach ($documents as $document) {
            $path = $packageDirectory . '/' . $document;

            self::assertTrue(is_readable($path), sprintf('Adopted Doctrine document is not readable: %s', $path));

            $contents = file_get_contents($path);

            self::assertNotFalse($contents, sprintf('Adopted Doctrine document could not be read: %s', $path));
            self::assertNotSame('', trim($contents), sprintf('Adopted Doctrine document is empty: %s', $path));
        }
    }

    public function testCodexAgentAdaptersMatchInstalledPackage(): void
    {
        $packageDirectory = $this->doctrinePackageDirectory();
        $repositoryDirectory = dirname(__DIR__, 2);
        $agents = [
            'doctrine-writer.toml',
            'doctrine-reviewer.toml',
        ];

        foreach ($agents as $agent) {
            $packagedPath = $packageDirectory . '/integrations/codex/agents/' . $agent;
            $repositoryPath = $repositoryDirectory . '/.codex/agents/' . $agent;

            self::assertFileEquals(
                $packagedPath,
                $repositoryPath,
                sprintf('Codex Doctrine agent differs from the installed package: %s', $agent),
            );
        }
    }

    public function testCodeOfConductAdoptsCodeOfSovereignty(): void
    {
        $packageDirectory = $this->doctrinePackageDirectory();
        $repositoryDirectory = dirname(__DIR__, 2);
        $canonical = file_get_contents($packageDirectory . '/CODE_OF_SOVEREIGNTY.md');
        $local = file_get_contents($repositoryDirectory . '/CODE_OF_CONDUCT.md');

        self::assertNotFalse($canonical);
        self::assertNotFalse($local);

        $canonical = str_replace(["\r\n", "\r"], "\n", $canonical);
        $local = str_replace(["\r\n", "\r"], "\n", $local);
        $canonical = preg_replace(
            '/^!\[[^\n]+]\(assets\/banners\/code-of-sovereignty\.webp\)\n\n/m',
            '',
            $canonical,
        );

        self::assertNotNull($canonical);
        self::assertSame($canonical, $local);
    }

    private function doctrinePackageDirectory(): string
    {
        $package = 'jbboehr/doctrine-of-the-second-sun';

        self::assertTrue(InstalledVersions::isInstalled($package), sprintf('Doctrine package is not installed: %s', $package));

        $packageDirectory = InstalledVersions::getInstallPath($package);

        self::assertNotNull($packageDirectory, sprintf('Doctrine package install path is unavailable: %s', $package));

        return $packageDirectory;
    }
}
