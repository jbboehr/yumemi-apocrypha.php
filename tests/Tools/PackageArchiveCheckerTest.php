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

namespace jbboehr\Yumemi\Apocrypha\Tests\Tools;

use jbboehr\Yumemi\Apocrypha\Tools\PackageArchiveChecker;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PackageArchiveChecker::class)]
final class PackageArchiveCheckerTest extends TestCase
{
    /** @var list<non-empty-string> */
    private array $archives = [];

    protected function tearDown(): void
    {
        foreach ($this->archives as $archive) {
            if (is_file($archive)) {
                self::assertTrue(unlink($archive));
            }
        }
    }

    public function testAcceptsRequiredAndWhitelistedFiles(): void
    {
        $archive = $this->archive([
            'README.md' => 'read me',
            'src/Example.php' => '<?php',
        ]);
        $checker = new PackageArchiveChecker(
            ['README.md'],
            ['README.md'],
            ['src/'],
        );

        self::assertSame(2, $checker->check($archive));
    }

    public function testRejectsMissingRequiredFile(): void
    {
        $archive = $this->archive(['src/Example.php' => '<?php']);
        $checker = new PackageArchiveChecker(
            ['README.md'],
            ['README.md'],
            ['src/'],
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Package archive omitted required file README.md.');
        $checker->check($archive);
    }

    public function testRejectsFileOutsideWhitelist(): void
    {
        $archive = $this->archive([
            'README.md' => 'read me',
            'tests/AccidentalTest.php' => '<?php',
        ]);
        $checker = new PackageArchiveChecker(
            ['README.md'],
            ['README.md'],
            ['src/'],
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Package archive contained unexpected file tests/AccidentalTest.php.');
        $checker->check($archive);
    }

    public function testRejectsBlacklistedFileWithinWhitelistedPrefix(): void
    {
        $archive = $this->archive([
            'README.md' => 'read me',
            'docs/pages/images/logia/OSD-1_1.webp' => 'image',
        ]);
        $checker = new PackageArchiveChecker(
            ['README.md'],
            ['README.md'],
            ['docs/pages/'],
            ['docs/pages/images/logia/'],
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Package archive contained unexpected file docs/pages/images/logia/OSD-1_1.webp.',
        );
        $checker->check($archive);
    }

    public function testRejectsMissingArchive(): void
    {
        $checker = new PackageArchiveChecker(
            ['README.md'],
            ['README.md'],
            ['src/'],
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('does not exist or is not a file');
        $checker->check(__DIR__ . '/missing-package.tar');
    }

    /**
     * @param non-empty-array<non-empty-string, string> $files
     *
     * @return non-empty-string
     */
    private function archive(array $files): string
    {
        $archive = rtrim(sys_get_temp_dir(), '/\\')
            . '/yumemi-apocrypha-package-checker-test-'
            . bin2hex(random_bytes(16))
            . '.tar';
        $package = new \PharData($archive);

        foreach ($files as $path => $contents) {
            $package->addFromString($path, $contents);
        }

        $this->archives[] = $archive;

        return $archive;
    }
}
