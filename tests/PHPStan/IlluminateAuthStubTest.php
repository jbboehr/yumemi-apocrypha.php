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

final class IlluminateAuthStubTest extends PHPStanTestCase
{
    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__ . '/../../vendor/jbboehr/yumemi/extension.neon',
            __DIR__ . '/../../vendor/jbboehr/yumemi/yumemi-tags.neon',
            __DIR__ . '/illuminate-auth-stub.neon',
        ];
    }

    public function testMinuteSecondAndMicrosecondTagsArePromotedByTheStubParser(): void
    {
        $parser = self::getContainer()->getService('stubParser');
        self::assertInstanceOf(Parser::class, $parser);

        $expectedUnits = [
            'auth.stub' => ["unit_int<'second'>"],
            'auth-session-guard.stub' => ["unit_int<'minute'>"],
            'auth-session-guard-timebox.stub' => ["unit_int<'microsecond'>", "unit_int<'minute'>"],
            'auth-session-guard-timebox-hash-key.stub' => ["unit_int<'microsecond'>", "unit_int<'minute'>"],
            'auth-password-broker-timebox.stub' => ["unit_int<'microsecond'>"],
            'auth-database-token-repository-11.stub' => ["unit_int<'minute'>", "unit_int<'second'>"],
            'auth-database-token-repository-12.stub' => ["unit_int<'second'>"],
            'auth-cache-token-repository-with-prefix.stub' => ["unit_int<'second'>"],
            'auth-cache-token-repository.stub' => ["unit_int<'second'>"],
        ];

        foreach ($expectedUnits as $file => $units) {
            $phpDocs = $this->methodPhpDocs($parser, $file);
            foreach ($units as $unit) {
                self::assertStringContainsString($unit, implode("\n", $phpDocs), $file);
            }
        }
    }

    /** @return list<string> */
    private function methodPhpDocs(Parser $parser, string $file): array
    {
        $phpDocs = [];
        foreach ($parser->parseFile(__DIR__ . '/../../stubs/illuminate/' . $file) as $node) {
            if (!$node instanceof Namespace_) {
                continue;
            }

            foreach ($node->stmts as $statement) {
                if (!$statement instanceof ClassLike) {
                    continue;
                }

                foreach ($statement->getMethods() as $method) {
                    $phpDocs[] = $method->getDocComment()?->getText() ?? '';
                }
            }
        }

        return $phpDocs;
    }
}
