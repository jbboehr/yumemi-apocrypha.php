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

namespace jbboehr\Yumemi\Apocrypha\Tests\Tools;

use jbboehr\Yumemi\Apocrypha\Tools\StubCandidateSurvey\UnitLexicon;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(UnitLexicon::class)]
final class UnitLexiconTest extends TestCase
{
    public function testDetectsConfusableTimeScales(): void
    {
        $matches = (new UnitLexicon())->detect('Block for seconds and sleep for milliseconds.', 'documentation');

        self::assertSame(['millisecond', 'second'], $this->scales($matches, 'time'));
    }

    public function testDetectsDegreesAndRadians(): void
    {
        $matches = (new UnitLexicon())->detect('Convert degrees to radians.', 'documentation');

        self::assertSame(['degree', 'radian'], $this->scales($matches, 'angle'));
    }

    public function testDoesNotTreatGenericMinimumAsMinutes(): void
    {
        $matches = (new UnitLexicon())->detect('$minimum = min($values);', 'implementation');

        self::assertSame([], $this->scales($matches, 'time'));
    }

    /**
     * @param list<array{dimension: string, scale: string, term: string, context: string}> $matches
     * @return list<string>
     */
    private function scales(array $matches, string $dimension): array
    {
        $scales = array_column(array_filter(
            $matches,
            static fn (array $match): bool => $dimension === $match['dimension'],
        ), 'scale');
        sort($scales);

        return $scales;
    }
}
