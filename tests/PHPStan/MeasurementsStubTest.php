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

use jbboehr\Yumemi\Apocrypha\PHPStan\PackageIntegrationUnitBoundaryMetadata;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Namespace_;
use PHPStan\Parser\Parser;
use PHPStan\Testing\PHPStanTestCase;

final class MeasurementsStubTest extends PHPStanTestCase
{
    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__ . '/../../vendor/jbboehr/yumemi/extension.neon',
            __DIR__ . '/measurements-stub.neon',
        ];
    }

    public function testLengthAndDurationMagicFactoriesPreserveTheCompleteUpstreamSurface(): void
    {
        $parser = self::getContainer()->getService('stubParser');
        self::assertInstanceOf(Parser::class, $parser);

        $phpDocs = $this->classPhpDocs($parser);
        $length = $phpDocs['Measurements\\Quantities\\Length'];
        $duration = $phpDocs['Measurements\\Quantities\\Duration'];

        self::assertSame(21, substr_count($length, '@method'));
        self::assertStringContainsString("Length meters(unit_float<'meter'> \$value)", $length);
        self::assertStringContainsString("Length feet(unit_float<'foot'> \$value)", $length);
        self::assertStringContainsString("Length astronomicalUnits(unit_float<'astronomical_unit'> \$value)", $length);
        self::assertSame(3, substr_count($duration, '@method'));
        self::assertStringContainsString("Duration seconds(unit_float<'second'> \$value)", $duration);
        self::assertStringContainsString("Duration hours(unit_float<'hour'> \$value)", $duration);

        $boundaries = PackageIntegrationUnitBoundaryMetadata::all()['nmarfurt/measurements']['arguments'];
        self::assertCount(24, $boundaries);
        foreach ($boundaries as $boundary) {
            $class = $boundary['class'];
            self::assertArrayHasKey($class, $phpDocs);
            $shortName = substr($class, (int) strrpos($class, '\\') + 1);
            self::assertStringContainsString(
                sprintf('@method static %s %s(%s $value)', $shortName, $boundary['method'], $boundary['type']),
                $phpDocs[$class],
            );
        }
    }

    /** @return array<string, string> */
    private function classPhpDocs(Parser $parser): array
    {
        $phpDocs = [];
        foreach ($parser->parseFile(__DIR__ . '/../../stubs/measurements/measurements.stub') as $node) {
            if (!$node instanceof Namespace_) {
                continue;
            }

            $namespace = $node->name?->toString() ?? '';
            foreach ($node->stmts as $statement) {
                if (!$statement instanceof ClassLike || $statement->name === null) {
                    continue;
                }

                $docComment = $statement->getDocComment();
                if ($docComment !== null) {
                    $class = $namespace . '\\' . $statement->name->toString();
                    $phpDocs[$class] = $docComment->getText();
                }
            }
        }

        return $phpDocs;
    }
}
