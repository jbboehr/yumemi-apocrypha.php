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

use Illuminate\Contracts\Cache\Store;
use Illuminate\Contracts\Cookie\Factory;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Database\Connection;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Process\PendingProcess;
use Illuminate\Queue\WorkerOptions;
use Illuminate\Redis\Limiters\DurationLimiterBuilder;
use Illuminate\Support\Sleep;
use Illuminate\Validation\Rules\Dimensions;
use Illuminate\Validation\Rules\File;

use function jbboehr\Yumemi\unit;
use function PHPStan\Testing\assertType;

function exerciseLaravelFrameworkIntegrations(
    Store $cache,
    Factory $cookies,
    Connection $database,
    Filesystem $filesystem,
    PendingRequest $request,
    PendingProcess $process,
    Queue $queue,
    DurationLimiterBuilder $redisLimiter,
): void {
    $seconds = unit(30, 'second');

    $cache->put('report', 'ready', $seconds);
    $cookies->make('session', 'token', unit(30, 'minute'));
    $database->whenQueryingForLongerThan(unit(500, 'millisecond'), static function (): void {
    });
    assertType("unit_float<'1/1000 * second'>", $database->totalQueryDuration());
    assertType("unit_int<'octet'>", $filesystem->size('report.csv'));
    $request->timeout($seconds);
    $process->timeout($seconds);
    $queue->later($seconds, 'App\\Jobs\\RefreshReport');
    $redisLimiter->every($seconds)->block($seconds)->sleep(unit(250, 'millisecond'));
    Sleep::sleep($seconds);
    (new Dimensions())->width(unit(1200, 'pixel'))->height(unit(800, 'pixel'));
    (new File())->between(unit(64, '1024 * byte'), unit(2048, '1024 * byte'));

    new WorkerOptions(
        backoff: [unit(1, 'second'), unit(5, 'second')],
        memory: unit(128, '1048576 * byte'),
        timeout: unit(60, 'second'),
        sleep: unit(3, 'second'),
        maxTime: unit(3600, 'second'),
        rest: unit(1, 'second'),
    );
}
