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

final class GuzzleStubTest extends PHPStanTestCase
{
    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__ . '/../../vendor/jbboehr/yumemi/extension.neon',
            __DIR__ . '/../../vendor/jbboehr/yumemi/yumemi-tags.neon',
            __DIR__ . '/guzzle-stub.neon',
        ];
    }

    public function testRequestOptionsAndMajorSpecificRetryCallbacksAreParsed(): void
    {
        $parser = self::getContainer()->getService('stubParser');
        self::assertInstanceOf(Parser::class, $parser);

        $docs7 = $this->classAndMethodPhpDocs($parser, 'guzzle-7.stub');
        $docs8 = $this->classAndMethodPhpDocs($parser, 'guzzle-8.stub');

        foreach ([$docs7, $docs8] as $phpDocs) {
            self::assertStringContainsString(
                "connect_timeout?: unit_int<'second'>|unit_float<'second'>",
                $phpDocs['Client'],
            );
            self::assertStringContainsString("expect?: bool|unit_int<'byte'>", $phpDocs['Client']);
            self::assertStringContainsString('YumemiRequestOptions', $phpDocs['Client::request']);
            self::assertStringContainsString(
                '@yumemi-param YumemiRequestOptions $options',
                $phpDocs['Client::request'],
            );
        }

        self::assertStringContainsString(
            "callable(int): unit_int<'millisecond'>",
            $docs7['Middleware::retry'],
        );
        self::assertStringContainsString(
            "\\Psr\\Http\\Message\\RequestInterface): unit_int<'millisecond'>",
            $docs8['Middleware::retry'],
        );
    }

    public function testTransferTimeTagsArePromotedByTheStubParser(): void
    {
        $parser = self::getContainer()->getService('stubParser');
        self::assertInstanceOf(Parser::class, $parser);

        $methods = [];
        foreach ($parser->parseFile(__DIR__ . '/../../stubs/guzzle/guzzle.stub') as $node) {
            if (!$node instanceof Namespace_) {
                continue;
            }

            foreach ($node->stmts as $statement) {
                if (!$statement instanceof ClassLike || $statement->name?->toString() !== 'TransferStats') {
                    continue;
                }

                foreach ($statement->getMethods() as $method) {
                    $docComment = $method->getDocComment();
                    self::assertNotNull($docComment);
                    $methods[$method->name->toString()] = $docComment->getText();
                }
            }
        }

        self::assertStringContainsString("unit_float<'second'>|null", $methods['__construct']);
        self::assertStringContainsString("unit_float<'second'>|null", $methods['getTransferTime']);
    }

    /** @return array<string, string> */
    private function classAndMethodPhpDocs(Parser $parser, string $file): array
    {
        $phpDocs = [];
        foreach ($parser->parseFile(__DIR__ . '/../../stubs/guzzle/' . $file) as $node) {
            if (!$node instanceof Namespace_) {
                continue;
            }

            foreach ($node->stmts as $statement) {
                if (!$statement instanceof ClassLike || $statement->name === null) {
                    continue;
                }

                $class = $statement->name->toString();
                $docComment = $statement->getDocComment();
                if ($docComment !== null) {
                    $phpDocs[$class] = $docComment->getText();
                }
                foreach ($statement->getMethods() as $method) {
                    $docComment = $method->getDocComment();
                    if ($docComment !== null) {
                        $phpDocs[$class . '::' . $method->name->toString()] = $docComment->getText();
                    }
                }
            }
        }

        return $phpDocs;
    }
}
