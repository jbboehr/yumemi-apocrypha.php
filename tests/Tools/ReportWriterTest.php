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

use jbboehr\Yumemi\Apocrypha\Tools\StubCandidateSurvey\ReportWriter;
use jbboehr\Yumemi\Apocrypha\Tools\StubCandidateSurvey\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @phpstan-import-type FindingRecord from Schema
 * @phpstan-import-type RepositoryRecord from Schema
 */
#[CoversClass(ReportWriter::class)]
final class ReportWriterTest extends TestCase
{
    public function testReportOutputIsDeterministicAndSeparatesNoisyYield(): void
    {
        $directory = sys_get_temp_dir() . '/apocrypha-report-' . bin2hex(random_bytes(8));
        self::assertTrue(mkdir($directory));
        $first = $directory . '/first.md';
        $second = $directory . '/second.md';
        $repository = $this->repository();
        $finding = $this->finding();

        try {
            $writer = new ReportWriter();
            $manifest = [
                'snapshot' => 'fixture',
                'profile' => 'science',
                'collectedAt' => '2026-08-06T01:21:33+00:00',
                'tagDiscoveries' => ['focused' => [], 'noisy' => ['measurement' => 1]],
                'counts' => ['selectedNewRepositories' => 1, 'selectedBaselineRepositories' => 0],
            ];
            $writer->write($first, [$repository], [$finding], $manifest);
            $writer->write($second, [$repository], [$finding], $manifest);

            self::assertFileEquals($first, $second);
            $contents = (string) file_get_contents($first);
            self::assertStringContainsString('- Collected at: `2026-08-06T01:21:33+00:00`', $contents);
            self::assertStringContainsString('- Profile: `science`', $contents);
            self::assertStringContainsString('- New repositories: 1', $contents);
            self::assertStringContainsString('1. **`example/measure` (`1.0.0`)** — noisy; signature;', $contents);
            self::assertStringContainsString('| noisy | 1 | 1 | 1 | 0 | 0 |', $contents);
            self::assertStringContainsString('| noisy | `measurement` | 1 | 1 | 1 | 1 |', $contents);
            self::assertStringContainsString('These findings are broad discovery leads', $contents);
        } finally {
            if (is_file($first)) {
                unlink($first);
            }
            if (is_file($second)) {
                unlink($second);
            }
            rmdir($directory);
        }
    }

    public function testManualQueueExcludesSingleUnitFindings(): void
    {
        $directory = sys_get_temp_dir() . '/apocrypha-report-' . bin2hex(random_bytes(8));
        self::assertTrue(mkdir($directory));
        $path = $directory . '/report.md';
        $finding = $this->finding();
        $finding['locality'] = 'single-unit';

        try {
            (new ReportWriter())->write($path, [$this->repository()], [$finding], ['snapshot' => 'fixture']);
            $contents = (string) file_get_contents($path);
            $queue = substr($contents, (int) strpos($contents, '## Manual verification queue'));

            self::assertStringNotContainsString('1. `example/measure`', $queue);
        } finally {
            if (is_file($path)) {
                unlink($path);
            }
            rmdir($directory);
        }
    }

    /** @return RepositoryRecord */
    private function repository(): array
    {
        return [
            'key' => 'github.com/example/measure',
            'url' => 'https://github.com/example/measure',
            'owner' => 'example',
            'package' => 'example/measure',
            'packages' => ['example/measure'],
            'version' => '1.0.0',
            'stratum' => 'noisy',
            'sources' => [['stratum' => 'noisy', 'tag' => 'measurement', 'rank' => 1, 'role' => null]],
            'distUrl' => 'https://example.com/archive.zip',
            'distType' => 'zip',
            'stats' => [
                'downloadsTotal' => 10,
                'downloadsMonthly' => 5,
                'downloadsDaily' => 1,
                'dependents' => 2,
                'favers' => 3,
                'abandoned' => false,
            ],
            'archivePath' => '/tmp/archive.zip',
            'archiveSha256' => str_repeat('a', 64),
            'archiveBytes' => 100,
            'archiveStatus' => 'downloaded',
            'archiveError' => null,
        ];
    }

    /** @return FindingRecord */
    private function finding(): array
    {
        return [
            'repositoryKey' => 'github.com/example/measure',
            'package' => 'example/measure',
            'version' => '1.0.0',
            'stratum' => 'noisy',
            'locality' => 'signature',
            'dimensions' => ['time' => ['millisecond', 'second']],
            'declarations' => [],
            'evidenceCount' => 2,
            'distinctScaleCount' => 2,
        ];
    }
}
