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

$version = Composer\InstalledVersions::getVersion('illuminate/auth')
    ?? Composer\InstalledVersions::getVersion('laravel/framework');
if ($version === null || preg_match('/^v?(11|12|13)\./', $version, $matches) !== 1) {
    throw new RuntimeException(sprintf('Unable to determine a supported Illuminate Auth version from %s.', $version ?? 'null'));
}

$major = (int) $matches[1];
$normalizedVersion = ltrim($version, 'v');

/** @param list<string> $expectedParameters */
function verifyAuthMethod(string $class, string $method, array $expectedParameters): void
{
    $reflection = new ReflectionMethod($class, $method);
    $actualParameters = array_map(
        static fn (ReflectionParameter $parameter): string => $parameter->getName(),
        $reflection->getParameters(),
    );
    if ($actualParameters !== $expectedParameters) {
        throw new RuntimeException(sprintf(
            '%s::%s() expected parameters %s; found %s.',
            $class,
            $method,
            implode(', ', $expectedParameters),
            implode(', ', $actualParameters),
        ));
    }
}

verifyAuthMethod(Illuminate\Auth\SessionGuard::class, 'setRememberDuration', ['minutes']);
verifyAuthMethod(
    Illuminate\Auth\Middleware\RequirePassword::class,
    '__construct',
    ['responseFactory', 'urlGenerator', 'passwordTimeout'],
);
verifyAuthMethod(
    Illuminate\Auth\Middleware\RequirePassword::class,
    'using',
    ['redirectToRoute', 'passwordTimeoutSeconds'],
);
verifyAuthMethod(
    Illuminate\Auth\Middleware\RequirePassword::class,
    'handle',
    ['request', 'next', 'redirectToRoute', 'passwordTimeoutSeconds'],
);
verifyAuthMethod(
    Illuminate\Auth\Passwords\DatabaseTokenRepository::class,
    '__construct',
    ['connection', 'hasher', 'table', 'hashKey', 'expires', 'throttle'],
);

$supportsCacheRepository = $major !== 11 || version_compare($normalizedVersion, '11.31.0', '>=');
if (class_exists(Illuminate\Auth\Passwords\CacheTokenRepository::class) !== $supportsCacheRepository) {
    throw new RuntimeException(sprintf(
        'CacheTokenRepository availability does not match the Illuminate Auth %s profile.',
        $version,
    ));
}
if ($supportsCacheRepository) {
    $cacheParameters = ['cache', 'hasher', 'hashKey', 'expires', 'throttle'];
    if ($major === 11 || ($major === 12 && version_compare($normalizedVersion, '12.20.0', '<'))) {
        $cacheParameters[] = 'prefix';
    }
    verifyAuthMethod(Illuminate\Auth\Passwords\CacheTokenRepository::class, '__construct', $cacheParameters);
}

$supportsTimebox = $major === 13
    || ($major === 12 && version_compare($normalizedVersion, '12.14.0', '>='))
    || ($major === 11 && version_compare($normalizedVersion, '11.45.0', '>='));
$sessionParameters = ['name', 'provider', 'session', 'request', 'timebox', 'rehashOnLogin'];
if ($supportsTimebox) {
    $sessionParameters[] = 'timeboxDuration';
}
if ($major === 13 || ($major === 12 && version_compare($normalizedVersion, '12.45.0', '>='))) {
    $sessionParameters[] = 'hashKey';
}
verifyAuthMethod(Illuminate\Auth\SessionGuard::class, '__construct', $sessionParameters);

$brokerParameters = ['tokens', 'users', 'dispatcher'];
if ($supportsTimebox) {
    $brokerParameters[] = 'timebox';
    $brokerParameters[] = 'timeboxDuration';
}
verifyAuthMethod(Illuminate\Auth\Passwords\PasswordBroker::class, '__construct', $brokerParameters);

$connection = new Illuminate\Database\Connection(null);
$hasher = new Illuminate\Hashing\BcryptHasher();
$databaseTokens = new Illuminate\Auth\Passwords\DatabaseTokenRepository(
    $connection,
    $hasher,
    'password_reset_tokens',
    'hash-key',
    2,
    3,
);
$expires = new ReflectionProperty($databaseTokens, 'expires');
$throttle = new ReflectionProperty($databaseTokens, 'throttle');
$expectedExpiry = $major === 11 ? 120 : 2;
if ($expires->getValue($databaseTokens) !== $expectedExpiry || $throttle->getValue($databaseTokens) !== 3) {
    throw new RuntimeException(sprintf(
        'DatabaseTokenRepository did not preserve the %s expiration and second-valued throttle behavior.',
        $major === 11 ? 'minute-valued' : 'second-valued',
    ));
}
