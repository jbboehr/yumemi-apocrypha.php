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

namespace jbboehr\Yumemi\Apocrypha\Tests\Benchmarks;

use jbboehr\Yumemi\Apocrypha\Benchmarks\StressFixture;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Function_;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class StressFixtureTest extends TestCase
{
    public function testGeneratedGroupsHaveDistinctDeclarationsAndSixDiagnosticSitesEach(): void
    {
        $fixture = StressFixture::generate(2);
        $nodes = (new ParserFactory())->createForNewestSupportedVersion()->parse($fixture['source']) ?? [];
        $functions = (new NodeFinder())->findInstanceOf($nodes, Function_::class);

        self::assertCount(2, $functions);
        self::assertSame('analyseCheckout000001', $functions[0]->name->toString());
        self::assertSame('analyseCheckout000002', $functions[1]->name->toString());
        self::assertCount(12, $fixture['expected']);
        self::assertSame($fixture, StressFixture::generate(2));
    }

    public function testDiagnosticSitesCoverEveryPromisedCallForm(): void
    {
        $fixture = StressFixture::generate(1);
        $nodes = (new ParserFactory())->createForNewestSupportedVersion()->parse($fixture['source']) ?? [];
        $sites = (new NodeFinder())->find($nodes, static fn ($node): bool =>
            ($node instanceof MethodCall || $node instanceof StaticCall)
            && array_key_exists($node->getStartLine(), $fixture['expected']));

        self::assertCount(6, $sites);
        self::assertInstanceOf(MethodCall::class, $sites[0]);
        self::assertInstanceOf(StaticCall::class, $sites[1]);
        self::assertInstanceOf(MethodCall::class, $sites[2]);
        self::assertInstanceOf(FuncCall::class, $sites[2]->var);
        self::assertInstanceOf(MethodCall::class, $sites[3]);
        self::assertSame('ttl', $sites[3]->getArgs()[0]->name?->toString());
        self::assertInstanceOf(MethodCall::class, $sites[4]);
        self::assertTrue($sites[4]->getArgs()[0]->unpack);
        self::assertInstanceOf(MethodCall::class, $sites[5]);
        self::assertInstanceOf(Identifier::class, $sites[5]->name);
        self::assertSame('timeout', $sites[5]->name->toString());
    }

    #[DataProvider('invalidSizes')]
    public function testWorkloadSizeIsBounded(int $groups): void
    {
        $this->expectException(\InvalidArgumentException::class);
        StressFixture::generate($groups);
    }

    /** @return iterable<string, array{int}> */
    public static function invalidSizes(): iterable
    {
        yield 'empty' => [0];
        yield 'negative' => [-1];
        yield 'too large' => [10001];
    }
}
