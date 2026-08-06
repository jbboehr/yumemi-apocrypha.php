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

use jbboehr\Yumemi\Apocrypha\Tools\StubCandidateSurvey\JsonStorage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(JsonStorage::class)]
final class JsonStorageTest extends TestCase
{
    public function testInvalidUtf8IsReplacedInJson(): void
    {
        $path = sys_get_temp_dir() . '/yumemi-apocrypha-json-' . bin2hex(random_bytes(8)) . '.json';

        try {
            JsonStorage::write($path, ['text' => "valid\xB0text"]);

            self::assertSame(['text' => "valid�text"], JsonStorage::read($path));
        } finally {
            @unlink($path);
        }
    }

    public function testInvalidUtf8IsReplacedInJsonLines(): void
    {
        $path = sys_get_temp_dir() . '/yumemi-apocrypha-jsonl-' . bin2hex(random_bytes(8)) . '.jsonl';

        try {
            JsonStorage::writeLines($path, [['text' => "valid\xB0text"]]);

            self::assertSame([['text' => "valid�text"]], JsonStorage::readLines($path));
        } finally {
            @unlink($path);
        }
    }
}
