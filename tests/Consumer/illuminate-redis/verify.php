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

$version = Composer\InstalledVersions::getVersion('illuminate/redis')
    ?? Composer\InstalledVersions::getVersion('laravel/framework');
if ($version === null || preg_match('/^v?(11|12|13)\./', $version) !== 1) {
    throw new RuntimeException(sprintf('Unable to determine a supported Illuminate Redis version from %s.', $version ?? 'null'));
}

$checks = [
    Illuminate\Redis\Limiters\DurationLimiterBuilder::class => [
        'every' => ['decay'],
        'block' => ['timeout'],
        'sleep' => ['sleep'],
    ],
    Illuminate\Redis\Limiters\DurationLimiter::class => [
        '__construct' => ['redis', 'name', 'maxLocks', 'decay'],
        'block' => ['timeout', 'callback', 'sleep'],
    ],
    Illuminate\Redis\Limiters\ConcurrencyLimiterBuilder::class => [
        'releaseAfter' => ['releaseAfter'],
        'block' => ['timeout'],
        'sleep' => ['sleep'],
    ],
    Illuminate\Redis\Limiters\ConcurrencyLimiter::class => [
        '__construct' => ['redis', 'name', 'maxLocks', 'releaseAfter'],
        'block' => ['timeout', 'callback', 'sleep'],
    ],
    Illuminate\Redis\Events\CommandExecuted::class => [
        '__construct' => ['command', 'parameters', 'time', 'connection'],
    ],
];

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

$propertyChecks = [
    Illuminate\Redis\Limiters\DurationLimiterBuilder::class => ['decay', 'timeout', 'sleep'],
    Illuminate\Redis\Limiters\ConcurrencyLimiterBuilder::class => ['releaseAfter', 'timeout', 'sleep'],
    Illuminate\Redis\Events\CommandExecuted::class => ['time'],
];

foreach ($propertyChecks as $class => $properties) {
    $reflection = new ReflectionClass($class);
    foreach ($properties as $property) {
        if (!$reflection->hasProperty($property)) {
            throw new RuntimeException(sprintf('%s::$%s does not exist.', $class, $property));
        }
    }
}

$connection = new class () extends Illuminate\Redis\Connections\Connection {
    public function createSubscription($channels, Closure $callback, $method = 'subscribe'): void
    {
    }
};
$connection->setName('reports');

$duration = $connection->throttle('reports')->allow(10)->every(30)->block(5)->sleep(250);
if ($duration->decay !== 30 || $duration->timeout !== 5 || $duration->sleep !== 250) {
    throw new RuntimeException('Duration limiter builder does not preserve its documented time scales.');
}

$concurrency = $connection->funnel('reports')->limit(10)->releaseAfter(60)->block(5)->sleep(250);
if ($concurrency->releaseAfter !== 60 || $concurrency->timeout !== 5 || $concurrency->sleep !== 250) {
    throw new RuntimeException('Concurrency limiter builder does not preserve its documented time scales.');
}

$event = new Illuminate\Redis\Events\CommandExecuted('get', ['report'], 1.25, $connection);
if ($event->time !== 1.25 || $event->connectionName !== 'reports') {
    throw new RuntimeException('CommandExecuted does not preserve its millisecond duration or connection name.');
}
