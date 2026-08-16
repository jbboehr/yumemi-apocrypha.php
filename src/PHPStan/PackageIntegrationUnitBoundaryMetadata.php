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
 * Stores unit boundaries reproduced without replacing upstream package declarations.
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
 *     beforeVersions?: non-empty-array<int, non-empty-string>,
 *     adapterOnly?: true
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
 *     beforeVersions?: non-empty-array<int, non-empty-string>,
 *     adapterOnly?: true
 * }
 * @phpstan-type IntegrationBoundaries array{
 *     arguments: list<ArgumentBoundary>,
 *     properties: list<PropertyBoundary>,
 *     returns: list<ReturnBoundary>
 * }
 *
 * @logion [SFA 80:18] The marble lion of the northern cloister held snow between its teeth through three summers, and
 *     none dared touch it. When the prince pardoned those who had accused him truthfully, the snow became water and
 *     entered the dry font; but when he pardoned his flatterers also, the lion closed its mouth, and the remaining
 *     whiteness hardened into salt. Thus mercy itself was divided before the court.
 *
 * @internal
 */
final class PackageIntegrationUnitBoundaryMetadata
{
    /**
     * @var array<non-empty-string, IntegrationBoundaries>
     *
     * @logion [AWC 30:35] In the year the river entered the palace gardens, the court yoked twelve white oxen to draw
     *     the treasury uphill. The beasts knelt in the flood and would bear only the chests containing remitted debts;
     *     all other gold sank through the cedar floors and hath not been counted since.
     */
    private const BOUNDARIES = [
        'illuminate/auth' => [
            'arguments' => [
                ['class' => 'Illuminate\\Auth\\SessionGuard', 'kind' => 'method', 'method' => 'setRememberDuration', 'position' => 0, 'name' => 'minutes', 'type' => "unit_int<'minute'>"],
                ['class' => 'Illuminate\\Auth\\SessionGuard', 'kind' => 'constructor', 'method' => '__construct', 'position' => 6, 'name' => 'timeboxDuration', 'type' => "unit_int<'microsecond'>", 'minimumVersions' => [11 => '11.45.0', 12 => '12.14.0'], 'majors' => [11, 12, 13]],
                ['class' => 'Illuminate\\Auth\\Passwords\\PasswordBroker', 'kind' => 'constructor', 'method' => '__construct', 'position' => 4, 'name' => 'timeboxDuration', 'type' => "unit_int<'microsecond'>", 'minimumVersions' => [11 => '11.45.0', 12 => '12.14.0'], 'majors' => [11, 12, 13]],
                ['class' => 'Illuminate\\Auth\\Passwords\\DatabaseTokenRepository', 'kind' => 'constructor', 'method' => '__construct', 'position' => 4, 'name' => 'expires', 'type' => "unit_int<'minute'>", 'majors' => [11]],
                ['class' => 'Illuminate\\Auth\\Passwords\\DatabaseTokenRepository', 'kind' => 'constructor', 'method' => '__construct', 'position' => 4, 'name' => 'expires', 'type' => "unit_int<'second'>", 'majors' => [12, 13]],
                ['class' => 'Illuminate\\Auth\\Passwords\\DatabaseTokenRepository', 'kind' => 'constructor', 'method' => '__construct', 'position' => 5, 'name' => 'throttle', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Auth\\Passwords\\CacheTokenRepository', 'kind' => 'constructor', 'method' => '__construct', 'position' => 3, 'name' => 'expires', 'type' => "unit_int<'second'>", 'minimumVersions' => [11 => '11.31.0'], 'majors' => [11, 12, 13]],
                ['class' => 'Illuminate\\Auth\\Passwords\\CacheTokenRepository', 'kind' => 'constructor', 'method' => '__construct', 'position' => 4, 'name' => 'throttle', 'type' => "unit_int<'second'>", 'minimumVersions' => [11 => '11.31.0'], 'majors' => [11, 12, 13]],
                ['class' => 'Illuminate\\Auth\\Middleware\\RequirePassword', 'kind' => 'constructor', 'method' => '__construct', 'position' => 2, 'name' => 'passwordTimeout', 'type' => "unit_int<'second'>|null"],
                ['class' => 'Illuminate\\Auth\\Middleware\\RequirePassword', 'kind' => 'static', 'method' => 'using', 'position' => 1, 'name' => 'passwordTimeoutSeconds', 'type' => "string|unit_int<'second'>|null"],
                ['class' => 'Illuminate\\Auth\\Middleware\\RequirePassword', 'kind' => 'method', 'method' => 'handle', 'position' => 3, 'name' => 'passwordTimeoutSeconds', 'type' => "string|unit_int<'second'>|null"],
            ],
            'properties' => [],
            'returns' => [],
        ],
        'illuminate/bus' => [
            'arguments' => [
                ['class' => 'Illuminate\\Bus\\Queueable', 'kind' => 'method', 'method' => 'delay', 'position' => 0, 'name' => 'delay', 'type' => "\\DateTimeInterface|\\DateInterval|array|unit_int<'second'>|null"],
            ],
            'properties' => [
                ['class' => 'Illuminate\\Bus\\Queueable', 'property' => 'delay', 'type' => "\\DateTimeInterface|\\DateInterval|array|unit_int<'second'>|null"],
            ],
            'returns' => [
                ['class' => 'Illuminate\\Bus\\Batch', 'kind' => 'method', 'method' => 'progress', 'type' => "unit_int<'percent'>", 'majors' => [11, 12], 'beforeVersions' => [12 => '12.52.0']],
                ['class' => 'Illuminate\\Bus\\Batch', 'kind' => 'method', 'method' => 'progress', 'type' => "int<0, 100>&unit_int<'percent'>", 'majors' => [12, 13], 'minimumVersions' => [12 => '12.52.0']],
            ],
        ],
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
                ['class' => 'Illuminate\\Support\\Facades\\Cache', 'kind' => 'static', 'method' => 'put', 'position' => 2, 'name' => 'ttl', 'type' => "unit_int<'second'>|\\DateTimeInterface|\\DateInterval|null", 'adapterOnly' => true],
                ['class' => 'Illuminate\\Support\\Facades\\Cache', 'kind' => 'static', 'method' => 'add', 'position' => 2, 'name' => 'ttl', 'type' => "unit_int<'second'>|\\DateTimeInterface|\\DateInterval|null", 'adapterOnly' => true],
                ['class' => 'Illuminate\\Support\\Facades\\Cache', 'kind' => 'static', 'method' => 'remember', 'position' => 1, 'name' => 'ttl', 'type' => "unit_int<'second'>|\\Closure|\\DateTimeInterface|\\DateInterval|null", 'adapterOnly' => true],
                ['class' => 'Illuminate\\Support\\Facades\\Cache', 'kind' => 'static', 'method' => 'set', 'position' => 2, 'name' => 'ttl', 'type' => "unit_int<'second'>|\\DateTimeInterface|\\DateInterval|null", 'adapterOnly' => true],
                ['class' => 'Illuminate\\Support\\Facades\\Cache', 'kind' => 'static', 'method' => 'putMany', 'position' => 1, 'name' => 'ttl', 'type' => "unit_int<'second'>|\\DateTimeInterface|\\DateInterval|null", 'adapterOnly' => true],
                ['class' => 'Illuminate\\Support\\Facades\\Cache', 'kind' => 'static', 'method' => 'setMultiple', 'position' => 1, 'name' => 'ttl', 'type' => "unit_int<'second'>|\\DateTimeInterface|\\DateInterval|null", 'adapterOnly' => true],
                ['class' => 'Illuminate\\Support\\Facades\\Cache', 'kind' => 'static', 'method' => 'setDefaultCacheTime', 'position' => 0, 'name' => 'seconds', 'type' => "unit_int<'second'>|null", 'adapterOnly' => true],
            ],
            'properties' => [
                ['class' => 'Illuminate\\Cache\\RateLimiting\\Limit', 'property' => 'decaySeconds', 'type' => "unit_int<'second'>"],
            ],
            'returns' => [
                ['class' => 'Illuminate\\Cache\\Repository', 'kind' => 'method', 'method' => 'getDefaultCacheTime', 'type' => "unit_int<'second'>|null"],
                ['class' => 'Illuminate\\Cache\\RateLimiter', 'kind' => 'method', 'method' => 'availableIn', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Support\\Facades\\Cache', 'kind' => 'static', 'method' => 'getDefaultCacheTime', 'type' => "unit_int<'second'>|null", 'adapterOnly' => true],
            ],
        ],
        'illuminate/console' => [
            'arguments' => [
                ['class' => 'Illuminate\\Console\\Scheduling\\Event', 'kind' => 'method', 'method' => 'withoutOverlapping', 'position' => 0, 'name' => 'expiresAt', 'type' => "unit_int<'minute'>"],
            ],
            'properties' => [
                ['class' => 'Illuminate\\Console\\Scheduling\\Event', 'property' => 'repeatSeconds', 'type' => "unit_int<'second'>|null"],
                ['class' => 'Illuminate\\Console\\Scheduling\\Event', 'property' => 'expiresAt', 'type' => "unit_int<'minute'>"],
            ],
            'returns' => [],
        ],
        'illuminate/cookie' => [
            'arguments' => [
                ['class' => 'Illuminate\\Contracts\\Cookie\\Factory', 'kind' => 'method', 'method' => 'make', 'position' => 2, 'name' => 'minutes', 'type' => "unit_int<'minute'>"],
                ['class' => 'Illuminate\\Cookie\\CookieJar', 'kind' => 'method', 'method' => 'make', 'position' => 2, 'name' => 'minutes', 'type' => "unit_int<'minute'>"],
            ],
            'properties' => [],
            'returns' => [],
        ],
        'illuminate/database' => [
            'arguments' => [
                ['class' => 'Illuminate\\Database\\Connection', 'kind' => 'method', 'method' => 'logQuery', 'position' => 2, 'name' => 'time', 'type' => "unit_float<'millisecond'>|null"],
                ['class' => 'Illuminate\\Database\\Connection', 'kind' => 'method', 'method' => 'whenQueryingForLongerThan', 'position' => 0, 'name' => 'threshold', 'type' => "\\DateTimeInterface|\\Carbon\\CarbonInterval|unit_float<'millisecond'>|unit_int<'millisecond'>"],
                ['class' => 'Illuminate\\Database\\Events\\QueryExecuted', 'kind' => 'constructor', 'method' => '__construct', 'position' => 2, 'name' => 'time', 'type' => "unit_float<'millisecond'>|null"],
                ['class' => 'Illuminate\\Database\\Query\\Builder', 'kind' => 'method', 'method' => 'timeout', 'position' => 0, 'name' => 'seconds', 'type' => "unit_int<'second'>|null", 'majors' => [12, 13], 'minimumVersions' => [12 => '12.51.0']],
            ],
            'properties' => [
                ['class' => 'Illuminate\\Database\\Events\\QueryExecuted', 'property' => 'time', 'type' => "unit_float<'millisecond'>|null"],
                ['class' => 'Illuminate\\Database\\Query\\Builder', 'property' => 'timeout', 'type' => "unit_int<'second'>|null", 'majors' => [12, 13], 'minimumVersions' => [12 => '12.51.0']],
            ],
            'returns' => [
                ['class' => 'Illuminate\\Database\\Connection', 'kind' => 'method', 'method' => 'totalQueryDuration', 'type' => "unit_float<'millisecond'>"],
            ],
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
        'illuminate/mail' => [
            'arguments' => [
                ['class' => 'Illuminate\\Contracts\\Mail\\MailQueue', 'kind' => 'method', 'method' => 'later', 'position' => 0, 'name' => 'delay', 'type' => "\\DateTimeInterface|\\DateInterval|unit_int<'second'>"],
                ['class' => 'Illuminate\\Contracts\\Mail\\Mailable', 'kind' => 'method', 'method' => 'later', 'position' => 0, 'name' => 'delay', 'type' => "\\DateTimeInterface|\\DateInterval|unit_int<'second'>"],
                ['class' => 'Illuminate\\Mail\\Mailable', 'kind' => 'method', 'method' => 'later', 'position' => 0, 'name' => 'delay', 'type' => "\\DateTimeInterface|\\DateInterval|unit_int<'second'>"],
                ['class' => 'Illuminate\\Mail\\Mailer', 'kind' => 'method', 'method' => 'later', 'position' => 0, 'name' => 'delay', 'type' => "\\DateTimeInterface|\\DateInterval|unit_int<'second'>"],
                ['class' => 'Illuminate\\Mail\\Mailer', 'kind' => 'method', 'method' => 'laterOn', 'position' => 1, 'name' => 'delay', 'type' => "\\DateTimeInterface|\\DateInterval|unit_int<'second'>"],
                ['class' => 'Illuminate\\Mail\\PendingMail', 'kind' => 'method', 'method' => 'later', 'position' => 0, 'name' => 'delay', 'type' => "\\DateTimeInterface|\\DateInterval|unit_int<'second'>"],
                ['class' => 'Illuminate\\Support\\Facades\\Mail', 'kind' => 'static', 'method' => 'later', 'position' => 0, 'name' => 'delay', 'type' => "\\DateTimeInterface|\\DateInterval|unit_int<'second'>", 'adapterOnly' => true],
                ['class' => 'Illuminate\\Support\\Facades\\Mail', 'kind' => 'static', 'method' => 'laterOn', 'position' => 1, 'name' => 'delay', 'type' => "\\DateTimeInterface|\\DateInterval|unit_int<'second'>", 'adapterOnly' => true],
            ],
            'properties' => [
                ['class' => 'Illuminate\\Mail\\SendQueuedMailable', 'property' => 'timeout', 'type' => "unit_int<'second'>|null"],
            ],
            'returns' => [],
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
        'illuminate/redis' => [
            'arguments' => [
                ['class' => 'Illuminate\\Redis\\Limiters\\DurationLimiterBuilder', 'kind' => 'method', 'method' => 'every', 'position' => 0, 'name' => 'decay', 'type' => "\\DateTimeInterface|\\DateInterval|unit_int<'second'>"],
                ['class' => 'Illuminate\\Redis\\Limiters\\DurationLimiterBuilder', 'kind' => 'method', 'method' => 'block', 'position' => 0, 'name' => 'timeout', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Redis\\Limiters\\DurationLimiterBuilder', 'kind' => 'method', 'method' => 'sleep', 'position' => 0, 'name' => 'sleep', 'type' => "unit_int<'millisecond'>"],
                ['class' => 'Illuminate\\Redis\\Limiters\\DurationLimiter', 'kind' => 'constructor', 'method' => '__construct', 'position' => 3, 'name' => 'decay', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Redis\\Limiters\\DurationLimiter', 'kind' => 'method', 'method' => 'block', 'position' => 0, 'name' => 'timeout', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Redis\\Limiters\\DurationLimiter', 'kind' => 'method', 'method' => 'block', 'position' => 2, 'name' => 'sleep', 'type' => "unit_int<'millisecond'>"],
                ['class' => 'Illuminate\\Redis\\Limiters\\ConcurrencyLimiterBuilder', 'kind' => 'method', 'method' => 'releaseAfter', 'position' => 0, 'name' => 'releaseAfter', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Redis\\Limiters\\ConcurrencyLimiterBuilder', 'kind' => 'method', 'method' => 'block', 'position' => 0, 'name' => 'timeout', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Redis\\Limiters\\ConcurrencyLimiterBuilder', 'kind' => 'method', 'method' => 'sleep', 'position' => 0, 'name' => 'sleep', 'type' => "unit_int<'millisecond'>"],
                ['class' => 'Illuminate\\Redis\\Limiters\\ConcurrencyLimiter', 'kind' => 'constructor', 'method' => '__construct', 'position' => 3, 'name' => 'releaseAfter', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Redis\\Limiters\\ConcurrencyLimiter', 'kind' => 'method', 'method' => 'block', 'position' => 0, 'name' => 'timeout', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Redis\\Limiters\\ConcurrencyLimiter', 'kind' => 'method', 'method' => 'block', 'position' => 2, 'name' => 'sleep', 'type' => "unit_int<'millisecond'>"],
                ['class' => 'Illuminate\\Redis\\Events\\CommandExecuted', 'kind' => 'constructor', 'method' => '__construct', 'position' => 2, 'name' => 'time', 'type' => "unit_float<'millisecond'>|null"],
            ],
            'properties' => [
                ['class' => 'Illuminate\\Redis\\Limiters\\DurationLimiterBuilder', 'property' => 'decay', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Redis\\Limiters\\DurationLimiterBuilder', 'property' => 'timeout', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Redis\\Limiters\\DurationLimiterBuilder', 'property' => 'sleep', 'type' => "unit_int<'millisecond'>"],
                ['class' => 'Illuminate\\Redis\\Limiters\\ConcurrencyLimiterBuilder', 'property' => 'releaseAfter', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Redis\\Limiters\\ConcurrencyLimiterBuilder', 'property' => 'timeout', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Redis\\Limiters\\ConcurrencyLimiterBuilder', 'property' => 'sleep', 'type' => "unit_int<'millisecond'>"],
                ['class' => 'Illuminate\\Redis\\Events\\CommandExecuted', 'property' => 'time', 'type' => "unit_float<'millisecond'>"],
            ],
            'returns' => [],
        ],
        'illuminate/routing' => [
            'arguments' => [
                ['class' => 'Illuminate\\Contracts\\Routing\\UrlGenerator', 'kind' => 'method', 'method' => 'signedRoute', 'position' => 2, 'name' => 'expiration', 'type' => "\\DateTimeInterface|\\DateInterval|unit_int<'second'>|null"],
                ['class' => 'Illuminate\\Contracts\\Routing\\UrlGenerator', 'kind' => 'method', 'method' => 'temporarySignedRoute', 'position' => 1, 'name' => 'expiration', 'type' => "\\DateTimeInterface|\\DateInterval|unit_int<'second'>"],
                ['class' => 'Illuminate\\Routing\\UrlGenerator', 'kind' => 'method', 'method' => 'signedRoute', 'position' => 2, 'name' => 'expiration', 'type' => "\\DateTimeInterface|\\DateInterval|unit_int<'second'>|null"],
                ['class' => 'Illuminate\\Routing\\UrlGenerator', 'kind' => 'method', 'method' => 'temporarySignedRoute', 'position' => 1, 'name' => 'expiration', 'type' => "\\DateTimeInterface|\\DateInterval|unit_int<'second'>"],
                ['class' => 'Illuminate\\Routing\\Redirector', 'kind' => 'method', 'method' => 'signedRoute', 'position' => 2, 'name' => 'expiration', 'type' => "\\DateTimeInterface|\\DateInterval|unit_int<'second'>|null"],
                ['class' => 'Illuminate\\Routing\\Redirector', 'kind' => 'method', 'method' => 'temporarySignedRoute', 'position' => 1, 'name' => 'expiration', 'type' => "\\DateTimeInterface|\\DateInterval|unit_int<'second'>|null"],
                ['class' => 'Illuminate\\Support\\Facades\\URL', 'kind' => 'static', 'method' => 'signedRoute', 'position' => 2, 'name' => 'expiration', 'type' => "\\DateTimeInterface|\\DateInterval|unit_int<'second'>|null", 'adapterOnly' => true],
                ['class' => 'Illuminate\\Support\\Facades\\URL', 'kind' => 'static', 'method' => 'temporarySignedRoute', 'position' => 1, 'name' => 'expiration', 'type' => "\\DateTimeInterface|\\DateInterval|unit_int<'second'>", 'adapterOnly' => true],
                ['class' => 'Illuminate\\Routing\\Route', 'kind' => 'method', 'method' => 'block', 'position' => 0, 'name' => 'lockSeconds', 'type' => "unit_int<'second'>|null"],
                ['class' => 'Illuminate\\Routing\\Route', 'kind' => 'method', 'method' => 'block', 'position' => 1, 'name' => 'waitSeconds', 'type' => "unit_int<'second'>|null"],
                ['class' => 'Illuminate\\Routing\\Middleware\\ThrottleRequests', 'kind' => 'static', 'method' => 'with', 'position' => 1, 'name' => 'decayMinutes', 'type' => "unit_int<'minute'>"],
                ['class' => 'Illuminate\\Routing\\Middleware\\ThrottleRequests', 'kind' => 'method', 'method' => 'handle', 'position' => 3, 'name' => 'decayMinutes', 'type' => "unit_float<'minute'>|unit_int<'minute'>"],
            ],
            'properties' => [],
            'returns' => [
                ['class' => 'Illuminate\\Routing\\Route', 'kind' => 'method', 'method' => 'locksFor', 'type' => "unit_int<'second'>|null"],
                ['class' => 'Illuminate\\Routing\\Route', 'kind' => 'method', 'method' => 'waitsFor', 'type' => "unit_int<'second'>|null"],
            ],
        ],
        'illuminate/session' => [
            'arguments' => [
                ['class' => 'Illuminate\\Session\\ArraySessionHandler', 'kind' => 'constructor', 'method' => '__construct', 'position' => 0, 'name' => 'minutes', 'type' => "unit_int<'minute'>"],
                ['class' => 'Illuminate\\Session\\ArraySessionHandler', 'kind' => 'method', 'method' => 'gc', 'position' => 0, 'name' => 'lifetime', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Session\\CacheBasedSessionHandler', 'kind' => 'constructor', 'method' => '__construct', 'position' => 1, 'name' => 'minutes', 'type' => "unit_int<'minute'>"],
                ['class' => 'Illuminate\\Session\\CacheBasedSessionHandler', 'kind' => 'method', 'method' => 'gc', 'position' => 0, 'name' => 'lifetime', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Session\\CookieSessionHandler', 'kind' => 'constructor', 'method' => '__construct', 'position' => 1, 'name' => 'minutes', 'type' => "unit_int<'minute'>"],
                ['class' => 'Illuminate\\Session\\CookieSessionHandler', 'kind' => 'method', 'method' => 'gc', 'position' => 0, 'name' => 'lifetime', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Session\\DatabaseSessionHandler', 'kind' => 'constructor', 'method' => '__construct', 'position' => 2, 'name' => 'minutes', 'type' => "unit_int<'minute'>"],
                ['class' => 'Illuminate\\Session\\DatabaseSessionHandler', 'kind' => 'method', 'method' => 'gc', 'position' => 0, 'name' => 'lifetime', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Session\\FileSessionHandler', 'kind' => 'constructor', 'method' => '__construct', 'position' => 2, 'name' => 'minutes', 'type' => "unit_int<'minute'>"],
                ['class' => 'Illuminate\\Session\\FileSessionHandler', 'kind' => 'method', 'method' => 'gc', 'position' => 0, 'name' => 'lifetime', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Session\\NullSessionHandler', 'kind' => 'method', 'method' => 'gc', 'position' => 0, 'name' => 'lifetime', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Session\\SymfonySessionDecorator', 'kind' => 'method', 'method' => 'invalidate', 'position' => 0, 'name' => 'lifetime', 'type' => "unit_int<'second'>|null"],
                ['class' => 'Illuminate\\Session\\SymfonySessionDecorator', 'kind' => 'method', 'method' => 'migrate', 'position' => 1, 'name' => 'lifetime', 'type' => "unit_int<'second'>|null"],
            ],
            'properties' => [],
            'returns' => [
                ['class' => 'Illuminate\\Session\\SessionManager', 'kind' => 'method', 'method' => 'defaultRouteBlockLockSeconds', 'type' => "unit_int<'second'>"],
                ['class' => 'Illuminate\\Session\\SessionManager', 'kind' => 'method', 'method' => 'defaultRouteBlockWaitSeconds', 'type' => "unit_int<'second'>"],
            ],
        ],
        'illuminate/validation' => [
            'arguments' => [
                ['class' => 'Illuminate\\Validation\\Rules\\Dimensions', 'kind' => 'method', 'method' => 'width', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'pixel'>"],
                ['class' => 'Illuminate\\Validation\\Rules\\Dimensions', 'kind' => 'method', 'method' => 'height', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'pixel'>"],
                ['class' => 'Illuminate\\Validation\\Rules\\Dimensions', 'kind' => 'method', 'method' => 'minWidth', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'pixel'>"],
                ['class' => 'Illuminate\\Validation\\Rules\\Dimensions', 'kind' => 'method', 'method' => 'minHeight', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'pixel'>"],
                ['class' => 'Illuminate\\Validation\\Rules\\Dimensions', 'kind' => 'method', 'method' => 'maxWidth', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'pixel'>"],
                ['class' => 'Illuminate\\Validation\\Rules\\Dimensions', 'kind' => 'method', 'method' => 'maxHeight', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'pixel'>"],
                ['class' => 'Illuminate\\Validation\\Rules\\File', 'kind' => 'method', 'method' => 'size', 'position' => 0, 'name' => 'size', 'type' => "string|unit_int<'1024 * byte'>"],
                ['class' => 'Illuminate\\Validation\\Rules\\File', 'kind' => 'method', 'method' => 'between', 'position' => 0, 'name' => 'minSize', 'type' => "string|unit_int<'1024 * byte'>"],
                ['class' => 'Illuminate\\Validation\\Rules\\File', 'kind' => 'method', 'method' => 'between', 'position' => 1, 'name' => 'maxSize', 'type' => "string|unit_int<'1024 * byte'>"],
                ['class' => 'Illuminate\\Validation\\Rules\\File', 'kind' => 'method', 'method' => 'min', 'position' => 0, 'name' => 'size', 'type' => "string|unit_int<'1024 * byte'>"],
                ['class' => 'Illuminate\\Validation\\Rules\\File', 'kind' => 'method', 'method' => 'max', 'position' => 0, 'name' => 'size', 'type' => "string|unit_int<'1024 * byte'>"],
            ],
            'properties' => [],
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
        'intervention/image' => [
            'arguments' => [
                ['class' => 'Intervention\\Image\\ImageManager', 'kind' => 'method', 'method' => 'create', 'position' => 0, 'name' => 'width', 'type' => "unit_int<'pixel'>", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\ImageManager', 'kind' => 'method', 'method' => 'create', 'position' => 1, 'name' => 'height', 'type' => "unit_int<'pixel'>", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\ImageManager', 'kind' => 'method', 'method' => 'createImage', 'position' => 0, 'name' => 'width', 'type' => "unit_int<'pixel'>", 'majors' => [4]],
                ['class' => 'Intervention\\Image\\ImageManager', 'kind' => 'method', 'method' => 'createImage', 'position' => 1, 'name' => 'height', 'type' => "unit_int<'pixel'>", 'majors' => [4]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'pixelate', 'position' => 0, 'name' => 'size', 'type' => "unit_int<'pixel'>"],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'rotate', 'position' => 0, 'name' => 'angle', 'type' => "unit_float<'degree'>"],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'text', 'position' => 1, 'name' => 'x', 'type' => "unit_int<'pixel'>"],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'text', 'position' => 2, 'name' => 'y', 'type' => "unit_int<'pixel'>"],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'resize', 'position' => 0, 'name' => 'width', 'type' => "unit_int<'pixel'>|null", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'resize', 'position' => 1, 'name' => 'height', 'type' => "unit_int<'pixel'>|null", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'resizeDown', 'position' => 0, 'name' => 'width', 'type' => "unit_int<'pixel'>|null", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'resizeDown', 'position' => 1, 'name' => 'height', 'type' => "unit_int<'pixel'>|null", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'scale', 'position' => 0, 'name' => 'width', 'type' => "unit_int<'pixel'>|null", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'scale', 'position' => 1, 'name' => 'height', 'type' => "unit_int<'pixel'>|null", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'scaleDown', 'position' => 0, 'name' => 'width', 'type' => "unit_int<'pixel'>|null", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'scaleDown', 'position' => 1, 'name' => 'height', 'type' => "unit_int<'pixel'>|null", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'resize', 'position' => 0, 'name' => 'width', 'type' => "\\Intervention\\Image\\Fraction|unit_int<'pixel'>|null", 'majors' => [4]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'resize', 'position' => 1, 'name' => 'height', 'type' => "\\Intervention\\Image\\Fraction|unit_int<'pixel'>|null", 'majors' => [4]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'resizeDown', 'position' => 0, 'name' => 'width', 'type' => "\\Intervention\\Image\\Fraction|unit_int<'pixel'>|null", 'majors' => [4]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'resizeDown', 'position' => 1, 'name' => 'height', 'type' => "\\Intervention\\Image\\Fraction|unit_int<'pixel'>|null", 'majors' => [4]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'scale', 'position' => 0, 'name' => 'width', 'type' => "\\Intervention\\Image\\Fraction|unit_int<'pixel'>|null", 'majors' => [4]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'scale', 'position' => 1, 'name' => 'height', 'type' => "\\Intervention\\Image\\Fraction|unit_int<'pixel'>|null", 'majors' => [4]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'scaleDown', 'position' => 0, 'name' => 'width', 'type' => "\\Intervention\\Image\\Fraction|unit_int<'pixel'>|null", 'majors' => [4]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'scaleDown', 'position' => 1, 'name' => 'height', 'type' => "\\Intervention\\Image\\Fraction|unit_int<'pixel'>|null", 'majors' => [4]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'cover', 'position' => 0, 'name' => 'width', 'type' => "unit_int<'pixel'>", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'cover', 'position' => 1, 'name' => 'height', 'type' => "unit_int<'pixel'>", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'coverDown', 'position' => 0, 'name' => 'width', 'type' => "unit_int<'pixel'>", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'coverDown', 'position' => 1, 'name' => 'height', 'type' => "unit_int<'pixel'>", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'pad', 'position' => 0, 'name' => 'width', 'type' => "unit_int<'pixel'>", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'pad', 'position' => 1, 'name' => 'height', 'type' => "unit_int<'pixel'>", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'contain', 'position' => 0, 'name' => 'width', 'type' => "unit_int<'pixel'>", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'contain', 'position' => 1, 'name' => 'height', 'type' => "unit_int<'pixel'>", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'cover', 'position' => 0, 'name' => 'width', 'type' => "\\Intervention\\Image\\Fraction|unit_int<'pixel'>", 'majors' => [4]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'cover', 'position' => 1, 'name' => 'height', 'type' => "\\Intervention\\Image\\Fraction|unit_int<'pixel'>", 'majors' => [4]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'coverDown', 'position' => 0, 'name' => 'width', 'type' => "\\Intervention\\Image\\Fraction|unit_int<'pixel'>", 'majors' => [4]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'coverDown', 'position' => 1, 'name' => 'height', 'type' => "\\Intervention\\Image\\Fraction|unit_int<'pixel'>", 'majors' => [4]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'contain', 'position' => 0, 'name' => 'width', 'type' => "\\Intervention\\Image\\Fraction|unit_int<'pixel'>", 'majors' => [4]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'contain', 'position' => 1, 'name' => 'height', 'type' => "\\Intervention\\Image\\Fraction|unit_int<'pixel'>", 'majors' => [4]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'containDown', 'position' => 0, 'name' => 'width', 'type' => "\\Intervention\\Image\\Fraction|unit_int<'pixel'>", 'majors' => [4]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'containDown', 'position' => 1, 'name' => 'height', 'type' => "\\Intervention\\Image\\Fraction|unit_int<'pixel'>", 'majors' => [4]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'resizeCanvas', 'position' => 0, 'name' => 'width', 'type' => "unit_int<'pixel'>|null", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'resizeCanvas', 'position' => 1, 'name' => 'height', 'type' => "unit_int<'pixel'>|null", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'resizeCanvasRelative', 'position' => 0, 'name' => 'width', 'type' => "unit_int<'pixel'>|null", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'resizeCanvasRelative', 'position' => 1, 'name' => 'height', 'type' => "unit_int<'pixel'>|null", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'resizeCanvas', 'position' => 0, 'name' => 'width', 'type' => "\\Intervention\\Image\\Fraction|unit_int<'pixel'>|null", 'majors' => [4]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'resizeCanvas', 'position' => 1, 'name' => 'height', 'type' => "\\Intervention\\Image\\Fraction|unit_int<'pixel'>|null", 'majors' => [4]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'resizeCanvasRelative', 'position' => 0, 'name' => 'width', 'type' => "\\Intervention\\Image\\Fraction|unit_int<'pixel'>|null", 'majors' => [4]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'resizeCanvasRelative', 'position' => 1, 'name' => 'height', 'type' => "\\Intervention\\Image\\Fraction|unit_int<'pixel'>|null", 'majors' => [4]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'crop', 'position' => 0, 'name' => 'width', 'type' => "unit_int<'pixel'>", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'crop', 'position' => 1, 'name' => 'height', 'type' => "unit_int<'pixel'>", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'crop', 'position' => 2, 'name' => 'offset_x', 'type' => "unit_int<'pixel'>", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'crop', 'position' => 3, 'name' => 'offset_y', 'type' => "unit_int<'pixel'>", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'crop', 'position' => 0, 'name' => 'width', 'type' => "\\Intervention\\Image\\Fraction|unit_int<'pixel'>", 'majors' => [4]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'crop', 'position' => 1, 'name' => 'height', 'type' => "\\Intervention\\Image\\Fraction|unit_int<'pixel'>", 'majors' => [4]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'crop', 'position' => 2, 'name' => 'x', 'type' => "unit_int<'pixel'>", 'majors' => [4]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'crop', 'position' => 3, 'name' => 'y', 'type' => "unit_int<'pixel'>", 'majors' => [4]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'place', 'position' => 2, 'name' => 'offset_x', 'type' => "unit_int<'pixel'>", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'place', 'position' => 3, 'name' => 'offset_y', 'type' => "unit_int<'pixel'>", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'insert', 'position' => 1, 'name' => 'x', 'type' => "unit_int<'pixel'>", 'majors' => [4]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'insert', 'position' => 2, 'name' => 'y', 'type' => "unit_int<'pixel'>", 'majors' => [4]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'fill', 'position' => 1, 'name' => 'x', 'type' => "unit_int<'pixel'>|null"],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'fill', 'position' => 2, 'name' => 'y', 'type' => "unit_int<'pixel'>|null"],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'drawPixel', 'position' => 0, 'name' => 'x', 'type' => "unit_int<'pixel'>"],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'drawPixel', 'position' => 1, 'name' => 'y', 'type' => "unit_int<'pixel'>"],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'drawRectangle', 'position' => 0, 'name' => 'x', 'type' => "unit_int<'pixel'>", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'drawRectangle', 'position' => 1, 'name' => 'y', 'type' => "unit_int<'pixel'>", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'drawEllipse', 'position' => 0, 'name' => 'x', 'type' => "unit_int<'pixel'>", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'drawEllipse', 'position' => 1, 'name' => 'y', 'type' => "unit_int<'pixel'>", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'drawCircle', 'position' => 0, 'name' => 'x', 'type' => "unit_int<'pixel'>", 'majors' => [3]],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'drawCircle', 'position' => 1, 'name' => 'y', 'type' => "unit_int<'pixel'>", 'majors' => [3]],
            ],
            'properties' => [],
            'returns' => [
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'width', 'type' => "unit_int<'pixel'>"],
                ['class' => 'Intervention\\Image\\Interfaces\\ImageInterface', 'kind' => 'method', 'method' => 'height', 'type' => "unit_int<'pixel'>"],
            ],
        ],
        'nmarfurt/measurements' => [
            'arguments' => [
                ['class' => 'Measurements\\Quantities\\Length', 'kind' => 'static', 'method' => 'megameters', 'position' => 0, 'name' => 'value', 'type' => "unit_float<'megameter'>"],
                ['class' => 'Measurements\\Quantities\\Length', 'kind' => 'static', 'method' => 'kilometers', 'position' => 0, 'name' => 'value', 'type' => "unit_float<'kilometer'>"],
                ['class' => 'Measurements\\Quantities\\Length', 'kind' => 'static', 'method' => 'hectometers', 'position' => 0, 'name' => 'value', 'type' => "unit_float<'hectometer'>"],
                ['class' => 'Measurements\\Quantities\\Length', 'kind' => 'static', 'method' => 'decameters', 'position' => 0, 'name' => 'value', 'type' => "unit_float<'dekameter'>"],
                ['class' => 'Measurements\\Quantities\\Length', 'kind' => 'static', 'method' => 'meters', 'position' => 0, 'name' => 'value', 'type' => "unit_float<'meter'>"],
                ['class' => 'Measurements\\Quantities\\Length', 'kind' => 'static', 'method' => 'decimeters', 'position' => 0, 'name' => 'value', 'type' => "unit_float<'decimeter'>"],
                ['class' => 'Measurements\\Quantities\\Length', 'kind' => 'static', 'method' => 'centimeters', 'position' => 0, 'name' => 'value', 'type' => "unit_float<'centimeter'>"],
                ['class' => 'Measurements\\Quantities\\Length', 'kind' => 'static', 'method' => 'millimeters', 'position' => 0, 'name' => 'value', 'type' => "unit_float<'millimeter'>"],
                ['class' => 'Measurements\\Quantities\\Length', 'kind' => 'static', 'method' => 'micrometers', 'position' => 0, 'name' => 'value', 'type' => "unit_float<'micrometer'>"],
                ['class' => 'Measurements\\Quantities\\Length', 'kind' => 'static', 'method' => 'nanometers', 'position' => 0, 'name' => 'value', 'type' => "unit_float<'nanometer'>"],
                ['class' => 'Measurements\\Quantities\\Length', 'kind' => 'static', 'method' => 'picometers', 'position' => 0, 'name' => 'value', 'type' => "unit_float<'picometer'>"],
                ['class' => 'Measurements\\Quantities\\Length', 'kind' => 'static', 'method' => 'inches', 'position' => 0, 'name' => 'value', 'type' => "unit_float<'inch'>"],
                ['class' => 'Measurements\\Quantities\\Length', 'kind' => 'static', 'method' => 'feet', 'position' => 0, 'name' => 'value', 'type' => "unit_float<'foot'>"],
                ['class' => 'Measurements\\Quantities\\Length', 'kind' => 'static', 'method' => 'yards', 'position' => 0, 'name' => 'value', 'type' => "unit_float<'yard'>"],
                ['class' => 'Measurements\\Quantities\\Length', 'kind' => 'static', 'method' => 'miles', 'position' => 0, 'name' => 'value', 'type' => "unit_float<'mile'>"],
                ['class' => 'Measurements\\Quantities\\Length', 'kind' => 'static', 'method' => 'lightyears', 'position' => 0, 'name' => 'value', 'type' => "unit_float<'light_year'>"],
                ['class' => 'Measurements\\Quantities\\Length', 'kind' => 'static', 'method' => 'nauticalMiles', 'position' => 0, 'name' => 'value', 'type' => "unit_float<'nautical_mile'>"],
                ['class' => 'Measurements\\Quantities\\Length', 'kind' => 'static', 'method' => 'fathoms', 'position' => 0, 'name' => 'value', 'type' => "unit_float<'fathom'>"],
                ['class' => 'Measurements\\Quantities\\Length', 'kind' => 'static', 'method' => 'furlongs', 'position' => 0, 'name' => 'value', 'type' => "unit_float<'furlong'>"],
                ['class' => 'Measurements\\Quantities\\Length', 'kind' => 'static', 'method' => 'astronomicalUnits', 'position' => 0, 'name' => 'value', 'type' => "unit_float<'astronomical_unit'>"],
                ['class' => 'Measurements\\Quantities\\Length', 'kind' => 'static', 'method' => 'parsecs', 'position' => 0, 'name' => 'value', 'type' => "unit_float<'parsec'>"],
                ['class' => 'Measurements\\Quantities\\Duration', 'kind' => 'static', 'method' => 'seconds', 'position' => 0, 'name' => 'value', 'type' => "unit_float<'second'>"],
                ['class' => 'Measurements\\Quantities\\Duration', 'kind' => 'static', 'method' => 'minutes', 'position' => 0, 'name' => 'value', 'type' => "unit_float<'minute'>"],
                ['class' => 'Measurements\\Quantities\\Duration', 'kind' => 'static', 'method' => 'hours', 'position' => 0, 'name' => 'value', 'type' => "unit_float<'hour'>"],
            ],
            'properties' => [],
            'returns' => [],
        ],
        'nesbot/carbon' => [
            'arguments' => [
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'addRealMicroseconds', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'microsecond'>", 'majors' => [2]],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'subRealMicroseconds', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'microsecond'>", 'majors' => [2]],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'addRealMilliseconds', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'millisecond'>", 'majors' => [2]],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'subRealMilliseconds', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'millisecond'>", 'majors' => [2]],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'addRealSeconds', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'second'>", 'majors' => [2]],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'subRealSeconds', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'second'>", 'majors' => [2]],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'addRealMinutes', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'minute'>", 'majors' => [2]],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'subRealMinutes', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'minute'>", 'majors' => [2]],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'addRealHours', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'hour'>", 'majors' => [2]],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'subRealHours', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'hour'>", 'majors' => [2]],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'addRealMicroseconds', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'microsecond'>|unit_float<'microsecond'>", 'majors' => [3], 'beforeVersions' => [3 => '3.2.0']],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'subRealMicroseconds', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'microsecond'>|unit_float<'microsecond'>", 'majors' => [3], 'beforeVersions' => [3 => '3.2.0']],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'addRealMilliseconds', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'millisecond'>|unit_float<'millisecond'>", 'majors' => [3], 'beforeVersions' => [3 => '3.2.0']],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'subRealMilliseconds', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'millisecond'>|unit_float<'millisecond'>", 'majors' => [3], 'beforeVersions' => [3 => '3.2.0']],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'addRealSeconds', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'second'>|unit_float<'second'>", 'majors' => [3], 'beforeVersions' => [3 => '3.2.0']],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'subRealSeconds', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'second'>|unit_float<'second'>", 'majors' => [3], 'beforeVersions' => [3 => '3.2.0']],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'addRealMinutes', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'minute'>|unit_float<'minute'>", 'majors' => [3], 'beforeVersions' => [3 => '3.2.0']],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'subRealMinutes', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'minute'>|unit_float<'minute'>", 'majors' => [3], 'beforeVersions' => [3 => '3.2.0']],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'addRealHours', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'hour'>|unit_float<'hour'>", 'majors' => [3], 'beforeVersions' => [3 => '3.2.0']],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'subRealHours', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'hour'>|unit_float<'hour'>", 'majors' => [3], 'beforeVersions' => [3 => '3.2.0']],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'addUTCMicroseconds', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'microsecond'>|unit_float<'microsecond'>", 'majors' => [3], 'minimumVersions' => [3 => '3.2.0']],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'subUTCMicroseconds', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'microsecond'>|unit_float<'microsecond'>", 'majors' => [3], 'minimumVersions' => [3 => '3.2.0']],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'addUTCMilliseconds', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'millisecond'>|unit_float<'millisecond'>", 'majors' => [3], 'minimumVersions' => [3 => '3.2.0']],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'subUTCMilliseconds', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'millisecond'>|unit_float<'millisecond'>", 'majors' => [3], 'minimumVersions' => [3 => '3.2.0']],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'addUTCSeconds', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'second'>|unit_float<'second'>", 'majors' => [3], 'minimumVersions' => [3 => '3.2.0']],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'subUTCSeconds', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'second'>|unit_float<'second'>", 'majors' => [3], 'minimumVersions' => [3 => '3.2.0']],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'addUTCMinutes', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'minute'>|unit_float<'minute'>", 'majors' => [3], 'minimumVersions' => [3 => '3.2.0']],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'subUTCMinutes', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'minute'>|unit_float<'minute'>", 'majors' => [3], 'minimumVersions' => [3 => '3.2.0']],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'addUTCHours', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'hour'>|unit_float<'hour'>", 'majors' => [3], 'minimumVersions' => [3 => '3.2.0']],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'subUTCHours', 'position' => 0, 'name' => 'value', 'type' => "unit_int<'hour'>|unit_float<'hour'>", 'majors' => [3], 'minimumVersions' => [3 => '3.2.0']],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'static', 'method' => 'sleep', 'position' => 0, 'name' => 'seconds', 'type' => "unit_int<'second'>|unit_float<'second'>", 'majors' => [3]],
                ['class' => 'Carbon\\Carbon', 'kind' => 'static', 'method' => 'sleep', 'position' => 0, 'name' => 'seconds', 'type' => "unit_int<'second'>|unit_float<'second'>", 'majors' => [3]],
                ['class' => 'Carbon\\CarbonImmutable', 'kind' => 'static', 'method' => 'sleep', 'position' => 0, 'name' => 'seconds', 'type' => "unit_int<'second'>|unit_float<'second'>", 'majors' => [3]],
                ['class' => 'Carbon\\FactoryImmutable', 'kind' => 'method', 'method' => 'sleep', 'position' => 0, 'name' => 'seconds', 'type' => "unit_int<'second'>|unit_float<'second'>", 'majors' => [3]],
                ['class' => 'Carbon\\WrapperClock', 'kind' => 'method', 'method' => 'sleep', 'position' => 0, 'name' => 'seconds', 'type' => "unit_int<'second'>|unit_float<'second'>", 'majors' => [3]],
            ],
            'properties' => [],
            'returns' => [
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'diffInRealMicroseconds', 'type' => "unit_int<'microsecond'>", 'majors' => [2]],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'diffInRealMilliseconds', 'type' => "unit_int<'millisecond'>", 'majors' => [2]],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'diffInRealSeconds', 'type' => "unit_int<'second'>", 'majors' => [2]],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'diffInRealMinutes', 'type' => "unit_int<'minute'>", 'majors' => [2]],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'diffInRealHours', 'type' => "unit_int<'hour'>", 'majors' => [2]],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'floatDiffInRealSeconds', 'type' => "unit_float<'second'>", 'majors' => [2]],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'floatDiffInRealMinutes', 'type' => "unit_float<'minute'>", 'majors' => [2]],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'floatDiffInRealHours', 'type' => "unit_float<'hour'>", 'majors' => [2]],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'diffInMicroseconds', 'type' => "unit_float<'microsecond'>", 'majors' => [3]],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'diffInMilliseconds', 'type' => "unit_float<'millisecond'>", 'majors' => [3]],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'diffInSeconds', 'type' => "unit_float<'second'>", 'majors' => [3]],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'diffInMinutes', 'type' => "unit_float<'minute'>", 'majors' => [3]],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'diffInHours', 'type' => "unit_float<'hour'>", 'majors' => [3]],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'secondsSinceMidnight', 'type' => "unit_float<'second'>", 'majors' => [3]],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'secondsUntilEndOfDay', 'type' => "unit_float<'second'>", 'majors' => [3]],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'diffInUTCMicroseconds', 'type' => "unit_float<'microsecond'>", 'majors' => [3], 'minimumVersions' => [3 => '3.2.0']],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'diffInUTCMilliseconds', 'type' => "unit_float<'millisecond'>", 'majors' => [3], 'minimumVersions' => [3 => '3.2.0']],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'diffInUTCSeconds', 'type' => "unit_float<'second'>", 'majors' => [3], 'minimumVersions' => [3 => '3.2.0']],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'diffInUTCMinutes', 'type' => "unit_float<'minute'>", 'majors' => [3], 'minimumVersions' => [3 => '3.2.0']],
                ['class' => 'Carbon\\CarbonInterface', 'kind' => 'method', 'method' => 'diffInUTCHours', 'type' => "unit_float<'hour'>", 'majors' => [3], 'minimumVersions' => [3 => '3.2.0']],
            ],
        ],
    ];

    /**
     * Returns the complete immutable compatibility catalog.
     *
     * @return array<non-empty-string, IntegrationBoundaries>
     *
     * @logion [RAS 22:54] Beyond the ninth orbit stood a colonnade of black marble, turning without foundation around
     *     the void. Lions carved upon its capitals drank the constellations one by one, and with each draught an
     *     extinct province shone again beneath the heavens. When the last lion lifted its head, the provinces did not
     *     vanish; they surrounded the living world and demanded the names by which they had been forgotten.
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
     * @logion [SFA 69:59] Salt laid around the throne remained dull through victories and feasts; but when the ruler
     *     grieved for a sentence he could not recall, it rose in clear spires about his feet. Let no counselor sweep it
     *     away, for remorse hath discovered the boundary that power forgot.
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
