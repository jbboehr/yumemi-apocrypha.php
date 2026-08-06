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
use jbboehr\Yumemi\Apocrypha\Tools\StubCandidateSurvey\Schema;
use jbboehr\Yumemi\Apocrypha\Tools\StubCandidateSurvey\Selector;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @phpstan-import-type PackageRecord from Schema
 */
#[CoversClass(Selector::class)]
final class SelectorTest extends TestCase
{
    private Config $config;

    protected function setUp(): void
    {
        $this->config = Config::fromFile(__DIR__ . '/../../tools/stub-candidate-survey.json');
    }

    public function testSmallSurveyIncludesEveryStratum(): void
    {
        $packages = [
            $this->package('seed/package', 'curated', null, 1, 'seed'),
            $this->package('geo/package', 'focused', 'gis', 1),
            $this->package('math/package', 'noisy', 'math', 1),
            $this->package('popular/package', 'popular', null, 1),
        ];

        $selected = (new Selector())->select($packages, $this->config, 4);

        self::assertSame(['curated', 'focused', 'noisy', 'popular'], array_column($selected, 'stratum'));
    }

    public function testRepositoryHardCapIsEnforcedAfterBackfilling(): void
    {
        $packages = [];
        for ($index = 1; $index <= 300; ++$index) {
            $packages[] = $this->package(sprintf('vendor%d/package', $index), 'popular', null, $index);
        }

        $selected = (new Selector())->select($packages, $this->config, 250);

        self::assertCount(250, $selected);
        self::assertCount(250, array_unique(array_column($selected, 'key')));
    }

    public function testNoisyOwnerCannotConsumeMoreThanFiveSlots(): void
    {
        $packages = [];
        for ($index = 1; $index <= 10; ++$index) {
            $packages[] = $this->package(sprintf('noisy/package%d', $index), 'noisy', 'math', $index, null, 'shared-owner');
        }
        for ($index = 1; $index <= 50; ++$index) {
            $packages[] = $this->package(sprintf('popular%d/package', $index), 'popular', null, $index);
        }

        $selected = (new Selector())->select($packages, $this->config, 50);
        $noisyForOwner = array_filter(
            $selected,
            static fn (array $repository): bool => 'noisy' === $repository['stratum'] && 'shared-owner' === $repository['owner'],
        );

        self::assertLessThanOrEqual(5, count($noisyForOwner));
    }

    /** @return PackageRecord */
    private function package(
        string $name,
        string $stratum,
        ?string $tag,
        int $rank,
        ?string $role = null,
        ?string $owner = null,
    ): array {
        $vendor = explode('/', $name, 2)[0];
        $packageName = explode('/', $name, 2)[1];
        $owner ??= $vendor;
        $repositoryKey = sprintf('github.com/%s/%s', $vendor, $packageName);

        /** @var PackageRecord */
        return [
            'name' => $name,
            'repositoryKey' => $repositoryKey,
            'repositoryUrl' => 'https://' . $repositoryKey,
            'owner' => $owner,
            'version' => '1.0.0',
            'versionNormalized' => '1.0.0.0',
            'packageType' => 'library',
            'distUrl' => 'https://example.com/archive.zip',
            'distType' => 'zip',
            'sources' => [[
                'stratum' => $stratum,
                'tag' => $tag,
                'rank' => $rank,
                'role' => $role,
            ]],
        ];
    }
}
