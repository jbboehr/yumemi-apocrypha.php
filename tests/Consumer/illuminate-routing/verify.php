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

$version = Composer\InstalledVersions::getVersion('illuminate/routing')
    ?? Composer\InstalledVersions::getVersion('laravel/framework');
if ($version === null || preg_match('/^v?(11|12|13)\./', $version) !== 1) {
    throw new RuntimeException(sprintf('Unable to determine a supported Illuminate Routing version from %s.', $version ?? 'null'));
}

$selection = new jbboehr\Yumemi\Apocrypha\PHPStan\ConfiguredIntegrationStubFilesExtension(
    ['illuminate/routing'],
    false,
    true,
);
if ($selection->getFiles() !== [] || !$selection->usesUnitBoundaryAdapter('illuminate/routing')) {
    throw new RuntimeException('Illuminate Routing did not select its package-boundary adapter.');
}

$checks = [
    Illuminate\Contracts\Routing\UrlGenerator::class => [
        'signedRoute' => ['name', 'parameters', 'expiration', 'absolute'],
        'temporarySignedRoute' => ['name', 'expiration', 'parameters', 'absolute'],
    ],
    Illuminate\Routing\UrlGenerator::class => [
        'signedRoute' => ['name', 'parameters', 'expiration', 'absolute'],
        'temporarySignedRoute' => ['name', 'expiration', 'parameters', 'absolute'],
    ],
    Illuminate\Routing\Redirector::class => [
        'signedRoute' => ['route', 'parameters', 'expiration', 'status', 'headers'],
        'temporarySignedRoute' => ['route', 'expiration', 'parameters', 'status', 'headers'],
    ],
    Illuminate\Routing\Route::class => [
        'block' => ['lockSeconds', 'waitSeconds'],
        'locksFor' => [],
        'middleware' => ['middleware'],
        'waitsFor' => [],
    ],
    Illuminate\Routing\Middleware\ThrottleRequests::class => [
        'with' => ['maxAttempts', 'decayMinutes', 'prefix'],
        'handle' => ['request', 'next', 'maxAttempts', 'decayMinutes', 'prefix'],
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

$route = new Illuminate\Routing\Route(['GET'], '/report', static fn (): string => 'report');
$route->block(30, 5);
if ($route->locksFor() !== 30 || $route->waitsFor() !== 5) {
    throw new RuntimeException('Route does not preserve its second-valued lock durations.');
}

if (Illuminate\Routing\Middleware\ThrottleRequests::with(60, 2) !== 'Illuminate\\Routing\\Middleware\\ThrottleRequests:60,2') {
    throw new RuntimeException('ThrottleRequests does not preserve its minute-valued decay configuration.');
}
