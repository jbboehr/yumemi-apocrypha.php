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

$expectedMajor = (int) ($argv[1] ?? 0);
$frameworkVersion = Composer\InstalledVersions::getPrettyVersion('laravel/framework')
    ?? Composer\InstalledVersions::getVersion('laravel/framework');
if (
    $frameworkVersion === null
    || preg_match('/^v?([1-9][0-9]*)\./', $frameworkVersion, $matches) !== 1
    || (int) $matches[1] !== $expectedMajor
) {
    throw new RuntimeException(sprintf(
        'Expected Laravel framework major %d; found %s.',
        $expectedMajor,
        $frameworkVersion ?? 'unknown',
    ));
}

$checks = [
    'illuminate/cache' => [Illuminate\Contracts\Cache\Store::class, 'put', 'seconds'],
    'illuminate/cookie' => [Illuminate\Contracts\Cookie\Factory::class, 'make', 'minutes'],
    'illuminate/filesystem' => [Illuminate\Contracts\Filesystem\Filesystem::class, 'size', 'path'],
    'illuminate/http' => [Illuminate\Http\Client\PendingRequest::class, 'timeout', 'seconds'],
    'illuminate/process' => [Illuminate\Process\PendingProcess::class, 'timeout', 'timeout'],
    'illuminate/queue' => [Illuminate\Contracts\Queue\Queue::class, 'later', 'delay'],
    'illuminate/redis' => [Illuminate\Redis\Limiters\DurationLimiterBuilder::class, 'every', 'decay'],
    'illuminate/support' => [Illuminate\Support\Sleep::class, 'sleep', 'duration'],
    'illuminate/validation' => [Illuminate\Validation\Rules\Dimensions::class, 'width', 'value'],
];

foreach ($checks as $package => [$class, $method, $parameter]) {
    if (!Composer\InstalledVersions::isInstalled($package)) {
        throw new RuntimeException(sprintf('%s is not represented as installed.', $package));
    }
    if (Composer\InstalledVersions::getVersion($package) !== null) {
        throw new RuntimeException(sprintf('%s unexpectedly has a direct version.', $package));
    }

    $replacementVersions = explode(' || ', Composer\InstalledVersions::getVersionRanges($package));
    if (!in_array($frameworkVersion, $replacementVersions, true)) {
        throw new RuntimeException(sprintf(
            '%s replacement versions %s do not contain Laravel framework version %s.',
            $package,
            implode(', ', $replacementVersions),
            $frameworkVersion,
        ));
    }

    $reflection = new ReflectionMethod($class, $method);
    $parameterNames = array_map(
        static fn (ReflectionParameter $reflectionParameter): string => $reflectionParameter->getName(),
        $reflection->getParameters(),
    );
    if (!in_array($parameter, $parameterNames, true)) {
        throw new RuntimeException(sprintf('%s::%s() does not have parameter $%s.', $class, $method, $parameter));
    }
}
