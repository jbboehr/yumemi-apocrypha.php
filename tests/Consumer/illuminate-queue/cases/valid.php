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

use Illuminate\Contracts\Queue\Job;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\WorkerOptions;

use function jbboehr\Yumemi\unit;
use function PHPStan\Testing\assertType;

final class BackgroundReportJob
{
    use InteractsWithQueue;
}

function scheduleBackgroundJob(Queue $queue, Job $job): void
{
    $queue->later(unit(30, 'second'), 'App\\Jobs\\RefreshReport');
    $queue->later(...[unit(30, 'second'), 'App\\Jobs\\RefreshReport']);
    $queue->later(delay: unit(30, 'second'), job: 'App\\Jobs\\RefreshReport');
    $queue->laterOn('reports', unit(1, 'second'), 'App\\Jobs\\RefreshReport');
    $job->release(unit(5, 'second'));
    (new BackgroundReportJob())->release(unit(5, 'second'));

    $options = new WorkerOptions(
        backoff: [unit(1, 'second'), unit(5, 'second')],
        memory: unit(128, '1048576 * byte'),
        timeout: unit(60, 'second'),
        sleep: unit(3, 'second'),
        maxTime: unit(3600, 'second'),
        rest: unit(1, 'second'),
    );

    assertType("array<unit_int<'second'>>|unit_int<'second'>", $options->backoff);
    assertType("unit_int<'1048576 * octet'>", $options->memory);
    assertType("unit_int<'second'>", $options->timeout);
    assertType("unit_int<'second'>", $options->sleep);
    assertType("unit_int<'second'>", $options->maxTime);
    assertType("unit_int<'second'>", $options->rest);

    $options->timeout = unit(120, 'second');
    $options->timeout += unit(1, 'second');

    new WorkerOptions(...[
        'name' => 'reports',
        'backoff' => unit(1, 'second'),
        'memory' => unit(128, '1048576 * byte'),
        'timeout' => unit(60, 'second'),
        'sleep' => unit(3, 'second'),
    ]);
}
