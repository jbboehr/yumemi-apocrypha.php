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
use function PHPStan\Testing\assertType;

function configureRedisLimiters(
    Connection $connection,
    DurationLimiterBuilder $duration,
    ConcurrencyLimiterBuilder $concurrency,
): void {
    $duration
        ->every(unit(30, 'second'))
        ->block(unit(5, 'second'))
        ->sleep(unit(250, 'millisecond'));
    $duration->every(new DateInterval('PT30S'));
    $duration->every(new DateTimeImmutable('+30 seconds'));
    $duration->decay = unit(30, 'second');
    $duration->timeout = unit(5, 'second');
    $duration->sleep = unit(250, 'millisecond');

    $concurrency
        ->releaseAfter(unit(60, 'second'))
        ->block(unit(5, 'second'))
        ->sleep(unit(250, 'millisecond'));
    $concurrency->releaseAfter = unit(60, 'second');
    $concurrency->timeout = unit(5, 'second');
    $concurrency->sleep = unit(250, 'millisecond');

    $durationLimiter = new DurationLimiter($connection, 'reports', 10, unit(30, 'second'));
    $durationLimiter->block(unit(5, 'second'), null, unit(250, 'millisecond'));

    $concurrencyLimiter = new ConcurrencyLimiter($connection, 'reports', 10, unit(60, 'second'));
    $concurrencyLimiter->block(unit(5, 'second'), null, unit(250, 'millisecond'));

    $event = new CommandExecuted('get', ['report'], unit(1.25, 'millisecond'), $connection);
    assertType("unit_float<'1/1000 * second'>", $event->time);
    new CommandExecuted('get', ['report'], null, $connection);
}
