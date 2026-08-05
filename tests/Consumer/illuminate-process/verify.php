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

$version = Composer\InstalledVersions::getVersion('illuminate/process')
    ?? Composer\InstalledVersions::getPrettyVersion('laravel/framework');
if ($version === null || preg_match('/^v?([1-9][0-9]*)\./', $version, $matches) !== 1) {
    throw new RuntimeException(sprintf('Unable to determine Illuminate Process version from %s.', $version ?? 'null'));
}

$major = (int) $matches[1];
$reflection = new ReflectionClass(Illuminate\Process\PendingProcess::class);

foreach (['timeout', 'idleTimeout'] as $methodName) {
    $method = $reflection->getMethod($methodName);
    $parameters = $method->getParameters();
    if (count($parameters) !== 1 || $parameters[0]->getName() !== 'timeout') {
        throw new RuntimeException(sprintf('%s::%s() has an unexpected signature.', $reflection->getName(), $methodName));
    }

    $type = (string) $parameters[0]->getType();
    $expectedType = $major === 13 ? 'Carbon\\CarbonInterval|int' : 'int';
    if ($type !== $expectedType) {
        throw new RuntimeException(sprintf(
            '%s::%s() expected parameter type %s for major %d; found %s.',
            $reflection->getName(),
            $methodName,
            $expectedType,
            $major,
            $type,
        ));
    }
}
