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

namespace jbboehr\Yumemi\Apocrypha\Tests\ConsumerDiagnostics;

use PHPUnit\Framework\TestCase;

final class StructuredDiagnosticMatcherTest extends TestCase
{
    public function testAcceptsBidirectionallyCoveredDiagnosticsWithoutRequiringMultiplicity(): void
    {
        self::assertSame([], StructuredDiagnosticMatcher::errors($this->json([
            $this->diagnostic('First call expects seconds, minutes given.', 10),
            $this->diagnostic('Second call expects seconds, minutes given.', 20),
        ]), [
            'expects seconds, minutes given',
        ]));
    }

    public function testReportsMissingExpectedFragment(): void
    {
        self::assertSame([
            'Missing expected PHPStan diagnostic containing: expects meters',
        ], StructuredDiagnosticMatcher::errors($this->json([
            $this->diagnostic('Call expects seconds, minutes given.', 10),
        ]), [
            'expects seconds',
            'expects meters',
        ]));
    }

    public function testReportsUnexpectedDiagnosticWithLocationAndIdentifier(): void
    {
        self::assertSame([
            'Unexpected PHPStan diagnostic at /project/invalid.php:20 [method.notFound]: Call to an undefined method.',
        ], StructuredDiagnosticMatcher::errors($this->json([
            $this->diagnostic('Call expects seconds, minutes given.', 10),
            $this->diagnostic('Call to an undefined method.', 20, 'method.notFound'),
        ]), [
            'expects seconds',
        ]));
    }

    public function testReportsGeneralErrors(): void
    {
        self::assertSame([
            'PHPStan reported 1 general error(s).',
            'General PHPStan error: Broken configuration.',
        ], StructuredDiagnosticMatcher::errors(json_encode([
            'totals' => ['errors' => 1, 'file_errors' => 0],
            'files' => [],
            'errors' => ['Broken configuration.'],
        ], JSON_THROW_ON_ERROR), [
            'expects seconds',
        ]));
    }

    public function testRejectsMalformedJson(): void
    {
        self::assertSame([
            'PHPStan did not produce valid JSON: Syntax error',
        ], StructuredDiagnosticMatcher::errors('{', [
            'expects seconds',
        ]));
    }

    public function testRejectsInconsistentFileErrorCount(): void
    {
        self::assertSame([
            'PHPStan reported 2 file error(s), but its files object contains 1 diagnostic(s).',
        ], StructuredDiagnosticMatcher::errors($this->json([
            $this->diagnostic('Call expects seconds, minutes given.', 10),
        ], 2), [
            'expects seconds',
        ]));
    }

    /**
     * @param list<array{message: string, line: int, ignorable: bool, identifier: string}> $messages
     */
    private function json(array $messages, ?int $fileErrors = null): string
    {
        return json_encode([
            'totals' => [
                'errors' => 0,
                'file_errors' => $fileErrors ?? count($messages),
            ],
            'files' => [
                '/project/invalid.php' => [
                    'errors' => count($messages),
                    'messages' => $messages,
                ],
            ],
            'errors' => [],
        ], JSON_THROW_ON_ERROR);
    }

    /** @return array{message: string, line: int, ignorable: bool, identifier: string} */
    private function diagnostic(string $message, int $line, string $identifier = 'argument.type'): array
    {
        return [
            'message' => $message,
            'line' => $line,
            'ignorable' => true,
            'identifier' => $identifier,
        ];
    }
}
