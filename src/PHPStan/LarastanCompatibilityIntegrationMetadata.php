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

namespace jbboehr\Yumemi\Apocrypha\PHPStan;

/**
 * Stores the unit boundaries reproduced when Larastan owns Illuminate declarations.
 *
 * @phpstan-type ArgumentBoundary array{
 *     class: non-empty-string,
 *     kind: 'constructor'|'method'|'static',
 *     method: non-empty-string,
 *     position: non-negative-int,
 *     name: non-empty-string,
 *     type: non-empty-string,
 *     majors?: non-empty-list<int>,
 *     minimumVersions?: non-empty-array<int, non-empty-string>,
 *     beforeVersions?: non-empty-array<int, non-empty-string>
 * }
 * @phpstan-type PropertyBoundary array{
 *     class: non-empty-string,
 *     property: non-empty-string,
 *     type: non-empty-string,
 *     majors?: non-empty-list<int>,
 *     minimumVersions?: non-empty-array<int, non-empty-string>,
 *     beforeVersions?: non-empty-array<int, non-empty-string>
 * }
 * @phpstan-type ReturnBoundary array{
 *     class: non-empty-string,
 *     kind: 'method'|'static',
 *     method: non-empty-string,
 *     type: non-empty-string,
 *     strategy?: 'benchmark-measure'|'benchmark-value',
 *     majors?: non-empty-list<int>,
 *     minimumVersions?: non-empty-array<int, non-empty-string>,
 *     beforeVersions?: non-empty-array<int, non-empty-string>
 * }
 * @phpstan-type IntegrationBoundaries array{
 *     arguments: list<ArgumentBoundary>,
 *     properties: list<PropertyBoundary>,
 *     returns: list<ReturnBoundary>
 * }
 *
 * @logion [SFA 80:18] The fifth archive kept no portrait of its founders, but only the winter bread they had measured
 *     for strangers; and by this the later custodians knew the covenant had been received rather than invented.
 *
 * @internal
 */
