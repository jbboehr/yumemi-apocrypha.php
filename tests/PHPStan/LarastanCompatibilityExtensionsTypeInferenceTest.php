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

use PHPStan\Testing\TypeInferenceTestCase;

final class LarastanCompatibilityExtensionsTypeInferenceTest extends TypeInferenceTestCase
{
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/larastan-compatibility-extensions.neon'];
    }

    protected static function getAdditionalAnalysedFiles(): array
    {
        return [__DIR__ . '/fixtures/larastan-compatibility-upstream.php'];
    }

    public function testUpstreamFixturesAreAvailableToPhpstanReflection(): void
    {
        $reflectionProvider = self::createReflectionProvider();

        foreach ([
            'Illuminate\\Filesystem\\Filesystem',
            'Illuminate\\Queue\\WorkerOptions',
            'Illuminate\\Support\\Benchmark',
        ] as $class) {
            self::assertTrue($reflectionProvider->hasClass($class), sprintf('PHPStan cannot reflect %s.', $class));
        }
    }

    public function testFileAsserts(): void
    {
        foreach (self::gatherAssertTypes(__DIR__ . '/fixtures/larastan-compatibility-cases.php') as $arguments) {
            $this->assertFileAsserts(...$arguments);
        }
    }
}
