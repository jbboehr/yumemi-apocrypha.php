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

namespace jbboehr\Yumemi\Apocrypha\Tests\Documentation;

use jbboehr\Akashi\Integration\PHPStan\ExpectationParser;
use PHPUnit\Framework\TestCase;

final class PublicDocumentationExamplesTest extends TestCase
{
    public function testMarkdownManifestCoversEveryPublicDocument(): void
    {
        self::assertSame(PublicDocumentationExamples::documents(), $this->publicMarkdownDocuments());
    }

    public function testEveryPublicPhpExampleHasAConsumerVerificationRoute(): void
    {
        $manifest = PublicDocumentationExamples::consumerExamples();
        $covered = [];

        foreach (PublicDocumentationExamples::corpus() as $example) {
            $location = sprintf(
                '%s:%d',
                $example->document->path->value,
                $example->location->openingFenceLine,
            );
            $marker = $example->explicitMarkerId?->value;

            self::assertNotNull(
                $marker,
                sprintf('Public PHP example at %s has no yumemi-example verification marker.', $location),
            );
            self::assertArrayHasKey(
                $marker,
                $manifest,
                sprintf('Public PHP example %s at %s has no declared verification route.', $marker, $location),
            );

            $verification = $manifest[$marker];
            self::assertSame(
                $example->document->path->value,
                $verification['document'],
                sprintf('Public PHP example %s is assigned to the wrong document.', $marker),
            );
            self::assertDirectoryExists(
                dirname(__DIR__) . '/Consumer/' . $verification['consumer'],
                sprintf('Public PHP example %s names an unknown consumer fixture.', $marker),
            );
            self::assertNotEmpty(
                (new ExpectationParser())->parse($example),
                sprintf('Public PHP example %s has no standalone //! diagnostic expectation.', $marker),
            );

            $covered[$marker] = $verification;
        }

        $manifestMarkers = array_keys($manifest);
        $coveredMarkers = array_keys($covered);
        sort($manifestMarkers, SORT_STRING);
        sort($coveredMarkers, SORT_STRING);

        self::assertSame(
            $manifestMarkers,
            $coveredMarkers,
            'The verification manifest contains a marker that was not found in the public documentation corpus.',
        );
    }

    public function testEveryConsumerVerificationRouteIsWiredIntoTheHarness(): void
    {
        $contents = file_get_contents(dirname(__DIR__) . '/Consumer/run');
        self::assertNotFalse($contents);

        $matched = preg_match_all(
            '/^[ \t]*run_documentation_phpstan_case[ \t]+([a-z0-9-]+)[^\r\n]*[ \t]+'
                . '([a-z0-9-]+)[ \t]*\r?$/m',
            $contents,
            $matches,
            PREG_SET_ORDER,
        );
        self::assertNotFalse($matched);

        $wired = [];
        foreach ($matches as $match) {
            self::assertArrayNotHasKey(
                $match[2],
                $wired,
                sprintf('Consumer harness wires documentation marker %s more than once.', $match[2]),
            );
            $wired[$match[2]] = $match[1];
        }

        $expected = [];
        foreach (PublicDocumentationExamples::consumerExamples() as $marker => $verification) {
            $expected[$marker] = $verification['consumer'];
        }

        ksort($expected, SORT_STRING);
        ksort($wired, SORT_STRING);

        self::assertSame($expected, $wired);
    }

    /**
     * @return list<string>
     */
    private function publicMarkdownDocuments(): array
    {
        $root = dirname(__DIR__, 2);
        $documents = ['README.md'];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root . '/docs/pages', \FilesystemIterator::SKIP_DOTS),
        );

        foreach ($iterator as $file) {
            if ($file instanceof \SplFileInfo && $file->isFile() && $file->getExtension() === 'md') {
                $documents[] = str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1));
            }
        }

        sort($documents, SORT_STRING);

        return $documents;
    }
}
