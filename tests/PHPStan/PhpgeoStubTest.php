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

use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Namespace_;
use PHPStan\Parser\Parser;
use PHPStan\Testing\PHPStanTestCase;

final class PhpgeoStubTest extends PHPStanTestCase
{
    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__ . '/../../vendor/jbboehr/yumemi/extension.neon',
            __DIR__ . '/../../vendor/jbboehr/yumemi/yumemi-tags.neon',
            __DIR__ . '/phpgeo-stub.neon',
        ];
    }

    public function testDistanceBearingAreaAndToleranceTagsArePromoted(): void
    {
        $parser = self::getContainer()->getService('stubParser');
        self::assertInstanceOf(Parser::class, $parser);

        $phpDocs = $this->methodPhpDocs($parser);

        self::assertStringContainsString(
            "unit_float<'meter'>",
            $phpDocs['Location\\Distance\\DistanceInterface::getDistance'],
        );
        self::assertStringContainsString(
            "unit_float<'degree'> \$bearing",
            $phpDocs['Location\\Bearing\\BearingInterface::calculateDestination'],
        );
        self::assertStringContainsString(
            "unit_float<'meter'> \$distance",
            $phpDocs['Location\\Bearing\\BearingInterface::calculateDestination'],
        );
        self::assertStringContainsString(
            "unit_float<'meter ^ 2'>",
            $phpDocs['Location\\Polygon::getArea'],
        );
        self::assertStringContainsString(
            "unit_float<'meter'> \$north",
            $phpDocs['Location\\CardinalDirection\\CardinalDirectionDistances::setNorth'],
        );
        self::assertStringContainsString(
            "unit_float<'meter'> \$tolerance",
            $phpDocs['Location\\Processor\\Polyline\\SimplifyDouglasPeucker::__construct'],
        );
    }

    /** @return array<string, string> */
    private function methodPhpDocs(Parser $parser): array
    {
        $phpDocs = [];
        foreach ($parser->parseFile(__DIR__ . '/../../stubs/phpgeo/phpgeo.stub') as $node) {
            if (!$node instanceof Namespace_) {
                continue;
            }

            $namespace = $node->name?->toString() ?? '';
            foreach ($node->stmts as $statement) {
                if (!$statement instanceof ClassLike || $statement->name === null) {
                    continue;
                }

                foreach ($statement->getMethods() as $method) {
                    $docComment = $method->getDocComment();
                    if ($docComment !== null) {
                        $class = $namespace . '\\' . $statement->name->toString();
                        $phpDocs[$class . '::' . $method->name->toString()] = $docComment->getText();
                    }
                }
            }
        }

        return $phpDocs;
    }
}
