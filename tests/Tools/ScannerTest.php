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

use jbboehr\Yumemi\Apocrypha\Tools\StubCandidateSurvey\Config;
use jbboehr\Yumemi\Apocrypha\Tools\StubCandidateSurvey\Scanner;
use jbboehr\Yumemi\Apocrypha\Tools\StubCandidateSurvey\Schema;
use jbboehr\Yumemi\Apocrypha\Tools\StubCandidateSurvey\UnitLexicon;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ZipArchive;

/**
 * @phpstan-import-type RepositoryRecord from Schema
 */
#[CoversClass(Scanner::class)]
final class ScannerTest extends TestCase
{
    public function testPublicMethodWithSecondsAndMillisecondsIsASignatureCollision(): void
    {
        $source = <<<'PHP'
<?php
final class Limiter
{
    /**
     * @param int $timeout Number of seconds to block.
     * @param int $sleep Number of milliseconds between attempts.
     */
    public function block(int $timeout, int $sleep): void {}
}
PHP;

        $finding = $this->scanArchive($source);

        self::assertSame('signature', $finding['locality']);
        self::assertSame(['millisecond', 'second'], $finding['dimensions']['time']);
    }

    public function testPackageUsingOnlySecondsIsDeprioritized(): void
    {
        $source = <<<'PHP'
<?php
interface Cache
{
    /** @param int $ttl Number of seconds before expiration. */
    public function put(string $key, mixed $value, int $ttl): void;
}
PHP;

        $finding = $this->scanArchive($source);

        self::assertSame('single-unit', $finding['locality']);
        self::assertSame(['second'], $finding['dimensions']['time']);
    }

    public function testTestDeclarationsDoNotBecomePublicCandidates(): void
    {
        $source = <<<'PHP'
<?php
final class Cache
{
    /** @param int $ttl Number of seconds before expiration. */
    public function put(int $ttl): void {}
}
PHP;
        $testSource = <<<'PHP'
<?php
final class CacheTest
{
    /** Exercise seconds and milliseconds. */
    public function testMixedUnits(): void {}
}
PHP;

        $finding = $this->scanArchive($source, ['package/tests/CacheTest.php' => $testSource]);

        self::assertSame('single-unit', $finding['locality']);
        self::assertSame(['second'], $finding['dimensions']['time']);
    }

    /**
     * @param array<string, string> $additionalFiles
     * @return array{locality: string, dimensions: array<string, list<string>>}
     */
    private function scanArchive(string $source, array $additionalFiles = []): array
    {
        $path = tempnam(sys_get_temp_dir(), 'apocrypha-survey-');
        self::assertNotFalse($path);
        $archive = new ZipArchive();
        self::assertTrue($archive->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE));
        self::assertTrue($archive->addFromString('package/src/Fixture.php', $source));
        foreach ($additionalFiles as $name => $contents) {
            self::assertTrue($archive->addFromString($name, $contents));
        }
        self::assertTrue($archive->close());

        try {
            $scanner = new Scanner(new UnitLexicon());
            $config = Config::fromFile(__DIR__ . '/../../tools/stub-candidate-survey.json');
            $finding = $scanner->scanRepository($this->repository($path), $config);

            return ['locality' => $finding['locality'], 'dimensions' => $finding['dimensions']];
        } finally {
            unlink($path);
        }
    }

    /** @return RepositoryRecord */
    private function repository(string $path): array
    {
        return [
            'key' => 'github.com/example/fixture',
            'url' => 'https://github.com/example/fixture',
            'owner' => 'example',
            'package' => 'example/fixture',
            'packages' => ['example/fixture'],
            'version' => '1.0.0',
            'stratum' => 'curated',
            'sources' => [['stratum' => 'curated', 'tag' => null, 'rank' => 1, 'role' => 'fixture']],
            'distUrl' => 'https://example.com/archive.zip',
            'distType' => 'zip',
            'stats' => null,
            'archivePath' => $path,
            'archiveSha256' => false === ($sha256 = hash_file('sha256', $path)) ? null : $sha256,
            'archiveBytes' => false === ($size = filesize($path)) ? null : $size,
            'archiveStatus' => 'downloaded',
            'archiveError' => null,
        ];
    }
}
