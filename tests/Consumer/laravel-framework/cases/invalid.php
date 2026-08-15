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
use Illuminate\Auth\SessionGuard;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Cookie\Factory;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Database\Connection;
use Illuminate\Foundation\Queue\Queueable as FoundationQueueable;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Process\PendingProcess;
use Illuminate\Queue\WorkerOptions;
use Illuminate\Redis\Limiters\DurationLimiterBuilder;
use Illuminate\Routing\Route;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Support\Sleep;
use Illuminate\Validation\Rules\Dimensions;
use Illuminate\Validation\Rules\File;

use function jbboehr\Yumemi\unit;

/** @param unit_int<'meter'> $meters */
function acceptFrameworkMeters(int $meters): void
{
}

final class InvalidFrameworkBusJob
{
    use Queueable;
}

abstract class InvalidFrameworkBaseBusJob
{
    use FoundationQueueable;
}

final class InvalidInheritedFrameworkBusJob extends InvalidFrameworkBaseBusJob
{
}

/** @param unit_int<'second'> $seconds */
function acceptFrameworkBusSeconds(int $seconds): void
{
}

function rejectInvalidLaravelFrameworkUnits(
    SessionGuard $guard,
    Batch $batch,
    Store $cache,
    Event $event,
    Factory $cookies,
    Connection $database,
    Filesystem $filesystem,
    PendingRequest $request,
    PendingProcess $process,
    Queue $queue,
    DurationLimiterBuilder $redisLimiter,
): void {
    $guard->setRememberDuration(unit(30, 'second'));
    (new InvalidFrameworkBusJob())->delay(unit(1, 'minute'));
    $inheritedJob = new InvalidInheritedFrameworkBusJob();
    $inheritedJob->delay(unit(2, 'minute'));
    $inheritedJob->delay = unit(3, 'minute');
    acceptFrameworkBusSeconds($batch->progress());
    $cache->put('report', 'ready', unit(1, 'minute'));
    $event->withoutOverlapping(unit(30, 'second'));
    $cookies->make('session', 'token', unit(30, 'second'));
    $database->whenQueryingForLongerThan(unit(1, 'second'), static function (): void {
    });
    acceptFrameworkMeters($filesystem->size('report.csv'));
    $request->timeout(unit(500, 'millisecond'));
    $process->timeout(unit(1, 'minute'));
    $queue->later(unit(1, 'minute'), 'App\\Jobs\\RefreshReport');
    $redisLimiter->sleep(unit(1, 'second'));
    (new Route(['GET'], '/report', static fn (): string => 'report'))->block(unit(1, 'minute'));
    new ArraySessionHandler(unit(30, 'second'));
    Sleep::sleep(unit(500, 'millisecond'));
    (new Dimensions())->width(unit(1200, 'css_pixel'));
    (new File())->max(unit(2, 'megabyte'));

    new WorkerOptions(memory: unit(128, '1000000 * byte'));
}
