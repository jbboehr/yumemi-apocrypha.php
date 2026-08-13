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

require __DIR__ . '/vendor/autoload.php';

$version = Composer\InstalledVersions::getVersion('illuminate/database')
    ?? Composer\InstalledVersions::getVersion('laravel/framework');
if ($version === null || preg_match('/^v?(11|12|13)\./', $version, $matches) !== 1) {
    throw new RuntimeException(sprintf('Unable to determine a supported Illuminate Database version from %s.', $version ?? 'null'));
}

$major = (int) $matches[1];
$normalizedVersion = ltrim($version, 'v');
$hasTimeout = method_exists(Illuminate\Database\Query\Builder::class, 'timeout');
$expectedTimeout = $major === 13 || ($major === 12 && version_compare($normalizedVersion, '12.51.0', '>='));
if ($hasTimeout !== $expectedTimeout) {
    throw new RuntimeException(sprintf('Illuminate Database %s has an unexpected query-timeout surface.', $version));
}

$checks = [
    Illuminate\Database\Connection::class => [
        'logQuery' => ['query', 'bindings', 'time'],
        'whenQueryingForLongerThan' => ['threshold', 'handler'],
        'totalQueryDuration' => [],
    ],
];
if ($hasTimeout) {
    $checks[Illuminate\Database\Query\Builder::class] = [
        'timeout' => ['seconds'],
    ];
}

foreach ($checks as $class => $methods) {
    $reflection = new ReflectionClass($class);
    foreach ($methods as $methodName => $expectedParameters) {
        $actualParameters = array_map(
            static fn (ReflectionParameter $parameter): string => $parameter->getName(),
            $reflection->getMethod($methodName)->getParameters(),
        );
        if ($actualParameters !== $expectedParameters) {
            throw new RuntimeException(sprintf('%s::%s() has an unexpected signature.', $class, $methodName));
        }
    }
}

$expectedEventParameters = ['sql', 'bindings', 'time', 'connection'];
if (
    $major === 13
    || ($major === 12 && ($normalizedVersion === '12.x-dev' || version_compare($normalizedVersion, '12.45.0', '>=')))
) {
    $expectedEventParameters[] = 'readWriteType';
}
$actualEventParameters = array_map(
    static fn (ReflectionParameter $parameter): string => $parameter->getName(),
    (new ReflectionMethod(Illuminate\Database\Events\QueryExecuted::class, '__construct'))->getParameters(),
);
if ($actualEventParameters !== $expectedEventParameters) {
    throw new RuntimeException('QueryExecuted::__construct() has an unexpected signature.');
}

if (!(new ReflectionClass(Illuminate\Database\Events\QueryExecuted::class))->hasProperty('time')) {
    throw new RuntimeException('QueryExecuted::$time does not exist.');
}
if ($hasTimeout && !(new ReflectionClass(Illuminate\Database\Query\Builder::class))->hasProperty('timeout')) {
    throw new RuntimeException('Builder::$timeout does not exist.');
}

$connection = new Illuminate\Database\SQLiteConnection(null);
$connection->logQuery('select 1', [], 1.25);
if ($connection->totalQueryDuration() !== 1.25) {
    throw new RuntimeException('Connection does not preserve query durations in milliseconds.');
}

$event = new Illuminate\Database\Events\QueryExecuted('select 1', [], 1.25, $connection);
if ($event->time !== 1.25) {
    throw new RuntimeException('QueryExecuted does not preserve its millisecond duration.');
}
$unmeasuredEvent = new Illuminate\Database\Events\QueryExecuted('select 1', [], null, $connection);
if ($unmeasuredEvent->time !== null) {
    throw new RuntimeException('QueryExecuted does not preserve a missing query duration.');
}

if ($hasTimeout) {
    $builder = $connection->query()->timeout(5);
    if ($builder->timeout !== 5) {
        throw new RuntimeException('Query Builder does not preserve its second-valued timeout.');
    }
}
