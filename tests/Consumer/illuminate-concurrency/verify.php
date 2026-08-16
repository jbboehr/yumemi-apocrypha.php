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

$version = Composer\InstalledVersions::getVersion('illuminate/concurrency')
    ?? Composer\InstalledVersions::getVersion('laravel/framework');
if ($version === null || preg_match('/^v?(11|12|13)\./', $version, $matches) !== 1) {
    throw new RuntimeException(sprintf(
        'Unable to determine a supported Illuminate Concurrency version from %s.',
        $version ?? 'null',
    ));
}

$major = (int) $matches[1];
$normalizedVersion = ltrim($version, 'v');
$supportsTimeout = $major === 13
    && ($normalizedVersion === '13.x-dev' || version_compare($normalizedVersion, '13.9.0', '>='));
$expectedParameters = $supportsTimeout ? ['tasks', 'timeout'] : ['tasks'];

foreach ([
    Illuminate\Contracts\Concurrency\Driver::class,
    Illuminate\Concurrency\ForkDriver::class,
    Illuminate\Concurrency\ProcessDriver::class,
    Illuminate\Concurrency\SyncDriver::class,
] as $class) {
    $method = new ReflectionMethod($class, 'run');
    $actualParameters = array_map(
        static fn (ReflectionParameter $parameter): string => $parameter->getName(),
        $method->getParameters(),
    );
    if ($actualParameters !== $expectedParameters) {
        throw new RuntimeException(sprintf('%s::run() has an unexpected signature.', $class));
    }

    if (!$supportsTimeout) {
        continue;
    }

    $timeout = $method->getParameters()[1];
    if ((string) $timeout->getType() !== 'Carbon\\CarbonInterval|int|null' || !$timeout->isDefaultValueAvailable()) {
        throw new RuntimeException(sprintf('%s::run() has an unexpected timeout type.', $class));
    }
    if ($timeout->getDefaultValue() !== null) {
        throw new RuntimeException(sprintf('%s::run() has an unexpected timeout default.', $class));
    }
}

$manager = new ReflectionClass(Illuminate\Concurrency\ConcurrencyManager::class);
$managerDoc = $manager->getDocComment();
if (!is_string($managerDoc) || !str_contains($managerDoc, '@mixin \\Illuminate\\Contracts\\Concurrency\\Driver')) {
    throw new RuntimeException('Illuminate ConcurrencyManager does not retain its Driver mixin declaration.');
}

$facade = new ReflectionClass(Illuminate\Support\Facades\Concurrency::class);
$facadeDoc = $facade->getDocComment();
if (!is_string($facadeDoc) || !str_contains($facadeDoc, 'static array run(')) {
    throw new RuntimeException('Illuminate Concurrency facade does not retain its run declaration.');
}
