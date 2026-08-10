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

$start->addRealMicroseconds(unit(0.5, 'microsecond'));
$start->subRealMilliseconds(unit(5, 'millisecond'));
$start->addRealSeconds(unit(5.5, 'second'));
$start->subRealMinutes(unit(5, 'minute'));
$start->addRealHours(unit(0.5, 'hour'));
$start->subRealHours();
$start->addRealDay();

function exerciseEarlyCarbonThreeInterface(CarbonInterface $date): void
{
    $date->addRealSeconds(unit(2, 'second'));
}

assertType(CarbonImmutable::class, $end->addRealSeconds(unit(2, 'second')));
