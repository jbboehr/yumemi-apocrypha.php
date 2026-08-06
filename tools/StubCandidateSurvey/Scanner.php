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

use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use ZipArchive;

/**
 * @phpstan-import-type DeclarationRecord from Schema
 * @phpstan-import-type FindingRecord from Schema
 * @phpstan-import-type RepositoryRecord from Schema
 * @phpstan-import-type UnitMatch from Schema
 */
final class Scanner
{
    private readonly Parser $parser;

    public function __construct(private readonly UnitLexicon $lexicon)
    {
        $this->parser = (new ParserFactory())->createForNewestSupportedVersion();
    }

    /**
     * @param list<RepositoryRecord> $repositories
     * @return list<FindingRecord>
     */
    public function scanAll(array $repositories, Config $config): array
    {
        $findings = [];
        foreach ($repositories as $repository) {
            if ('downloaded' !== $repository['archiveStatus'] || null === $repository['archivePath']) {
                continue;
            }
            $findings[] = $this->scanRepository($repository, $config);
        }

        return $findings;
    }

    /**
     * @param RepositoryRecord $repository
     * @return FindingRecord
     */
    public function scanRepository(array $repository, Config $config): array
    {
        $archivePath = $repository['archivePath'];
        if (null === $archivePath) {
            throw new HttpException(sprintf('Repository %s has no archive path.', $repository['package']));
        }
        $archive = new ZipArchive();
        if (true !== $archive->open($archivePath, ZipArchive::RDONLY)) {
            throw new HttpException(sprintf('Unable to open archive for scanning: %s', $archivePath));
        }

        /** @var list<DeclarationRecord> $declarations */
        $declarations = [];
        $markdownCount = 0;
        try {
            for ($index = 0; $index < $archive->numFiles; ++$index) {
                $stat = $archive->statIndex($index);
                if (false === $stat) {
                    continue;
                }
                $name = $stat['name'];
                if ($stat['size'] <= 0 || $stat['size'] > $config->archiveLimits['textFileBytes']) {
                    continue;
                }
                if (str_contains('/' . strtolower($name), '/vendor/')) {
                    continue;
                }

                if (1 === preg_match('/\.(?:php|inc)$/i', $name)) {
                    $contents = $archive->getFromIndex($index);
                    if (false !== $contents) {
                        array_push($declarations, ...$this->scanPhp($contents));
                    }
                    continue;
                }

                if ($markdownCount < 250 && $this->isRelevantMarkdown($name)) {
                    $contents = $archive->getFromIndex($index);
                    if (false !== $contents) {
                        $units = $this->lexicon->detect($contents, 'documentation');
                        if ([] !== $units) {
                            ++$markdownCount;
                            $declarations[] = [
                                'class' => null,
                                'name' => $name,
                                'kind' => 'documentation',
                                'line' => 1,
                                'units' => $units,
                            ];
                        }
                    }
                }
            }
        } finally {
            $archive->close();
        }

        return $this->classify($repository, $declarations);
    }

    /** @return list<DeclarationRecord> */
    public function scanPhp(string $source): array
    {
        try {
            $statements = $this->parser->parse($source);
        } catch (Error) {
            return [];
        }
        if (null === $statements) {
            return [];
        }

        $collector = new DeclarationCollector(explode("\n", $source));
        $traverser = new NodeTraverser();
        $traverser->addVisitor($collector);
        $traverser->traverse($statements);

        $declarations = [];
        foreach ($collector->declarations() as $collected) {
            /** @var array<string, UnitMatch> $unitMap */
            $unitMap = [];
            foreach ([
                'signature' => $collected['signature'],
                'documentation' => $collected['documentation'],
                'implementation' => $collected['implementation'],
            ] as $context => $text) {
                foreach ($this->lexicon->detect($text, $context) as $unit) {
                    $unitMap[$unit['dimension'] . "\0" . $unit['scale']] = $unit;
                }
            }
            if ([] === $unitMap) {
                continue;
            }
            $declarations[] = [
                'class' => $collected['class'],
                'name' => $collected['name'],
                'kind' => $collected['kind'],
                'line' => $collected['line'],
                'units' => array_values($unitMap),
            ];
        }

        return $declarations;
    }

    /**
     * @param RepositoryRecord $repository
     * @param list<DeclarationRecord> $declarations
     * @return FindingRecord
     */
    private function classify(array $repository, array $declarations): array
    {
        /** @var array<string, array<string, true>> $allDimensions */
        $allDimensions = [];
        /** @var array<string, array<string, array<string, true>>> $classDimensions */
        $classDimensions = [];
        $signatureCollision = false;
        $evidenceCount = 0;

        foreach ($declarations as $declaration) {
            /** @var array<string, array<string, true>> $declarationDimensions */
            $declarationDimensions = [];
            foreach ($declaration['units'] as $unit) {
                ++$evidenceCount;
                $allDimensions[$unit['dimension']][$unit['scale']] = true;
                $declarationDimensions[$unit['dimension']][$unit['scale']] = true;
                if (null !== $declaration['class']) {
                    $classDimensions[$declaration['class']][$unit['dimension']][$unit['scale']] = true;
                }
            }
            foreach ($declarationDimensions as $scales) {
                if ('documentation' !== $declaration['kind'] && 'class' !== $declaration['kind'] && count($scales) > 1) {
                    $signatureCollision = true;
                }
            }
        }

        $classCollision = false;
        foreach ($classDimensions as $dimensions) {
            foreach ($dimensions as $scales) {
                if (count($scales) > 1) {
                    $classCollision = true;
                }
            }
        }

        $repositoryCollision = false;
        $distinctScaleCount = 0;
        $dimensions = [];
        foreach ($allDimensions as $dimension => $scales) {
            $scaleNames = array_keys($scales);
            sort($scaleNames);
            $dimensions[$dimension] = $scaleNames;
            $distinctScaleCount += count($scaleNames);
            if (count($scaleNames) > 1) {
                $repositoryCollision = true;
            }
        }
        ksort($dimensions);

        $locality = match (true) {
            $signatureCollision => 'signature',
            $classCollision => 'class',
            $repositoryCollision && 1 === count($repository['packages']) => 'package',
            $repositoryCollision => 'repository',
            $distinctScaleCount > 0 => 'single-unit',
            default => 'none',
        };

        return [
            'repositoryKey' => $repository['key'],
            'package' => $repository['package'],
            'version' => $repository['version'],
            'stratum' => $repository['stratum'],
            'locality' => $locality,
            'dimensions' => $dimensions,
            'declarations' => $declarations,
            'evidenceCount' => $evidenceCount,
            'distinctScaleCount' => $distinctScaleCount,
        ];
    }

    private function isRelevantMarkdown(string $name): bool
    {
        $normalized = strtolower(str_replace('\\', '/', $name));

        return 1 === preg_match('~(?:^|/)(?:readme[^/]*\.md|docs?/.*\.md)$~', $normalized);
    }
}
