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

use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchPeriod;

use function PHPStan\Testing\assertType;

/** @param unit_int<'millisecond'>|unit_float<'millisecond'> $duration */
function recordStopwatchMilliseconds(int|float $duration): void
{
}

/** @param unit_int<'byte'> $memory */
function recordStopwatchBytes(int $memory): void
{
}

$event = (new Stopwatch(morePrecision: true))->start('render-report');
assertType("unit_float<'1/1000 * second'>|unit_int<'1/1000 * second'>", $event->getDuration());
assertType("unit_int<'octet'>", $event->getMemory());
recordStopwatchMilliseconds($event->getDuration());
recordStopwatchBytes($event->getMemory());

$period = new StopwatchPeriod(10, 20, true);
assertType("unit_float<'1/1000 * second'>|unit_int<'1/1000 * second'>", $period->getDuration());
assertType("unit_int<'octet'>", $period->getMemory());
recordStopwatchMilliseconds($period->getDuration());
recordStopwatchBytes($period->getMemory());
