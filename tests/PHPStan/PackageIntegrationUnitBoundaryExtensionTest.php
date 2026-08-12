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

use jbboehr\Yumemi\Apocrypha\PHPStan\PackageIntegrationUnitBoundaryExtension;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/** @extends RuleTestCase<PackageIntegrationUnitBoundaryExtension> */
final class PackageIntegrationUnitBoundaryExtensionTest extends RuleTestCase
{
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/larastan-compatibility-extensions.neon'];
    }

    protected function getRule(): Rule
    {
        $rule = self::getContainer()->getService('packageIntegrationUnitBoundaryExtension');
        self::assertInstanceOf(PackageIntegrationUnitBoundaryExtension::class, $rule);

        return $rule;
    }

    public function testUnitBoundaryDiagnosticsRunInProcess(): void
    {
        $this->analyse(
            [__DIR__ . '/fixtures/larastan-compatibility-cases.php'],
            [
                [
                    "Illuminate\\Support\\Sleep::sleep() expects unit_float<'second'>|unit_int<'second'>, "
                        . "1&unit_int<'minute'> given at a Yumemi Apocrypha unit boundary.",
                    65,
                ],
                [
                    "Illuminate\\Support\\Sleep::usleep() expects unit_int<'1/1000000 * second'>, "
                        . "1&unit_int<'1/1000 * second'> given at a Yumemi "
                        . 'Apocrypha unit boundary.',
                    66,
                ],
                [
                    "Illuminate\\Support\\Sleep::sleep() expects unit_float<'second'>|unit_int<'second'>, "
                        . "1&unit_int<'minute'> given at a Yumemi Apocrypha unit boundary.",
                    67,
                ],
                [
                    "Illuminate\\Support\\Timebox::call() expects unit_int<'1/1000000 * second'>, "
                        . "1&unit_int<'1/1000 * second'> given at a Yumemi Apocrypha unit boundary.",
                    70,
                ],
                [
                    'Parameter $timeout of class Illuminate\\Queue\\WorkerOptions constructor expects '
                        . "unit_int<'second'>, 1&unit_int<'minute'> given at a Yumemi Apocrypha unit boundary.",
                    71,
                ],
                [
                    'Parameter $memory of class Illuminate\\Queue\\WorkerOptions constructor expects '
                        . "unit_int<'1048576 * octet'>, 1&unit_int<'octet'> given at a Yumemi Apocrypha unit "
                        . 'boundary.',
                    72,
                ],
                [
                    'Parameter $timeout of class Illuminate\\Queue\\WorkerOptions constructor expects '
                        . "unit_int<'second'>, 1&unit_int<'minute'> given at a Yumemi Apocrypha unit boundary.",
                    72,
                ],
                [
                    'Parameter $timeout of class Illuminate\\Queue\\WorkerOptions constructor expects '
                        . "unit_int<'second'>, 1&unit_int<'minute'> given at a Yumemi Apocrypha unit boundary.",
                    78,
                ],
                [
                    'Parameter $memory of class Illuminate\\Queue\\WorkerOptions constructor expects '
                        . "unit_int<'1048576 * octet'>, 1&unit_int<'octet'> given at a Yumemi Apocrypha unit "
                        . 'boundary.',
                    80,
                ],
                [
                    'Parameter $timeout of class Illuminate\\Queue\\WorkerOptions constructor expects '
                        . "unit_int<'second'>, 1&unit_int<'minute'> given at a Yumemi Apocrypha unit boundary.",
                    80,
                ],
                [
                    'Parameter $memory of class Illuminate\\Queue\\WorkerOptions constructor expects '
                        . "unit_int<'1048576 * octet'>, 1&unit_int<'octet'> given at a Yumemi Apocrypha unit "
                        . 'boundary.',
                    81,
                ],
                [
                    'Parameter $timeout of class Illuminate\\Queue\\WorkerOptions constructor expects '
                        . "unit_int<'second'>, 1&unit_int<'minute'> given at a Yumemi Apocrypha unit boundary.",
                    81,
                ],
                [
                    'Parameter $backoff of class Illuminate\\Queue\\WorkerOptions constructor expects '
                        . "array<unit_int<'second'>>|unit_int<'second'>, int given at a Yumemi Apocrypha unit "
                        . 'boundary.',
                    86,
                ],
                [
                    'Parameter $memory of class Illuminate\\Queue\\WorkerOptions constructor expects '
                        . "unit_int<'1048576 * octet'>, 1&unit_int<'minute'> given at a Yumemi Apocrypha unit "
                        . 'boundary.',
                    86,
                ],
                [
                    "Illuminate\\Queue\\Jobs\\Job::release() expects unit_int<'second'>, "
                        . "1&unit_int<'minute'> given at a Yumemi Apocrypha unit boundary.",
                    89,
                ],
                [
                    "Illuminate\\Queue\\WorkerOptions::\$timeout expects unit_int<'second'>, "
                        . "1&unit_int<'minute'> given at a Yumemi Apocrypha unit boundary.",
                    91,
                ],
                [
                    "Illuminate\\Queue\\WorkerOptions::\$timeout expects unit_int<'second'>, int given at a Yumemi "
                        . 'Apocrypha unit boundary.',
                    93,
                ],
                [
                    "Illuminate\\Queue\\WorkerOptions::\$timeout expects unit_int<'second'>, int given at a Yumemi "
                        . 'Apocrypha unit boundary.',
                    94,
                ],
                [
                    "Illuminate\\Queue\\WorkerOptions::\$timeout expects unit_int<'second'>, int given at a Yumemi "
                        . 'Apocrypha unit boundary.',
                    95,
                ],
                [
                    "Illuminate\\Queue\\WorkerOptions::\$timeout expects unit_int<'second'>, int given at a Yumemi "
                        . 'Apocrypha unit boundary.',
                    96,
                ],
                [
                    "Illuminate\\Support\\Sleep::sleep() expects unit_float<'second'>|unit_int<'second'>, "
                        . "1&unit_int<'minute'> given at a Yumemi Apocrypha unit boundary.",
                    116,
                ],
                [
                    "Illuminate\\Cache\\Repository::put() expects DateInterval|DateTimeInterface|unit_int<'second'>|null, "
                        . "1&unit_int<'minute'> given at a Yumemi Apocrypha unit boundary.",
                    123,
                ],
                [
                    "Carbon\\CarbonInterface::addUTCSeconds() expects unit_float<'second'>|unit_int<'second'>, "
                        . "1&unit_int<'minute'> given at a Yumemi Apocrypha unit boundary.",
                    124,
                ],
            ],
        );
    }

    public function testRedisUnitBoundaryDiagnosticsRunInProcess(): void
    {
        $this->analyse(
            [__DIR__ . '/fixtures/larastan-redis-compatibility-cases.php'],
            [
                [
                    "Illuminate\\Redis\\Limiters\\DurationLimiterBuilder::every() expects "
                        . "DateInterval|DateTimeInterface|unit_int<'second'>, 1&unit_int<'minute'> given at a Yumemi "
                        . 'Apocrypha unit boundary.',
                    49,
                ],
                [
                    "Illuminate\\Redis\\Limiters\\DurationLimiterBuilder::sleep() expects "
                        . "unit_int<'1/1000 * second'>, 1&unit_int<'second'> given at a Yumemi Apocrypha unit boundary.",
                    50,
                ],
                [
                    "Illuminate\\Redis\\Limiters\\DurationLimiterBuilder::\$decay expects unit_int<'second'>, "
                        . "1&unit_int<'minute'> given at a Yumemi Apocrypha unit boundary.",
                    51,
                ],
                [
                    "Illuminate\\Redis\\Limiters\\DurationLimiterBuilder::\$sleep expects "
                        . "unit_int<'1/1000 * second'>, 1&unit_int<'second'> given at a Yumemi Apocrypha unit boundary.",
                    52,
                ],
                [
                    'Parameter $time of class Illuminate\\Redis\\Events\\CommandExecuted constructor expects '
                        . "unit_float<'1/1000 * second'>|null, 1.0&unit_float<'second'> given at a Yumemi Apocrypha "
                        . 'unit boundary.',
                    53,
                ],
            ],
        );
    }

    public function testUnitBoundaryDiagnosticsUseStableIdentifier(): void
    {
        $errors = $this->gatherAnalyserErrors([
            __DIR__ . '/fixtures/larastan-compatibility-cases.php',
        ]);

        self::assertNotEmpty($errors);
        foreach ($errors as $error) {
            self::assertSame('apocrypha.unit', $error->getIdentifier());
        }
    }
}
