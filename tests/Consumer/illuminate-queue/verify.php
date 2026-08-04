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

$version = Composer\InstalledVersions::getVersion('illuminate/queue');
if ($version === null || preg_match('/^([1-9][0-9]*)\./', $version, $matches) !== 1) {
    throw new RuntimeException(sprintf('Unable to determine Illuminate Queue version from %s.', $version ?? 'null'));
}

$major = (int) $matches[1];
$checks = [
    Illuminate\Contracts\Queue\Queue::class => [
        'later' => ['delay', 'job', 'data', 'queue'],
        'laterOn' => ['queue', 'delay', 'job', 'data'],
    ],
    Illuminate\Contracts\Queue\Job::class => ['release' => ['delay']],
];

foreach ($checks as $class => $methods) {
    $reflection = new ReflectionClass($class);
    foreach ($methods as $methodName => $expectedNames) {
        $actualNames = array_map(
            static fn (ReflectionParameter $parameter): string => $parameter->getName(),
            $reflection->getMethod($methodName)->getParameters(),
        );
        if ($actualNames !== $expectedNames) {
            throw new RuntimeException(sprintf('%s::%s() has an unexpected signature.', $class, $methodName));
        }
    }
}

$workerParameters = array_map(
    static fn (ReflectionParameter $parameter): string => $parameter->getName(),
    (new ReflectionMethod(Illuminate\Queue\WorkerOptions::class, '__construct'))->getParameters(),
);
$expectedLastParameter = $major === 11 ? 'rest' : 'stopWhenEmptyFor';
if ($workerParameters[array_key_last($workerParameters)] !== $expectedLastParameter) {
    throw new RuntimeException(sprintf(
        'WorkerOptions expected final parameter $%s for major %d; found: %s.',
        $expectedLastParameter,
        $major,
        implode(', ', $workerParameters),
    ));
}
