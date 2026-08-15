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

namespace jbboehr\Yumemi\Apocrypha\Tests\PHPStan\Fixtures;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Queue\WorkerOptions;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Sleep;
use Illuminate\Support\Timebox;

use function jbboehr\Yumemi\unit;
use function PHPStan\Testing\assertType;

/**
 * @param array<mixed>                    $unknownArguments
 * @param array{string}|array{string, int} $ambiguousArguments
 */
function exerciseLarastanCompatibilityExtensions(
    Filesystem $filesystem,
    Job $job,
    WorkerOptions $options,
    Timebox $timebox,
    array $unknownArguments,
    array $ambiguousArguments,
    string $dynamicProperty,
): void {
    Sleep::sleep(unit(1, 'second'));
    Sleep::sleep(unit(1, 'minute'));
    Sleep::usleep(duration: unit(1, 'millisecond'));
    Sleep::sleep(...[unit(1, 'minute')]);
    Sleep::sleep(...$unknownArguments);

    $timebox->call(static fn (): string => 'done', unit(1, 'millisecond'));
    new WorkerOptions(timeout: unit(1, 'minute'));
    new WorkerOptions(memory: unit(1, 'byte'), timeout: unit(1, 'minute'));
    new WorkerOptions(
        'default',
        unit(1, 'second'),
        unit(128, '1048576 * byte'),
        unit(1, 'second'),
        timeout: unit(1, 'minute'),
    );
    new WorkerOptions(...['default'], ...[unit(1, 'second'), unit(1, 'byte'), unit(1, 'minute')]);
    new WorkerOptions(...[
        'memory' => unit(1, 'byte'),
        'timeout' => unit(1, 'minute'),
    ]);

    new WorkerOptions(...$ambiguousArguments, ...[unit(1, 'minute')]);
    new WorkerOptions(...$unknownArguments, ...[unit(1, 'minute')]);

    $job->release(unit(1, 'minute'));

    $options->timeout = unit(1, 'minute');
    $options->timeout += unit(1, 'minute');
    ++$options->timeout;
    --$options->timeout;
    $options->timeout++;
    $options->timeout--;

    $local = unit(1, 'minute');
    $options->{$dynamicProperty} = $local;

    assertType("unit_int<'second'>", $options->timeout);
    assertType("unit_int<'octet'>|false", $filesystem->put('report.txt', 'contents'));
    assertType("unit_float<'1/1000 * second'>", Benchmark::measure(static fn (): string => 'done'));
    assertType(
        "array<'first'|'second', unit_float<'1/1000 * second'>>",
        Benchmark::measure([
            'first' => static fn (): string => 'done',
            'second' => static fn (): string => 'done',
        ]),
    );
    assertType(
        "array{17, unit_float<'1/1000 * second'>}",
        Benchmark::value(static fn (): int => 17),
    );

    Sleep::SLEEP(unit(1, 'minute'));
}

function exerciseIndexedArgumentBoundaryLookup(
    \Illuminate\Cache\Repository $cache,
    \Carbon\CarbonInterface $carbon,
): void {
    $cache->put('key', 'value', unit(1, 'minute'));
    $carbon->addUTCSeconds(unit(1, 'minute'));
}

function exerciseInheritedQueueableBoundary(): void
{
    $job = new InheritedQueueableJob();
    $job->delay(unit(1, 'minute'));
    $job->delay = unit(1, 'minute');
}

function exerciseDefaultStaticReturnBoundary(): void
{
    assertType("unit_int<'second'>|null", \Illuminate\Support\Facades\Cache::getDefaultCacheTime());
}

function exerciseCacheHelperBoundary(\Illuminate\Cache\Repository $repository): void
{
    \cache()->put('cache-key', 'value', unit(1, 'minute'));
    \cache('cache-key')->put('cache-key', 'value', unit(1, 'minute'));

    $factory = static fn (): \Illuminate\Cache\Repository => $repository;
    $factory()->put('cache-key', 'value', unit(1, 'minute'));
}
