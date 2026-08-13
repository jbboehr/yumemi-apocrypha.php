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

namespace jbboehr\Yumemi\Apocrypha\Tests\PHPStan;

use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Namespace_;
use PHPStan\Parser\Parser;
use PHPStan\Testing\PHPStanTestCase;

final class IlluminateDatabaseStubTest extends PHPStanTestCase
{
    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__ . '/../../vendor/jbboehr/yumemi/extension.neon',
            __DIR__ . '/../../vendor/jbboehr/yumemi/yumemi-tags.neon',
            __DIR__ . '/illuminate-database-stub.neon',
        ];
    }

    public function testQueryTimingUnitTagsArePromotedByTheStubParser(): void
    {
        $parser = self::getContainer()->getService('stubParser');
        self::assertInstanceOf(Parser::class, $parser);

        $phpDocs = [];
        foreach ([
            __DIR__ . '/../../stubs/illuminate/database.stub',
            __DIR__ . '/../../stubs/illuminate/database-timeout.stub',
        ] as $file) {
            foreach ($parser->parseFile($file) as $node) {
                if (!$node instanceof Namespace_) {
                    continue;
                }

                foreach ($node->stmts as $statement) {
                    if (!$statement instanceof ClassLike || $statement->name === null) {
                        continue;
                    }

                    $class = $statement->name->toString();
                    foreach ($statement->getMethods() as $method) {
                        $phpDocs[$class . '::' . $method->name->toString()] = $method->getDocComment()?->getText() ?? '';
                    }
                    foreach ($statement->getProperties() as $property) {
                        $phpDocs[$class . '::$' . $property->props[0]->name->toString()]
                            = $property->getDocComment()?->getText() ?? '';
                    }
                }
            }
        }

        self::assertStringContainsString("unit_float<'millisecond'>", $phpDocs['Connection::logQuery']);
        self::assertStringContainsString("unit_int<'millisecond'>", $phpDocs['Connection::whenQueryingForLongerThan']);
        self::assertStringContainsString("unit_float<'millisecond'>", $phpDocs['Connection::totalQueryDuration']);
        self::assertStringContainsString("unit_float<'millisecond'>|null", $phpDocs['QueryExecuted::__construct']);
        self::assertStringContainsString("unit_float<'millisecond'>", $phpDocs['QueryExecuted::$time']);
        self::assertStringContainsString("unit_int<'second'>", $phpDocs['Builder::timeout']);
        self::assertStringContainsString("unit_int<'second'>", $phpDocs['Builder::$timeout']);
    }
}
