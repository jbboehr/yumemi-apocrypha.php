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

$version = Composer\InstalledVersions::getVersion('illuminate/session')
    ?? Composer\InstalledVersions::getVersion('laravel/framework');
if ($version === null || preg_match('/^v?(11|12|13)\./', $version) !== 1) {
    throw new RuntimeException(sprintf('Unable to determine a supported Illuminate Session version from %s.', $version ?? 'null'));
}

$checks = [
    Illuminate\Session\ArraySessionHandler::class => [
        '__construct' => ['minutes'],
        'gc' => ['lifetime'],
    ],
    Illuminate\Session\CacheBasedSessionHandler::class => [
        '__construct' => ['cache', 'minutes'],
        'gc' => ['lifetime'],
    ],
    Illuminate\Session\CookieSessionHandler::class => [
        '__construct' => ['cookie', 'minutes', 'expireOnClose'],
        'gc' => ['lifetime'],
    ],
    Illuminate\Session\DatabaseSessionHandler::class => [
        '__construct' => ['connection', 'table', 'minutes', 'container'],
        'gc' => ['lifetime'],
    ],
    Illuminate\Session\FileSessionHandler::class => [
        '__construct' => ['files', 'path', 'minutes'],
        'gc' => ['lifetime'],
    ],
    Illuminate\Session\NullSessionHandler::class => [
        'gc' => ['lifetime'],
    ],
    Illuminate\Session\SessionManager::class => [
        'defaultRouteBlockLockSeconds' => [],
        'defaultRouteBlockWaitSeconds' => [],
    ],
    Illuminate\Session\SymfonySessionDecorator::class => [
        'invalidate' => ['lifetime'],
        'migrate' => ['destroy', 'lifetime'],
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

$array = new Illuminate\Session\ArraySessionHandler(30);
if (!$array->write('report', 'ready') || $array->read('report') !== 'ready' || $array->gc(3600) !== 0) {
    throw new RuntimeException('ArraySessionHandler does not preserve its minute lifetime and second-valued GC API.');
}

$null = new Illuminate\Session\NullSessionHandler();
if ($null->gc(3600) !== 0) {
    throw new RuntimeException('NullSessionHandler does not preserve its second-valued GC API.');
}
