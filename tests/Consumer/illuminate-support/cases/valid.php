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

use Illuminate\Support\Benchmark;
use Illuminate\Support\Sleep;
use Illuminate\Support\Timebox;

use function jbboehr\Yumemi\unit;
use function PHPStan\Testing\assertType;

$measurement = Benchmark::measure(static function (): void {
});
assertType("unit_float<'1/1000 * second'>", $measurement);

[$result, $duration] = Benchmark::value(static fn (): string => 'done');
assertType("'done'", $result);
assertType("unit_float<'1/1000 * second'>", $duration);

Sleep::sleep(unit(2, 'second'));
Sleep::usleep(unit(500, 'microsecond'));

$timebox = new Timebox();
assertType("'done'", $timebox->call(static fn (): string => 'done', unit(250, 'microsecond')));
