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

final class GetId3StubTest extends PHPStanTestCase
{
    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__ . '/../../vendor/jbboehr/yumemi/extension.neon',
            __DIR__ . '/../../vendor/jbboehr/yumemi/yumemi-tags.neon',
            __DIR__ . '/getid3-stub.neon',
        ];
    }

    public function testVersionOneBoundedResultShapeAndFileSizeParametersArePromoted(): void
    {
        $parser = self::getContainer()->getService('stubParser');
        self::assertInstanceOf(Parser::class, $parser);

        foreach ($parser->parseFile(__DIR__ . '/../../stubs/getid3/getid3-1.stub') as $statement) {
            if (!$statement instanceof ClassLike || $statement->name?->toString() !== 'getID3') {
                continue;
            }

            $classDoc = $statement->getDocComment()?->getText() ?? '';
            self::assertStringContainsString("filesize?: unit_int<'byte'>", $classDoc);
            self::assertStringContainsString("playtime_seconds?: unit_int<'second'>|unit_float<'second'>", $classDoc);
            self::assertStringContainsString("sample_rate?: unit_int<'hertz'>|unit_float<'hertz'>", $classDoc);
            self::assertStringContainsString("resolution_x?: unit_int<'pixel'>", $classDoc);
            self::assertStringContainsString("resolution_y?: unit_int<'pixel'>", $classDoc);

            $openFileDoc = $statement->getMethod('openfile')?->getDocComment()?->getText() ?? '';
            self::assertStringContainsString("unit_int<'byte'>|null \$filesize", $openFileDoc);

            $analyzeDoc = $statement->getMethod('analyze')?->getDocComment()?->getText() ?? '';
            self::assertStringContainsString("unit_int<'byte'>|null \$filesize", $analyzeDoc);
            self::assertStringContainsString('@phpstan-return YumemiGetId3Info', $analyzeDoc);

            return;
        }

        self::fail('The getID3 stub class was not parsed.');
    }

    public function testVersionTwoBoundedResultShapeAndFileSizeParametersArePromoted(): void
    {
        $parser = self::getContainer()->getService('stubParser');
        self::assertInstanceOf(Parser::class, $parser);

        foreach ($parser->parseFile(__DIR__ . '/../../stubs/getid3/getid3-2.stub') as $node) {
            if (!$node instanceof Namespace_) {
                continue;
            }

            self::assertSame('JamesHeinrich\\GetID3', $node->name?->toString());
            foreach ($node->stmts as $statement) {
                if (!$statement instanceof ClassLike || $statement->name?->toString() !== 'GetID3') {
                    continue;
                }

                $classDoc = $statement->getDocComment()?->getText() ?? '';
                self::assertStringContainsString("filesize?: unit_int<'byte'>", $classDoc);
                self::assertStringContainsString(
                    "frame_rate?: unit_int<'hertz'>|unit_float<'hertz'>",
                    $classDoc,
                );
                self::assertStringContainsString("resolution_x?: unit_int<'pixel'>", $classDoc);
                self::assertStringContainsString("resolution_y?: unit_int<'pixel'>", $classDoc);

                $openFileDoc = $statement->getMethod('openfile')?->getDocComment()?->getText() ?? '';
                self::assertStringContainsString("unit_int<'byte'>|null \$filesize", $openFileDoc);

                $analyzeDoc = $statement->getMethod('analyze')?->getDocComment()?->getText() ?? '';
                self::assertStringContainsString('@phpstan-return YumemiGetId3Info', $analyzeDoc);

                return;
            }
        }

        self::fail('The namespaced getID3 v2 stub class was not parsed.');
    }
}
