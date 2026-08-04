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

/** @param unit_int<'second'>|unit_float<'second'> $duration */
function recordStopwatchSeconds(int|float $duration): void
{
}

/** @param unit_int<'meter'>|unit_float<'meter'> $distance */
function recordStopwatchDistance(int|float $distance): void
{
}

/** @param unit_int<'kilobyte'> $memory */
function recordStopwatchKilobytes(int $memory): void
{
}

/** @param unit_int<'second'> $memory */
function recordStopwatchMemoryDuration(int $memory): void
{
}

$event = (new Stopwatch())->start('render-report');
$period = new StopwatchPeriod(10, 20);

recordStopwatchSeconds($event->getDuration());
recordStopwatchDistance($period->getDuration());
recordStopwatchKilobytes($event->getMemory());
recordStopwatchMemoryDuration($period->getMemory());
