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

use jbboehr\Akashi\Example;
use jbboehr\Akashi\ExampleCorpus;
use jbboehr\Akashi\Source\MarkdownSource;
use jbboehr\Akashi\Source\MarkedExampleSelector;

/**
 * @phpstan-type ConsumerExample array{document: non-empty-string, consumer: non-empty-string}
 */
final class PublicDocumentationExamples
{
    /**
     * Public Markdown documents in bytewise lexical project-path order.
     *
     * @var non-empty-list<non-empty-string>
     */
    private const DOCUMENTS = [
        'README.md',
        'docs/pages/README.md',
        'docs/pages/SUMMARY.md',
        'docs/pages/contributing/maintaining-integrations.md',
        'docs/pages/getting-started.md',
        'docs/pages/integrations.md',
    ];

    /** @var non-empty-array<non-empty-string, ConsumerExample> */
    private const CONSUMER_EXAMPLES = [
        'readme-cache-invalid' => [
            'document' => 'README.md',
            'consumer' => 'illuminate-cache',
        ],
        'getting-started-invalid' => [
            'document' => 'docs/pages/getting-started.md',
            'consumer' => 'illuminate-cache',
        ],
        'carbon-invalid' => [
            'document' => 'docs/pages/integrations.md',
            'consumer' => 'carbon',
        ],
        'guzzle-invalid' => [
            'document' => 'docs/pages/integrations.md',
            'consumer' => 'guzzle',
        ],
        'getid3-invalid' => [
            'document' => 'docs/pages/integrations.md',
            'consumer' => 'getid3',
        ],
        'illuminate-cache-invalid' => [
            'document' => 'docs/pages/integrations.md',
            'consumer' => 'illuminate-cache',
        ],
        'illuminate-http-invalid' => [
            'document' => 'docs/pages/integrations.md',
            'consumer' => 'illuminate-http',
        ],
        'illuminate-redis-invalid' => [
            'document' => 'docs/pages/integrations.md',
            'consumer' => 'illuminate-redis',
        ],
        'illuminate-validation-invalid' => [
            'document' => 'docs/pages/integrations.md',
            'consumer' => 'illuminate-validation',
        ],
        'intervention-image-invalid' => [
            'document' => 'docs/pages/integrations.md',
            'consumer' => 'intervention-image',
        ],
        'measurements-invalid' => [
            'document' => 'docs/pages/integrations.md',
            'consumer' => 'measurements',
        ],
        'phpgeo-invalid' => [
            'document' => 'docs/pages/integrations.md',
            'consumer' => 'phpgeo',
        ],
        'symfony-http-foundation-invalid' => [
            'document' => 'docs/pages/integrations.md',
            'consumer' => 'symfony-http-foundation',
        ],
        'symfony-stopwatch-invalid' => [
            'document' => 'docs/pages/integrations.md',
            'consumer' => 'symfony-stopwatch',
        ],
    ];

    /**
     * @return non-empty-list<non-empty-string>
     */
    public static function documents(): array
    {
        return self::DOCUMENTS;
    }

    /**
     * @return non-empty-array<non-empty-string, ConsumerExample>
     */
    public static function consumerExamples(): array
    {
        return self::CONSUMER_EXAMPLES;
    }

    public static function corpus(): ExampleCorpus
    {
        $source = MarkdownSource::forProject(dirname(__DIR__, 2))
            ->withMarkerName('yumemi-example');

        foreach (self::DOCUMENTS as $document) {
            $source = $source->includeFile($document);
        }

        return $source->load();
    }

    /**
     * @return non-empty-string
     */
    public static function documentForConsumer(string $consumer, string $marker): string
    {
        $example = self::CONSUMER_EXAMPLES[$marker] ?? null;
        if ($example === null) {
            throw new \InvalidArgumentException(sprintf(
                'Public documentation example marker %s is not declared in the verification manifest.',
                $marker,
            ));
        }

        if ($example['consumer'] !== $consumer) {
            throw new \InvalidArgumentException(sprintf(
                'Public documentation example marker %s belongs to consumer %s, not %s.',
                $marker,
                $example['consumer'],
                $consumer,
            ));
        }

        return $example['document'];
    }

    public static function exampleForConsumer(string $consumer, string $marker): Example
    {
        $document = self::documentForConsumer($consumer, $marker);
        $example = (new MarkedExampleSelector())->select(self::corpus(), $marker);
        if ($example->document->path->value !== $document) {
            throw new \LogicException(sprintf(
                'Public documentation example marker %s resolved to %s instead of %s.',
                $marker,
                $example->document->path->value,
                $document,
            ));
        }

        return $example;
    }

    private function __construct()
    {
    }
}