final class LarastanCompatibilityIntegrationMetadata
{
    /**
     * @var array<non-empty-string, IntegrationBoundaries>
     *
     * @logion [AWC 30:35] In the year of the ash-colored sea, seven houses brought their household lamps unto the same
     *     ruined chapel; and each flame kept its color while the common altar returned from darkness.
     */
    private const BOUNDARIES = [
        'illuminate/cache' => [
            'arguments' => [
                ['class' => 'Illuminate\\Contracts\\Cache\\Repository', 'kind' => 'method', 'method' => 'put', 'position' => 2, 'name' => 'ttl', 'type' => "unit_int<'second'>|\\DateTimeInterface|\\DateInterval|null"],
                ['class' => 'Illuminate\\Contracts\\Cache\\Repository', 'kind' => 'method', 'method' => 'add', 'position' => 2, 'name' => 'ttl', 'type' => "unit_int<'second'>|\\DateTimeInterface|\\DateInterval|null"],
                ['class' => 'Illuminate\\Contracts\\Cache\\Repository', 'kind' => 'method', 'method' => 'remember', 'position' => 1, 'name' => 'ttl', 'type' => "unit_int<'second'>|\\DateTimeInterface|\\DateInterval|\\Closure|null"],
                ['class' => 'Illuminate\\Contracts\\Cache\\Store', 'kind' => 'method', 'method' => 'put', 'position' => 2, 'name' => 'seconds', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Contracts\\Cache\\Store', 'kind' => 'method', 'method' => 'putMany', 'position' => 1, 'name' => 'seconds', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Contracts\\Cache\\Lock', 'kind' => 'method', 'method' => 'block', 'position' => 0, 'name' => 'seconds', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Contracts\\Cache\\LockProvider', 'kind' => 'method', 'method' => 'lock', 'position' => 1, 'name' => 'seconds', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Cache\\Repository', 'kind' => 'method', 'method' => 'put', 'position' => 2, 'name' => 'ttl', 'type' => "unit_int<'second'>|\\DateTimeInterface|\\DateInterval|null"],
                ['class' => 'Illuminate\\Cache\\Repository', 'kind' => 'method', 'method' => 'set', 'position' => 2, 'name' => 'ttl', 'type' => "unit_int<'second'>|\\DateTimeInterface|\\DateInterval|null"],
                ['class' => 'Illuminate\\Cache\\Repository', 'kind' => 'method', 'method' => 'putMany', 'position' => 1, 'name' => 'ttl', 'type' => "unit_int<'second'>|\\DateTimeInterface|\\DateInterval|null"],
                ['class' => 'Illuminate\\Cache\\Repository', 'kind' => 'method', 'method' => 'setMultiple', 'position' => 1, 'name' => 'ttl', 'type' => "unit_int<'second'>|\\DateTimeInterface|\\DateInterval|null"],
                ['class' => 'Illuminate\\Cache\\Repository', 'kind' => 'method', 'method' => 'add', 'position' => 2, 'name' => 'ttl', 'type' => "unit_int<'second'>|\\DateTimeInterface|\\DateInterval|null"],
                ['class' => 'Illuminate\\Cache\\Repository', 'kind' => 'method', 'method' => 'remember', 'position' => 1, 'name' => 'ttl', 'type' => "unit_int<'second'>|\\Closure|\\DateTimeInterface|\\DateInterval|null"],
                ['class' => 'Illuminate\\Cache\\Repository', 'kind' => 'method', 'method' => 'setDefaultCacheTime', 'position' => 0, 'name' => 'seconds', 'type' => "unit_int<'second'>|null"],
                ['class' => 'Illuminate\\Cache\\RateLimiter', 'kind' => 'method', 'method' => 'attempt', 'position' => 3, 'name' => 'decaySeconds', 'type' => "unit_int<'second'>|\\DateTimeInterface|\\DateInterval"],
                ['class' => 'Illuminate\\Cache\\RateLimiter', 'kind' => 'method', 'method' => 'hit', 'position' => 1, 'name' => 'decaySeconds', 'type' => "unit_int<'second'>|\\DateTimeInterface|\\DateInterval"],
                ['class' => 'Illuminate\\Cache\\RateLimiter', 'kind' => 'method', 'method' => 'increment', 'position' => 1, 'name' => 'decaySeconds', 'type' => "unit_int<'second'>|\\DateTimeInterface|\\DateInterval"],
                ['class' => 'Illuminate\\Cache\\RateLimiter', 'kind' => 'method', 'method' => 'decrement', 'position' => 1, 'name' => 'decaySeconds', 'type' => "unit_int<'second'>|\\DateTimeInterface|\\DateInterval"],
                ['class' => 'Illuminate\\Cache\\Lock', 'kind' => 'constructor', 'method' => '__construct', 'position' => 1, 'name' => 'seconds', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Cache\\Lock', 'kind' => 'method', 'method' => 'block', 'position' => 0, 'name' => 'seconds', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Cache\\Lock', 'kind' => 'method', 'method' => 'betweenBlockedAttemptsSleepFor', 'position' => 0, 'name' => 'milliseconds', 'type' => "unit_int<'millisecond'>"],
                ['class' => 'Illuminate\\Cache\\RateLimiting\\Limit', 'kind' => 'constructor', 'method' => '__construct', 'position' => 2, 'name' => 'decaySeconds', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Cache\\RateLimiting\\Limit', 'kind' => 'static', 'method' => 'perSecond', 'position' => 1, 'name' => 'decaySeconds', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Cache\\RateLimiting\\Limit', 'kind' => 'static', 'method' => 'perMinute', 'position' => 1, 'name' => 'decayMinutes', 'type' => "unit_int<'minute'>"],
                ['class' => 'Illuminate\\Cache\\RateLimiting\\Limit', 'kind' => 'static', 'method' => 'perMinutes', 'position' => 0, 'name' => 'decayMinutes', 'type' => "unit_int<'minute'>"],
                ['class' => 'Illuminate\\Cache\\RateLimiting\\Limit', 'kind' => 'static', 'method' => 'perHour', 'position' => 1, 'name' => 'decayHours', 'type' => "unit_int<'hour'>"],
                ['class' => 'Illuminate\\Cache\\RateLimiting\\Limit', 'kind' => 'static', 'method' => 'perDay', 'position' => 1, 'name' => 'decayDays', 'type' => "unit_int<'day'>"],
            ],
            'properties' => [
                ['class' => 'Illuminate\\Cache\\RateLimiting\\Limit', 'property' => 'decaySeconds', 'type' => "unit_int<'second'>"],
            ],
            'returns' => [
                ['class' => 'Illuminate\\Cache\\Repository', 'kind' => 'method', 'method' => 'getDefaultCacheTime', 'type' => "unit_int<'second'>|null"],
                ['class' => 'Illuminate\\Cache\\RateLimiter', 'kind' => 'method', 'method' => 'availableIn', 'type' => "unit_int<'second'>"],
            ],
        ],
        'illuminate/cookie' => [
            'arguments' => [
                ['class' => 'Illuminate\\Contracts\\Cookie\\Factory', 'kind' => 'method', 'method' => 'make', 'position' => 2, 'name' => 'minutes', 'type' => "unit_int<'minute'>"],
                ['class' => 'Illuminate\\Cookie\\CookieJar', 'kind' => 'method', 'method' => 'make', 'position' => 2, 'name' => 'minutes', 'type' => "unit_int<'minute'>"],
            ],
            'properties' => [],
            'returns' => [],
        ],
        'illuminate/filesystem' => [
            'arguments' => [],
            'properties' => [],
            'returns' => [
                ['class' => 'Illuminate\\Contracts\\Filesystem\\Filesystem', 'kind' => 'method', 'method' => 'size', 'type' => "unit_int<'byte'>"],
                ['class' => 'Illuminate\\Filesystem\\Filesystem', 'kind' => 'method', 'method' => 'put', 'type' => "unit_int<'byte'>|false"],
                ['class' => 'Illuminate\\Filesystem\\Filesystem', 'kind' => 'method', 'method' => 'prepend', 'type' => "unit_int<'byte'>"],
                ['class' => 'Illuminate\\Filesystem\\Filesystem', 'kind' => 'method', 'method' => 'append', 'type' => "unit_int<'byte'>"],
                ['class' => 'Illuminate\\Filesystem\\Filesystem', 'kind' => 'method', 'method' => 'size', 'type' => "unit_int<'byte'>"],
                ['class' => 'Illuminate\\Filesystem\\FilesystemAdapter', 'kind' => 'method', 'method' => 'size', 'type' => "unit_int<'byte'>"],
                ['class' => 'Illuminate\\Filesystem\\LockableFile', 'kind' => 'method', 'method' => 'size', 'type' => "unit_int<'byte'>"],
            ],
        ],
        'illuminate/http' => [
            'arguments' => [
                ['class' => 'Illuminate\\Http\\Client\\PendingRequest', 'kind' => 'method', 'method' => 'timeout', 'position' => 0, 'name' => 'seconds', 'type' => "unit_int<'second'>", 'majors' => [11], 'beforeVersions' => [11 => '11.35.1']],
                ['class' => 'Illuminate\\Http\\Client\\PendingRequest', 'kind' => 'method', 'method' => 'connectTimeout', 'position' => 0, 'name' => 'seconds', 'type' => "unit_int<'second'>", 'majors' => [11], 'beforeVersions' => [11 => '11.35.1']],
                ['class' => 'Illuminate\\Http\\Client\\PendingRequest', 'kind' => 'method', 'method' => 'timeout', 'position' => 0, 'name' => 'seconds', 'type' => "unit_int<'second'>|unit_float<'second'>", 'majors' => [11, 12, 13], 'minimumVersions' => [11 => '11.35.1']],
                ['class' => 'Illuminate\\Http\\Client\\PendingRequest', 'kind' => 'method', 'method' => 'connectTimeout', 'position' => 0, 'name' => 'seconds', 'type' => "unit_int<'second'>|unit_float<'second'>", 'majors' => [11, 12, 13], 'minimumVersions' => [11 => '11.35.1']],
                ['class' => 'Illuminate\\Http\\Client\\PendingRequest', 'kind' => 'method', 'method' => 'retry', 'position' => 0, 'name' => 'times', 'type' => "array<int, unit_int<'millisecond'>>|int"],
                ['class' => 'Illuminate\\Http\\Client\\PendingRequest', 'kind' => 'method', 'method' => 'retry', 'position' => 1, 'name' => 'sleepMilliseconds', 'type' => "(\\Closure(int, mixed): unit_int<'millisecond'>)|unit_int<'millisecond'>"],
                ['class' => 'Illuminate\\Http\\Testing\\FileFactory', 'kind' => 'method', 'method' => 'create', 'position' => 1, 'name' => 'kilobytes', 'type' => "string|unit_int<'1024 * byte'>"],
                ['class' => 'Illuminate\\Http\\Testing\\File', 'kind' => 'static', 'method' => 'create', 'position' => 1, 'name' => 'kilobytes', 'type' => "string|unit_int<'1024 * byte'>"],
                ['class' => 'Illuminate\\Http\\Testing\\File', 'kind' => 'method', 'method' => 'size', 'position' => 0, 'name' => 'kilobytes', 'type' => "unit_int<'1024 * byte'>"],
            ],
            'properties' => [],
            'returns' => [
                ['class' => 'Illuminate\\Http\\Testing\\File', 'kind' => 'method', 'method' => 'getSize', 'type' => "unit_int<'byte'>"],
            ],
        ],
        'illuminate/process' => [
            'arguments' => [
                ['class' => 'Illuminate\\Process\\PendingProcess', 'kind' => 'method', 'method' => 'timeout', 'position' => 0, 'name' => 'timeout', 'type' => "unit_int<'second'>", 'majors' => [11, 12]],
                ['class' => 'Illuminate\\Process\\PendingProcess', 'kind' => 'method', 'method' => 'idleTimeout', 'position' => 0, 'name' => 'timeout', 'type' => "unit_int<'second'>", 'majors' => [11, 12]],
                ['class' => 'Illuminate\\Process\\PendingProcess', 'kind' => 'method', 'method' => 'timeout', 'position' => 0, 'name' => 'timeout', 'type' => "\\Carbon\\CarbonInterval|unit_int<'second'>", 'majors' => [13]],
                ['class' => 'Illuminate\\Process\\PendingProcess', 'kind' => 'method', 'method' => 'idleTimeout', 'position' => 0, 'name' => 'timeout', 'type' => "\\Carbon\\CarbonInterval|unit_int<'second'>", 'majors' => [13]],
            ],
            'properties' => [],
            'returns' => [],
        ],
        'illuminate/queue' => [
            'arguments' => [
                ['class' => 'Illuminate\\Contracts\\Queue\\Queue', 'kind' => 'method', 'method' => 'later', 'position' => 0, 'name' => 'delay', 'type' => "\\DateTimeInterface|\\DateInterval|unit_int<'second'>"],
                ['class' => 'Illuminate\\Contracts\\Queue\\Queue', 'kind' => 'method', 'method' => 'laterOn', 'position' => 1, 'name' => 'delay', 'type' => "\\DateTimeInterface|\\DateInterval|unit_int<'second'>"],
                ['class' => 'Illuminate\\Contracts\\Queue\\Job', 'kind' => 'method', 'method' => 'release', 'position' => 0, 'name' => 'delay', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Queue\\Jobs\\Job', 'kind' => 'method', 'method' => 'release', 'position' => 0, 'name' => 'delay', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Queue\\InteractsWithQueue', 'kind' => 'method', 'method' => 'release', 'position' => 0, 'name' => 'delay', 'type' => "\\DateTimeInterface|\\DateInterval|unit_int<'second'>"],
                ['class' => 'Illuminate\\Queue\\WorkerOptions', 'kind' => 'constructor', 'method' => '__construct', 'position' => 1, 'name' => 'backoff', 'type' => "unit_int<'second'>|array<unit_int<'second'>>"],
                ['class' => 'Illuminate\\Queue\\WorkerOptions', 'kind' => 'constructor', 'method' => '__construct', 'position' => 2, 'name' => 'memory', 'type' => "unit_int<'1048576 * byte'>"],
                ['class' => 'Illuminate\\Queue\\WorkerOptions', 'kind' => 'constructor', 'method' => '__construct', 'position' => 3, 'name' => 'timeout', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Queue\\WorkerOptions', 'kind' => 'constructor', 'method' => '__construct', 'position' => 4, 'name' => 'sleep', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Queue\\WorkerOptions', 'kind' => 'constructor', 'method' => '__construct', 'position' => 9, 'name' => 'maxTime', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Queue\\WorkerOptions', 'kind' => 'constructor', 'method' => '__construct', 'position' => 10, 'name' => 'rest', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Queue\\WorkerOptions', 'kind' => 'constructor', 'method' => '__construct', 'position' => 11, 'name' => 'stopWhenEmptyFor', 'type' => "unit_int<'second'>", 'majors' => [11, 12, 13], 'minimumVersions' => [11 => '11.53.0', 12 => '12.60.0', 13 => '13.10.0']],
            ],
            'properties' => [
                ['class' => 'Illuminate\\Queue\\WorkerOptions', 'property' => 'backoff', 'type' => "unit_int<'second'>|array<unit_int<'second'>>"],
                ['class' => 'Illuminate\\Queue\\WorkerOptions', 'property' => 'memory', 'type' => "unit_int<'1048576 * byte'>"],
                ['class' => 'Illuminate\\Queue\\WorkerOptions', 'property' => 'timeout', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Queue\\WorkerOptions', 'property' => 'sleep', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Queue\\WorkerOptions', 'property' => 'rest', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Queue\\WorkerOptions', 'property' => 'maxTime', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Queue\\WorkerOptions', 'property' => 'stopWhenEmptyFor', 'type' => "unit_int<'second'>", 'majors' => [11, 12, 13], 'minimumVersions' => [11 => '11.53.0', 12 => '12.60.0', 13 => '13.10.0']],
            ],
            'returns' => [],
        ],
        'illuminate/support' => [
            'arguments' => [
                ['class' => 'Illuminate\\Support\\Sleep', 'kind' => 'static', 'method' => 'usleep', 'position' => 0, 'name' => 'duration', 'type' => "unit_int<'microsecond'>"],
                ['class' => 'Illuminate\\Support\\Sleep', 'kind' => 'static', 'method' => 'sleep', 'position' => 0, 'name' => 'duration', 'type' => "unit_int<'second'>|unit_float<'second'>"],
                ['class' => 'Illuminate\\Support\\Timebox', 'kind' => 'method', 'method' => 'call', 'position' => 1, 'name' => 'microseconds', 'type' => "unit_int<'microsecond'>"],
            ],
            'properties' => [],
            'returns' => [
                ['class' => 'Illuminate\\Support\\Benchmark', 'kind' => 'static', 'method' => 'measure', 'type' => "(TBenchmarkables is \\Closure ? unit_float<'millisecond'> : array<unit_float<'millisecond'>>)", 'strategy' => 'benchmark-measure'],
                ['class' => 'Illuminate\\Support\\Benchmark', 'kind' => 'static', 'method' => 'value', 'type' => "array{0: TReturn, 1: unit_float<'millisecond'>}", 'strategy' => 'benchmark-value'],
            ],
        ],
    ];

