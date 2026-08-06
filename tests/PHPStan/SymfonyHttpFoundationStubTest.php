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

final class SymfonyHttpFoundationStubTest extends PHPStanTestCase
{
    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__ . '/../../vendor/jbboehr/yumemi/extension.neon',
            __DIR__ . '/../../vendor/jbboehr/yumemi/yumemi-tags.neon',
            __DIR__ . '/symfony-http-foundation-stub.neon',
        ];
    }

    public function testDurationAndByteTagsArePromotedByTheStubParser(): void
    {
        $parser = self::getContainer()->getService('stubParser');
        self::assertInstanceOf(Parser::class, $parser);

        $phpDocs = $this->methodPhpDocs($parser, __DIR__ . '/../../stubs/symfony/http-foundation.stub');
        self::assertStringContainsString("unit_int<'second'>", $phpDocs['Response::setMaxAge']);
        self::assertStringContainsString('YumemiHttpFoundationCacheOptions', $phpDocs['Response::setCache']);
        self::assertStringContainsString("unit_int<'byte'>|unit_float<'byte'>", $phpDocs['UploadedFile::getMaxFilesize']);
        self::assertStringContainsString("unit_int<'second'>|null", $phpDocs['SessionInterface::migrate']);

        $ssePhpDocs = $this->methodPhpDocs($parser, __DIR__ . '/../../stubs/symfony/http-foundation-sse.stub');
        self::assertStringContainsString("unit_int<'millisecond'>", $ssePhpDocs['ServerEvent::setRetry']);

        $ipPhpDocs = $this->methodPhpDocs($parser, __DIR__ . '/../../stubs/symfony/http-foundation-ip.stub');
        self::assertStringContainsString("int<0, 4>&unit_int<'byte'>", $ipPhpDocs['IpUtils::anonymize']);
    }

    /** @return array<string, string> */
    private function methodPhpDocs(Parser $parser, string $file): array
    {
        $phpDocs = [];
        foreach ($parser->parseFile($file) as $node) {
            if (!$node instanceof Namespace_) {
                continue;
            }

            foreach ($node->stmts as $statement) {
                if (!$statement instanceof ClassLike || $statement->name === null) {
                    continue;
                }

                foreach ($statement->getMethods() as $method) {
                    $docComment = $method->getDocComment();
                    if ($docComment === null) {
                        continue;
                    }

                    $phpDocs[$statement->name->toString() . '::' . $method->name->toString()] = $docComment->getText();
                }
            }
        }

        return $phpDocs;
    }
}
