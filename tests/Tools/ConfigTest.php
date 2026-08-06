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

use jbboehr\Yumemi\Apocrypha\Tools\StubCandidateSurvey\Config;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(Config::class)]
final class ConfigTest extends TestCase
{
    private Config $config;

    protected function setUp(): void
    {
        $this->config = Config::fromFile(__DIR__ . '/../../tools/stub-candidate-survey.json');
    }

    public function testFullSurveyUsesTheDecisionCompleteQuotas(): void
    {
        self::assertSame(
            ['curated' => 25, 'focused' => 90, 'noisy' => 30, 'popular' => 105],
            $this->config->quotasForLimit(250),
        );
    }

    public function testSmallSurveyPreservesDistinctStrata(): void
    {
        $quotas = $this->config->quotasForLimit(4);

        self::assertSame(4, array_sum($quotas));
        self::assertSame(1, $quotas['curated']);
        self::assertSame(1, $quotas['focused']);
        self::assertSame(1, $quotas['noisy']);
        self::assertSame(1, $quotas['popular']);
    }

    public function testHardCapCannotBeExceeded(): void
    {
        $this->expectException(RuntimeException::class);
        $this->config->quotasForLimit(251);
    }

    public function testScienceProfilePreservesTheExpansionBoundary(): void
    {
        $config = Config::fromFile(__DIR__ . '/../../tools/stub-candidate-survey-science.json');

        self::assertSame('science', $config->profile);
        self::assertFalse($config->backfillFromPopular);
        self::assertSame(
            ['curated' => 25, 'focused' => 165, 'noisy' => 60, 'popular' => 0],
            $config->quotasForLimit(250),
        );
        self::assertCount(250, $config->repositoryBlacklist);
        self::assertCount(25, $config->repositoryWhitelist);
        self::assertSame([], array_values(array_diff($config->repositoryWhitelist, $config->repositoryBlacklist)));
    }

    public function testFrameworkProfilePreservesTheCuratedRepositoryCap(): void
    {
        $config = Config::fromFile(__DIR__ . '/../../tools/stub-candidate-survey-frameworks.json');

        self::assertSame('frameworks', $config->profile);
        self::assertFalse($config->backfillFromPopular);
        self::assertCount(32, $config->seeds);
        self::assertSame(
            ['curated' => 32, 'focused' => 0, 'noisy' => 0, 'popular' => 0],
            $config->quotasForLimit(32),
        );
    }
}
