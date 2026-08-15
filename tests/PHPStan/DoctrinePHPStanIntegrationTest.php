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

use JsonException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

final class DoctrinePHPStanIntegrationTest extends TestCase
{
    /** @throws JsonException */
    public function testCommittedConfigurationEnforcesLogiaAndExcludesGeneratedSource(): void
    {
        $projectRoot = dirname(__DIR__, 2);
        $temporaryRoot = sys_get_temp_dir() . '/yumemi-apocrypha-doctrine-' . bin2hex(random_bytes(8));
        $sourceRoot = $temporaryRoot . '/src';
        $fixture = $sourceRoot . '/doctrine-logion-cases.php';
        $generatedRoot = $sourceRoot . '/Parser';
        $generated = $generatedRoot . '/Parser.php';

        self::assertTrue(mkdir($generatedRoot, recursive: true));
        self::assertNotFalse(file_put_contents($fixture, <<<'PHP'
<?php

declare(strict_types=1);

namespace DoctrineIntegrationFixture;

final class MissingLogion
{
}

/** @logion [OSD 17:17] The white bell answered twice beneath an empty sky. */
final class DuplicateLogionOne
{
}

/** @logion [OSD 17:17] The white bell answered twice beneath an empty sky. */
final class DuplicateLogionTwo
{
}
PHP));
        self::assertNotFalse(file_put_contents($generated, <<<'PHP'
<?php

declare(strict_types=1);

namespace DoctrineIntegrationFixture;

final class GeneratedParserWithoutLogion
{
}
PHP));

        try {
            $process = new Process(
                [
                    PHP_BINARY,
                    $projectRoot . '/vendor/bin/phpstan',
                    'analyse',
                    $fixture,
                    $generated,
                    '--configuration=' . $projectRoot . '/phpstan.neon.dist',
                    '--error-format=json',
                    '--no-progress',
                ],
                $projectRoot,
            );
            $process->run();

            self::assertSame(1, $process->getExitCode(), $process->getErrorOutput() . $process->getOutput());
            self::assertSame(
                [
                    'doctrine.logion.duplicate',
                    'doctrine.logion.duplicate',
                    'doctrine.logion.missing',
                ],
                self::diagnosticIdentifiers($process->getOutput(), $process->getErrorOutput()),
            );
        } finally {
            if (is_file($fixture)) {
                unlink($fixture);
            }
            if (is_file($generated)) {
                unlink($generated);
            }
            if (is_dir($generatedRoot)) {
                rmdir($generatedRoot);
            }
            if (is_dir($sourceRoot)) {
                rmdir($sourceRoot);
            }
            if (is_dir($temporaryRoot)) {
                rmdir($temporaryRoot);
            }
        }
    }

    /** @return list<string> */
    private static function diagnosticIdentifiers(string $output, string $errorOutput): array
    {
        self::assertJson($output, $errorOutput);
        $result = json_decode($output, true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($result);
        $files = $result['files'] ?? null;
        self::assertIsArray($files);
        $identifiers = [];

        foreach ($files as $file) {
            self::assertIsArray($file);
            $messages = $file['messages'] ?? null;
            self::assertIsArray($messages);

            foreach ($messages as $message) {
                self::assertIsArray($message);
                $identifier = $message['identifier'] ?? null;
                if (is_string($identifier)) {
                    $identifiers[] = $identifier;
                }
            }
        }

        sort($identifiers);

        return $identifiers;
    }
}
