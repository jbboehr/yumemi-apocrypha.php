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
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Namespace_;
use PHPStan\Parser\Parser;
use PHPStan\Testing\PHPStanTestCase;

final class IlluminateSupportStubTest extends PHPStanTestCase
{
    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__ . '/../../vendor/jbboehr/yumemi/extension.neon',
            __DIR__ . '/../../vendor/jbboehr/yumemi/yumemi-tags.neon',
            __DIR__ . '/illuminate-support-stub.neon',
        ];
    }

    public function testUnitTagsArePromotedByTheStubParser(): void
    {
        $parser = self::getContainer()->getService('stubParser');
        self::assertInstanceOf(Parser::class, $parser);

        $phpDocs = [];
        foreach ($parser->parseFile(__DIR__ . '/../../stubs/illuminate/support.stub') as $node) {
            if (!$node instanceof Namespace_) {
                continue;
            }

            foreach ($node->stmts as $statement) {
                if (!$statement instanceof ClassLike || $statement->name === null) {
                    continue;
                }

                foreach ($statement->getMethods() as $method) {
                    $phpDocs[$statement->name->toString() . '::' . $method->name->toString()] = $this->methodPhpDoc($method);
                }
            }
        }

        self::assertStringContainsString("unit_float<'millisecond'>", $phpDocs['Benchmark::measure']);
        self::assertStringContainsString("array{0: TReturn, 1: unit_float<'millisecond'>}", $phpDocs['Benchmark::value']);
        self::assertStringContainsString("unit_int<'microsecond'>", $phpDocs['Sleep::usleep']);
        self::assertStringContainsString("unit_int<'second'>", $phpDocs['Sleep::sleep']);
        self::assertStringContainsString("@param unit_int<'microsecond'> \$microseconds", $phpDocs['Timebox::call']);
    }

    private function methodPhpDoc(ClassMethod $method): string
    {
        $docComment = $method->getDocComment();
        self::assertNotNull($docComment);

        return $docComment->getText();
    }
}
