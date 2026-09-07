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

use jbboehr\Yumemi\Apocrypha\Benchmarks\StressAssertions;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class StressAssertionsTest extends TestCase
{
    public function testAcceptsTheExactDiagnosticContractAfterDebugOutput(): void
    {
        self::assertSame([], StressAssertions::diagnosticErrors(
            "/fixture.php\n" . self::report(),
            [10 => "1&unit_int<'minute'> given"],
            '/fixture.php',
            1,
        ));
    }

    #[DataProvider('incorrectReports')]
    public function testRejectsFalseSuccesses(string $report, int $exitCode): void
    {
        self::assertNotEmpty(StressAssertions::diagnosticErrors(
            $report,
            [10 => "1&unit_int<'minute'> given"],
            '/fixture.php',
            $exitCode,
        ));
    }

    /** @return iterable<string, array{string, int}> */
    public static function incorrectReports(): iterable
    {
        yield 'missing diagnostic' => [self::report(messages: []), 1];
        yield 'wrong line, same count' => [self::report(line: 11), 1];
        yield 'wrong identifier, same count' => [self::report(identifier: 'argument.type'), 1];
        yield 'wrong unit, same count' => [self::report(message: "1&unit_int<'meter'> given"), 1];
        yield 'wrong file, same count' => [self::report(file: '/other.php'), 1];
        yield 'duplicate diagnostic' => [self::report(messages: [self::message(), self::message()]), 1];
        yield 'incorrect totals' => [self::report(total: 2), 1];
        yield 'general error' => [self::report(errors: ['Internal error']), 1];
        yield 'success exit' => [self::report(), 0];
        yield 'crash exit' => [self::report(), 255];
        yield 'invalid JSON' => ['PHPStan crashed', 1];
    }

    public function testRejectsAnEmptyExpectedContract(): void
    {
        self::assertNotEmpty(StressAssertions::diagnosticErrors(self::report(messages: []), [], '/fixture.php', 1));
    }

    public function testRejectsStderrEvenWhenTheExpectedDiagnosticsAreComplete(): void
    {
        self::assertNotEmpty(StressAssertions::diagnosticErrors(
            self::report(),
            [10 => "1&unit_int<'minute'> given"],
            '/fixture.php',
            1,
            "PHP Warning: bootstrap failed to initialize.\n",
        ));
    }

    public function testAcceptsLinearGrowthWithANoisySample(): void
    {
        self::assertSame([], StressAssertions::scalingErrors([
            50 => [self::sample(1.0), self::sample(1.1), self::sample(1.0)],
            200 => [self::sample(3.0), self::sample(3.1), self::sample(12.0)],
            800 => [self::sample(9.0, 400000), self::sample(9.1, 400000), self::sample(9.2, 400000)],
        ]));
    }

    public function testRejectsQuadraticTimeGrowth(): void
    {
        self::assertNotEmpty(StressAssertions::scalingErrors([
            50 => [self::sample(0.2), self::sample(0.2), self::sample(0.2)],
            200 => [self::sample(2.0), self::sample(2.0), self::sample(2.0)],
            800 => [self::sample(32.0), self::sample(32.0), self::sample(32.0)],
        ]));
    }

    public function testRejectsPeakMemoryBudgetViolationEvenInOneSample(): void
    {
        self::assertNotEmpty(StressAssertions::scalingErrors([
            50 => [self::sample(1.0), self::sample(1.0), self::sample(1.0)],
            200 => [self::sample(2.0), self::sample(2.0), self::sample(2.0)],
            800 => [self::sample(3.0), self::sample(3.0, 800000), self::sample(3.0)],
        ]));
    }

    public function testRejectsQuadraticMemoryGrowthBelowTheAbsoluteBudget(): void
    {
        self::assertNotEmpty(StressAssertions::scalingErrors([
            50 => [self::sample(1.0, 1000), self::sample(1.0, 1000), self::sample(1.0, 1000)],
            200 => [self::sample(2.0, 10000), self::sample(2.0, 10000), self::sample(2.0, 10000)],
            800 => [self::sample(3.0, 160000), self::sample(3.0, 160000), self::sample(3.0, 160000)],
        ]));
    }

    public function testRejectsOneRunOverTheWallTimeBudget(): void
    {
        self::assertNotEmpty(StressAssertions::scalingErrors([
            50 => [self::sample(1.0), self::sample(1.0), self::sample(1.0)],
            200 => [self::sample(2.0), self::sample(2.0), self::sample(2.0)],
            800 => [self::sample(3.0), self::sample(181.0), self::sample(3.0)],
        ]));
    }

    public function testRejectsIncompleteMeasurements(): void
    {
        self::assertNotEmpty(StressAssertions::scalingErrors([]));
    }

    /** @return array{seconds: float, rssKiB: int} */
    private static function sample(float $seconds, int $rssKiB = 200000): array
    {
        return ['seconds' => $seconds, 'rssKiB' => $rssKiB];
    }

    /** @return array{message: string, line: int, identifier: string} */
    private static function message(): array
    {
        return ['message' => "1&unit_int<'minute'> given", 'line' => 10, 'identifier' => 'apocrypha.unit'];
    }

    /**
     * @param list<array{message: string, line: int, identifier: string}>|null $messages
     * @param list<string> $errors
     */
    private static function report(
        int $line = 10,
        string $identifier = 'apocrypha.unit',
        string $message = "1&unit_int<'minute'> given",
        string $file = '/fixture.php',
        ?array $messages = null,
        ?int $total = null,
        array $errors = [],
    ): string {
        $messages ??= [['message' => $message, 'line' => $line, 'identifier' => $identifier]];

        return json_encode([
            'totals' => ['errors' => count($errors), 'file_errors' => $total ?? count($messages)],
            'files' => [$file => ['errors' => count($messages), 'messages' => $messages]],
            'errors' => $errors,
        ], JSON_THROW_ON_ERROR);
    }
}
