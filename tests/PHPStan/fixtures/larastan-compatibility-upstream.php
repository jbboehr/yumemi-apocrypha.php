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

namespace Illuminate\Filesystem;

final class Filesystem
{
    public function put(string $path, string $contents): int|false
    {
        return $contents === '' ? false : strlen($contents);
    }
}

namespace Illuminate\Contracts\Queue;

interface Job
{
    public function release(int $delay = 0): void;
}

namespace Illuminate\Queue;

final class WorkerOptions
{
    /** @param int|array<int> $backoff */
    public function __construct(
        public string $name = 'default',
        public int|array $backoff = 0,
        public int $memory = 128,
        public int $timeout = 60,
        public int $sleep = 3,
        public int $maxTries = 1,
        public bool $force = false,
        public bool $stopWhenEmpty = false,
        public int $maxJobs = 0,
        public int $maxTime = 0,
        public int $rest = 0,
        public int $stopWhenEmptyFor = 0,
    ) {
    }
}

namespace Illuminate\Queue\Jobs;

final class Job implements \Illuminate\Contracts\Queue\Job
{
    public function release(int $delay = 0): void
    {
    }
}

namespace Illuminate\Support;

final class Benchmark
{
    /**
     * @template TKey of array-key
     * @template TBenchmarkables of \Closure|array<TKey, \Closure>
     *
     * @param TBenchmarkables $benchmarkables
     *
     * @return (TBenchmarkables is \Closure ? float : array<TKey, float>)
     */
    public static function measure(\Closure|array $benchmarkables, int $iterations = 1): float|array
    {
        return $benchmarkables instanceof \Closure ? 1.0 : array_fill_keys(array_keys($benchmarkables), 1.0);
    }

    /**
     * @template TReturn
     *
     * @param \Closure(): TReturn $benchmarkable
     *
     * @return array{TReturn, float}
     */
    public static function value(\Closure $benchmarkable): array
    {
        return [$benchmarkable(), 1.0];
    }
}

final class Sleep
{
    public static function sleep(int|float $duration): self
    {
        return new self();
    }

    public static function usleep(int $duration): self
    {
        return new self();
    }
}

final class Timebox
{
    /**
     * @template TReturn
     *
     * @param callable(): TReturn $callback
     *
     * @return TReturn
     */
    public function call(callable $callback, int $microseconds): mixed
    {
        return $callback();
    }
}

namespace Illuminate\Contracts\Cache;

interface Repository
{
    public function put(mixed $key, mixed $value, int $ttl = 0): bool;
}

/** Provides the deliberately incompatible candidate between the contract and concrete Repository boundaries. */
interface Store
{
    public function put(mixed $key, mixed $value, int $seconds): bool;
}

namespace Illuminate\Cache;

final class Repository implements \Illuminate\Contracts\Cache\Repository
{
    public function put(mixed $key, mixed $value, int $ttl = 0): bool
    {
        return true;
    }
}

namespace Carbon;

interface CarbonInterface
{
    public function addUTCSeconds(int|float $value): static;
}
