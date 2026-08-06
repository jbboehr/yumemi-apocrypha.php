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

namespace jbboehr\Yumemi\Apocrypha\Tools\StubCandidateSurvey;

/**
 * @phpstan-type SourceRecord array{stratum: 'curated'|'focused'|'noisy'|'popular', tag: string|null, rank: int, role: string|null}
 * @phpstan-type DiscoveryRecord array{name: string, sources: non-empty-list<SourceRecord>}
 * @phpstan-type PackageRecord array{
 *     name: string,
 *     repositoryKey: string,
 *     repositoryUrl: string,
 *     owner: string,
 *     version: string,
 *     versionNormalized: string,
 *     packageType: string,
 *     distUrl: string,
 *     distType: string,
 *     sources: non-empty-list<SourceRecord>
 * }
 * @phpstan-type StatsRecord array{downloadsTotal: int, downloadsMonthly: int, downloadsDaily: int, dependents: int, favers: int, abandoned: bool|string}
 * @phpstan-type RepositoryRecord array{
 *     key: string,
 *     url: string,
 *     owner: string,
 *     package: string,
 *     packages: non-empty-list<string>,
 *     version: string,
 *     stratum: 'curated'|'focused'|'noisy'|'popular',
 *     sources: non-empty-list<SourceRecord>,
 *     distUrl: string,
 *     distType: string,
 *     stats: StatsRecord|null,
 *     archivePath: string|null,
 *     archiveSha256: string|null,
 *     archiveBytes: int|null,
 *     archiveStatus: 'pending'|'downloaded'|'failed'|'oversized'|'unsafe',
 *     archiveError: string|null
 * }
 * @phpstan-type UnitMatch array{dimension: string, scale: string, term: string, context: string}
 * @phpstan-type DeclarationRecord array{class: string|null, name: string, kind: string, line: int, units: list<UnitMatch>}
 * @phpstan-type FindingRecord array{
 *     repositoryKey: string,
 *     package: string,
 *     version: string,
 *     stratum: 'curated'|'focused'|'noisy'|'popular',
 *     locality: 'signature'|'class'|'package'|'repository'|'single-unit'|'none',
 *     dimensions: array<string, list<string>>,
 *     declarations: list<DeclarationRecord>,
 *     evidenceCount: int,
 *     distinctScaleCount: int
 * }
 */
final class Schema
{
}
