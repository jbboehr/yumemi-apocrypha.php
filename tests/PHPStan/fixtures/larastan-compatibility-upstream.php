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

namespace Illuminate\Database;

final class Connection
{
    public function logQuery(string $query, array $bindings, ?float $time = null): void
    {
    }

    public function whenQueryingForLongerThan(\DateTimeInterface|\Carbon\CarbonInterval|float|int $threshold, callable $handler): void
    {
    }

    public function totalQueryDuration(): float
    {
        return 1.0;
    }
}

namespace Illuminate\Database\Events;

final class QueryExecuted
{
    public ?float $time;

    /** @param float|null $time */
    public function __construct($sql, $bindings, $time, \Illuminate\Database\Connection $connection)
    {
        $this->time = $time;
    }
}

namespace Illuminate\Database\Query;

final class Builder
{
    public ?int $timeout = null;

    public function timeout(?int $seconds): static
    {
        return $this;
    }
}

namespace Illuminate\Bus;

trait Queueable
{
    public \DateTimeInterface|\DateInterval|array|int|null $delay = null;

    public function delay(\DateTimeInterface|\DateInterval|array|int|null $delay = null): static
    {
        $this->delay = $delay;

        return $this;
    }
}

namespace Illuminate\Foundation\Queue;

trait Queueable
{
    use \Illuminate\Bus\Queueable;
}

namespace jbboehr\Yumemi\Apocrypha\Tests\PHPStan\Fixtures;

abstract class InheritedQueueableBaseJob
{
    use \Illuminate\Foundation\Queue\Queueable;
}

final class InheritedQueueableJob extends InheritedQueueableBaseJob
{
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

namespace Illuminate\Redis\Limiters;

final class DurationLimiterBuilder
{
    /** @var int */
    public $decay = 0;

    /** @var int */
    public $timeout = 3;

    /** @var int */
    public $sleep = 750;

    /** @param \DateTimeInterface|\DateInterval|int $decay */
    public function every($decay): self
    {
        return $this;
    }

    /** @param int $timeout */
    public function block($timeout): self
    {
        return $this;
    }

    /** @param int $sleep */
    public function sleep($sleep): self
    {
        return $this;
    }
}

namespace Illuminate\Redis\Events;

final class CommandExecuted
{
    /** @var float */
    public $time;

    /** @param float|null $time */
    public function __construct($command, $parameters, $time, $connection)
    {
        $this->time = $time;
    }
}

namespace Illuminate\Session;

final class ArraySessionHandler
{
    public function __construct(int $minutes)
    {
    }

    public function gc(int $lifetime): int
    {
        return 0;
    }
}

final class NullSessionHandler
{
    public function gc(int $lifetime): int
    {
        return 0;
    }
}

final class SessionManager
{
    public function defaultRouteBlockLockSeconds(): int
    {
        return 10;
    }

    public function defaultRouteBlockWaitSeconds(): int
    {
        return 10;
    }
}

final class SymfonySessionDecorator
{
    public function invalidate(?int $lifetime = null): bool
    {
        return true;
    }

    public function migrate(bool $destroy = false, ?int $lifetime = null): bool
    {
        return true;
    }
}

namespace Illuminate\Contracts\Routing;

interface UrlGenerator
{
    public function signedRoute($name, $parameters = [], \DateTimeInterface|\DateInterval|int|null $expiration = null, $absolute = true): string;

    public function temporarySignedRoute($name, \DateTimeInterface|\DateInterval|int $expiration, $parameters = [], $absolute = true): string;
}

namespace Illuminate\Routing;

final class Route
{
    public function block(?int $lockSeconds = 10, ?int $waitSeconds = 10): self
    {
        return $this;
    }

    public function locksFor(): ?int
    {
        return 10;
    }

    public function middleware(string|array|null $middleware = null): self|array
    {
        return $middleware === null ? [] : $this;
    }

    public function waitsFor(): ?int
    {
        return 10;
    }
}

final class UrlGenerator implements \Illuminate\Contracts\Routing\UrlGenerator
{
    public function signedRoute($name, $parameters = [], \DateTimeInterface|\DateInterval|int|null $expiration = null, $absolute = true): string
    {
        return '/signed';
    }

    public function temporarySignedRoute($name, \DateTimeInterface|\DateInterval|int $expiration, $parameters = [], $absolute = true): string
    {
        return '/signed';
    }
}

final class Redirector
{
    public function signedRoute($route, $parameters = [], \DateTimeInterface|\DateInterval|int|null $expiration = null, $status = 302, $headers = []): string
    {
        return '/signed';
    }

    public function temporarySignedRoute($route, \DateTimeInterface|\DateInterval|int|null $expiration, $parameters = [], $status = 302, $headers = []): string
    {
        return '/signed';
    }
}

namespace Illuminate\Routing\Middleware;

final class ThrottleRequests
{
    public static function with($maxAttempts = 60, int $decayMinutes = 1, $prefix = ''): string
    {
        return '';
    }

    public function handle($request, \Closure $next, $maxAttempts = 60, int|float $decayMinutes = 1, $prefix = ''): mixed
    {
        return $next($request);
    }
}

namespace Illuminate\Support\Facades;

final class Cache
{
    public static function getDefaultCacheTime(): ?int
    {
        return null;
    }
}

final class URL
{
    public static function signedRoute($name, $parameters = [], \DateTimeInterface|\DateInterval|int|null $expiration = null, $absolute = true): string
    {
        return '/signed';
    }

    public static function temporarySignedRoute($name, \DateTimeInterface|\DateInterval|int $expiration, $parameters = [], $absolute = true): string
    {
        return '/signed';
    }
}

namespace Illuminate\Validation\Rules;

final class Dimensions
{
    public function width(int $value): self
    {
        return $this;
    }

    public function height(int $value): self
    {
        return $this;
    }

    public function minWidth(int $value): self
    {
        return $this;
    }

    public function minHeight(int $value): self
    {
        return $this;
    }

    public function maxWidth(int $value): self
    {
        return $this;
    }

    public function maxHeight(int $value): self
    {
        return $this;
    }
}

class File
{
    public function size(string|int $size): self
    {
        return $this;
    }

    public function between(string|int $minSize, string|int $maxSize): self
    {
        return $this;
    }

    public function min(string|int $size): self
    {
        return $this;
    }

    public function max(string|int $size): self
    {
        return $this;
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
