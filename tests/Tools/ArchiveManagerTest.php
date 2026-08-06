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

use jbboehr\Yumemi\Apocrypha\Tools\StubCandidateSurvey\ArchiveManager;
use jbboehr\Yumemi\Apocrypha\Tools\StubCandidateSurvey\CachedHttpClient;
use jbboehr\Yumemi\Apocrypha\Tools\StubCandidateSurvey\Config;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(ArchiveManager::class)]
final class ArchiveManagerTest extends TestCase
{
    private ArchiveManager $manager;
    private Config $config;

    protected function setUp(): void
    {
        $this->manager = new ArchiveManager(new CachedHttpClient(1, 0, 'test'));
        $this->config = Config::fromFile(__DIR__ . '/../../tools/stub-candidate-survey.json');
    }

    public function testRejectsTooManyArchiveEntriesWithoutConstructingAnUnsafeArchive(): void
    {
        $this->expectException(RuntimeException::class);
        $this->manager->validateArchiveMetrics($this->config->archiveLimits['entries'] + 1, 0, $this->config);
    }

    public function testRejectsExcessiveUncompressedSizeWithoutAllocatingIt(): void
    {
        $this->expectException(RuntimeException::class);
        $this->manager->validateArchiveMetrics(1, $this->config->archiveLimits['uncompressedBytes'] + 1, $this->config);
    }
}
