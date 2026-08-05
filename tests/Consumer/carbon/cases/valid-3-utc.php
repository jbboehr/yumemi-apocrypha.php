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

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

use function jbboehr\Yumemi\unit;
use function PHPStan\Testing\assertType;

$start = Carbon::parse('2026-01-01 00:00:00.000000');
$end = CarbonImmutable::parse('2026-01-01 01:02:03.004005');

assertType("unit_float<'1/1000000 * second'>", $start->diffInUTCMicroseconds($end));
assertType("unit_float<'1/1000 * second'>", $start->diffInUTCMilliseconds($end));
assertType("unit_float<'second'>", $start->diffInUTCSeconds($end));
assertType("unit_float<'minute'>", $start->diffInUTCMinutes($end));
assertType("unit_float<'hour'>", $start->diffInUTCHours($end));

$start->addUTCMicroseconds(unit(0.5, 'microsecond'));
$start->subUTCMilliseconds(unit(5, 'millisecond'));
$start->addUTCSeconds(unit(5.5, 'second'));
$start->subUTCMinutes(unit(5, 'minute'));
$start->addUTCHours(unit(0.5, 'hour'));
$start->subUTCHours();
$start->addUTCDay();

function exerciseCurrentCarbonThreeInterface(CarbonInterface $date): void
{
    assertType("unit_float<'second'>", $date->diffInUTCSeconds(CarbonImmutable::now()));
    $date->addUTCSeconds(unit(2, 'second'));
}

assertType(CarbonInterface::class, $end->addUTCSeconds(unit(2, 'second')));
