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

use jbboehr\Yumemi\Apocrypha\Tests\ConsumerDiagnostics\StructuredDiagnosticMatcher;

require __DIR__ . '/../ConsumerDiagnostics/StructuredDiagnosticMatcher.php';

$arguments = $_SERVER['argv'] ?? null;

if (!is_array($arguments) || !array_is_list($arguments) || count($arguments) < 2) {
    fwrite(STDERR, sprintf("Usage: %s EXPECTED-DIAGNOSTIC-FRAGMENT...\n", __FILE__));
    exit(2);
}

array_shift($arguments);

foreach ($arguments as $argument) {
    if (!is_string($argument) || $argument === '') {
        fwrite(STDERR, sprintf("Usage: %s EXPECTED-DIAGNOSTIC-FRAGMENT...\n", __FILE__));
        exit(2);
    }
}

$json = stream_get_contents(STDIN);
if ($json === false) {
    fwrite(STDERR, "Unable to read PHPStan JSON from standard input.\n");
    exit(1);
}

$errors = StructuredDiagnosticMatcher::errors($json, $arguments);
if ($errors !== []) {
    fwrite(STDERR, implode("\n", $errors) . "\n");
    exit(1);
}