    /**
     * Returns the complete immutable compatibility catalog.
     *
     * @return array<non-empty-string, IntegrationBoundaries>
     *
     * @logion [RAS 22:54] Above the electric sea there appeared a stair of unmoving fire, and each pilgrim beheld a
     *     different height; nevertheless the summit received their songs as one thanksgiving.
     */
    public static function all(): array
    {
        return self::BOUNDARIES;
    }

    /**
     * Determines whether a boundary belongs to an installed package version.
     *
     * @param array{
     *     majors?: non-empty-list<int>,
     *     minimumVersions?: non-empty-array<int, non-empty-string>,
     *     beforeVersions?: non-empty-array<int, non-empty-string>,
     *     ...
     * } $boundary
     *
     * @logion [SFA 69:59] The old gate refuseth neither spring nor autumn, yet it openeth each season beneath its own
     *     sign; for constancy is not blindness unto the appointed hour.
     */
    public static function supportsVersion(array $boundary, int $major, string $version): bool
    {
        if (isset($boundary['majors']) && !in_array($major, $boundary['majors'], true)) {
            return false;
        }

        $minimumVersion = $boundary['minimumVersions'][$major] ?? null;
        $beforeVersion = $boundary['beforeVersions'][$major] ?? null;
        $normalizedVersion = ltrim($version, 'v');
        $developmentVersion = $normalizedVersion === $major . '.x-dev';

        return ($minimumVersion === null || $developmentVersion || version_compare($normalizedVersion, $minimumVersion, '>='))
            && ($beforeVersion === null || (!$developmentVersion && version_compare($normalizedVersion, $beforeVersion, '<')));
    }
}
