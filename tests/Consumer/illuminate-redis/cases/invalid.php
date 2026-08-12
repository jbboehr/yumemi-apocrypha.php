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

use Illuminate\Redis\Connections\Connection;
use Illuminate\Redis\Events\CommandExecuted;
use Illuminate\Redis\Limiters\ConcurrencyLimiter;
use Illuminate\Redis\Limiters\ConcurrencyLimiterBuilder;
use Illuminate\Redis\Limiters\DurationLimiter;
use Illuminate\Redis\Limiters\DurationLimiterBuilder;

use function jbboehr\Yumemi\unit;

function misconfigureRedisLimiters(
    Connection $connection,
    DurationLimiterBuilder $duration,
    ConcurrencyLimiterBuilder $concurrency,
): void {
    $duration->every(unit(1, 'minute'));
    $duration->every(30);
    $duration->block(unit(250, 'millisecond'));
    $duration->sleep(unit(1, 'second'));
    $duration->sleep(250);
    $duration->decay = unit(1, 'minute');
    $duration->sleep = unit(1, 'second');

    $concurrency->releaseAfter(unit(1, 'minute'));
    $concurrency->block(unit(250, 'millisecond'));
    $concurrency->sleep(unit(1, 'second'));
    $concurrency->releaseAfter = unit(1, 'minute');

    $durationLimiter = new DurationLimiter($connection, 'reports', 10, unit(1, 'minute'));
    $durationLimiter->block(unit(1, 'minute'), null, unit(1, 'second'));

    $concurrencyLimiter = new ConcurrencyLimiter($connection, 'reports', 10, unit(1, 'minute'));
    $concurrencyLimiter->block(unit(1, 'minute'), null, unit(1, 'second'));

    $event = new CommandExecuted('get', ['report'], unit(1.25, 'second'), $connection);
    new CommandExecuted('get', ['report'], 1.25, $connection);
    $event->time = unit(1.25, 'second');
}
