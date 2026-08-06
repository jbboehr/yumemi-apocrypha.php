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

use jbboehr\Yumemi\Apocrypha\Tools\StubCandidateSurvey\CachedHttpClient;
use jbboehr\Yumemi\Apocrypha\Tools\StubCandidateSurvey\Config;
use jbboehr\Yumemi\Apocrypha\Tools\StubCandidateSurvey\PackagistClient;
use jbboehr\Yumemi\Apocrypha\Tools\StubCandidateSurvey\RepositoryNormalizer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PackagistClient::class)]
final class PackagistClientTest extends TestCase
{
    private Config $config;
    private PackagistClient $client;

    protected function setUp(): void
    {
        $this->config = Config::fromFile(__DIR__ . '/../../tools/stub-candidate-survey.json');
        $this->client = new PackagistClient(
            new CachedHttpClient(1, 0, 'test'),
            new RepositoryNormalizer(),
            sys_get_temp_dir(),
            true,
        );
    }

    public function testDiscoveryReadsAllFivePopularityPages(): void
    {
        $responses = [];
        for ($page = 1; $page <= 5; ++$page) {
            $packages = [];
            for ($offset = 1; $offset <= 100; ++$offset) {
                $packages[] = ['name' => sprintf('popular%d/package%d', $page, $offset)];
            }
            $responses['popular:' . $page] = json_encode(['packages' => $packages], JSON_THROW_ON_ERROR);
        }
        foreach ($this->config->focusedTags as $tag) {
            $responses['focused:' . $tag] = '{"results":[]}';
        }
        foreach ($this->config->noisyTags as $tag) {
            $responses['noisy:' . $tag] = '{"results":[]}';
        }

        $discoveries = $this->client->parseDiscoveryResponses($responses, $this->config);

        self::assertCount(500 + count($this->config->seeds), $discoveries);
        self::assertContains('popular5/package100', array_column($discoveries, 'name'));
    }

    public function testMetadataExpansionSelectsLatestStableRelease(): void
    {
        $discovery = [
            'name' => 'example/measure',
            'sources' => [['stratum' => 'popular', 'tag' => null, 'rank' => 1, 'role' => null]],
        ];
        $body = json_encode([
            'packages' => [
                'example/measure' => [
                    [
                        'name' => 'example/measure',
                        'version' => '2.0.0-beta1',
                        'version_normalized' => '2.0.0.0-beta1',
                        'type' => 'library',
                        'source' => ['url' => 'git@github.com:Example/Measure.git'],
                        'dist' => ['url' => 'https://example.com/measure.zip', 'type' => 'zip'],
                    ],
                    ['version' => '1.2.0', 'version_normalized' => '1.2.0.0'],
                    ['version' => '1.1.0', 'version_normalized' => '1.1.0.0'],
                ],
            ],
            'minified' => 'composer/2.0',
        ], JSON_THROW_ON_ERROR);

        $release = $this->client->releaseFromP2($discovery, $body);

        self::assertNotNull($release);
        self::assertSame('1.2.0', $release['version']);
        self::assertSame('github.com/example/measure', $release['repositoryKey']);
    }

    public function testPackageStatisticsPreserveAbandonmentAndReach(): void
    {
        $stats = $this->client->statsFromPackageBody('{"package":{"downloads":{"total":10,"monthly":4,"daily":1},"dependents":3,"favers":2,"abandoned":"replacement/package"}}');

        self::assertSame(4, $stats['downloadsMonthly']);
        self::assertSame(3, $stats['dependents']);
        self::assertSame('replacement/package', $stats['abandoned']);
    }
}
