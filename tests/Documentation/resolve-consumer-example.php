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

use jbboehr\Yumemi\Apocrypha\Tests\Documentation\PublicDocumentationExamples;
use jbboehr\Akashi\Integration\PHPStan\ExpectationParser;

require __DIR__ . '/../../vendor/autoload.php';

$arguments = $_SERVER['argv'] ?? null;
if (!is_array($arguments) || !array_is_list($arguments) || count($arguments) !== 4) {
    fwrite(STDERR, sprintf("Usage: %s document|expectations CONSUMER MARKER\n", __FILE__));
    exit(2);
}

[, $operation, $consumer, $marker] = $arguments;
if (
    !is_string($operation)
    || !is_string($consumer)
    || $consumer === ''
    || !is_string($marker)
    || $marker === ''
) {
    fwrite(STDERR, sprintf("Usage: %s document|expectations CONSUMER MARKER\n", __FILE__));
    exit(2);
}

try {
    if ($operation === 'document') {
        fwrite(STDOUT, PublicDocumentationExamples::documentForConsumer($consumer, $marker) . "\n");
        exit(0);
    }

    if ($operation === 'expectations') {
        $example = PublicDocumentationExamples::exampleForConsumer($consumer, $marker);
        foreach ((new ExpectationParser())->parse($example) as $expectation) {
            fwrite(STDOUT, $expectation->text . "\n");
        }
        exit(0);
    }

    fwrite(STDERR, sprintf("Usage: %s document|expectations CONSUMER MARKER\n", __FILE__));
    exit(2);
} catch (Throwable $exception) {
    fwrite(STDERR, $exception->getMessage() . "\n");
    exit(1);
}
