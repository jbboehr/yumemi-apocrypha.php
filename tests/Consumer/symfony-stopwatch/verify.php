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

$methods = [
    Symfony\Component\Stopwatch\StopwatchEvent::class => [
        'getDuration' => 'int|float',
        'getMemory' => 'int',
    ],
    Symfony\Component\Stopwatch\StopwatchPeriod::class => [
        'getDuration' => 'int|float',
        'getMemory' => 'int',
    ],
];

foreach ($methods as $class => $expectedMethods) {
    $reflection = new ReflectionClass($class);

    foreach ($expectedMethods as $methodName => $expectedReturnType) {
        if (!$reflection->hasMethod($methodName)) {
            throw new RuntimeException(sprintf('%s::%s() does not exist.', $class, $methodName));
        }

        $method = $reflection->getMethod($methodName);
        if (!$method->isPublic() || $method->getNumberOfParameters() !== 0) {
            throw new RuntimeException(sprintf('%s::%s() does not have the expected public shape.', $class, $methodName));
        }

        $returnType = $method->getReturnType();
        if ($returnType === null || (string) $returnType !== $expectedReturnType) {
            throw new RuntimeException(sprintf(
                '%s::%s() has return type %s; expected %s.',
                $class,
                $methodName,
                $returnType === null ? 'none' : (string) $returnType,
                $expectedReturnType,
            ));
        }
    }
}
