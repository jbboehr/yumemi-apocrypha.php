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

final class IlluminateQueueStubTest extends PHPStanTestCase
{
    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__ . '/../../vendor/jbboehr/yumemi/extension.neon',
            __DIR__ . '/../../vendor/jbboehr/yumemi/yumemi-tags.neon',
            __DIR__ . '/illuminate-queue-stub.neon',
        ];
    }

    public function testCommonUnitTagsArePromotedByTheStubParser(): void
    {
        $parser = self::getContainer()->getService('stubParser');
        self::assertInstanceOf(Parser::class, $parser);

        $phpDocs = $this->memberPhpDocs($parser, 'queue.stub');

        self::assertStringContainsString("unit_int<'second'>", $phpDocs['Queue::later']);
        self::assertStringContainsString("unit_int<'second'>", $phpDocs['Queue::laterOn']);
        self::assertStringContainsString("unit_int<'second'>", $phpDocs['Job::release']);
        self::assertStringContainsString("unit_int<'second'>", $phpDocs['InteractsWithQueue::release']);
    }

    public function testWorkerOptionUnitTagsArePromotedByTheStubParser(): void
    {
        $parser = self::getContainer()->getService('stubParser');
        self::assertInstanceOf(Parser::class, $parser);

        foreach (['queue-worker-11.stub', 'queue-worker-12.stub'] as $file) {
            $phpDocs = $this->memberPhpDocs($parser, $file);

            self::assertStringContainsString("unit_int<'second'>", $phpDocs['WorkerOptions::__construct']);
            self::assertStringContainsString("unit_int<'1048576 * byte'>", $phpDocs['WorkerOptions::__construct']);
            self::assertStringContainsString("unit_int<'second'>", $phpDocs['WorkerOptions::$backoff']);
            self::assertStringContainsString("unit_int<'1048576 * byte'>", $phpDocs['WorkerOptions::$memory']);
        }
    }

    /** @return array<string, string> */
    private function memberPhpDocs(Parser $parser, string $file): array
    {
        $phpDocs = [];
        foreach ($parser->parseFile(__DIR__ . '/../../stubs/illuminate/' . $file) as $node) {
            if (!$node instanceof Namespace_) {
                continue;
            }

            foreach ($node->stmts as $statement) {
                if (!$statement instanceof ClassLike || $statement->name === null) {
                    continue;
                }

                foreach ($statement->getMethods() as $method) {
                    $docComment = $method->getDocComment();
                    self::assertNotNull($docComment);
                    $phpDocs[$statement->name->toString() . '::' . $method->name->toString()] = $docComment->getText();
                }

                foreach ($statement->getProperties() as $property) {
                    $docComment = $property->getDocComment();
                    self::assertNotNull($docComment);
                    $name = $property->props[0]->name->toString();
                    $phpDocs[$statement->name->toString() . '::$' . $name] = $docComment->getText();
                }
            }
        }

        return $phpDocs;
    }
}
