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
use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\ParserFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PackageIntegrationUnitBoundaryMetadataTest extends TestCase
{
    /** @return iterable<string, array{non-empty-string, int, non-empty-string}> */
    public static function integrationMajors(): iterable
    {
        foreach (array_keys(PackageIntegrationUnitBoundaryMetadata::all()) as $integration) {
            if (!str_starts_with($integration, 'illuminate/')) {
                continue;
            }

            foreach ([11, 12, 13] as $major) {
                yield sprintf('%s %d', $integration, $major) => [$integration, $major, $major . '.0.0'];
            }
        }

        yield 'illuminate/queue 11.53.0' => ['illuminate/queue', 11, '11.53.0'];
        yield 'illuminate/queue v11.53.0' => ['illuminate/queue', 11, 'v11.53.0'];
        yield 'illuminate/queue 11.x-dev' => ['illuminate/queue', 11, '11.x-dev'];
        yield 'illuminate/queue 12.59.0' => ['illuminate/queue', 12, '12.59.0'];
        yield 'illuminate/queue 12.60.0' => ['illuminate/queue', 12, '12.60.0'];
        yield 'illuminate/queue 13.9.0' => ['illuminate/queue', 13, '13.9.0'];
        yield 'illuminate/queue 13.10.0' => ['illuminate/queue', 13, '13.10.0'];
        yield 'illuminate/http 11.35.0' => ['illuminate/http', 11, '11.35.0'];
        yield 'illuminate/http normalized 11.35.0' => ['illuminate/http', 11, '11.35.0.0'];
        yield 'illuminate/http v11.35.1' => ['illuminate/http', 11, 'v11.35.1'];
        yield 'illuminate/http normalized 11.35.1' => ['illuminate/http', 11, '11.35.1.0'];
        yield 'illuminate/http 11.x-dev' => ['illuminate/http', 11, '11.x-dev'];
        yield 'illuminate/database 12.50.0' => ['illuminate/database', 12, '12.50.0'];
        yield 'illuminate/database 12.51.0' => ['illuminate/database', 12, '12.51.0'];
        yield 'illuminate/database v12.51.0' => ['illuminate/database', 12, 'v12.51.0'];
        yield 'illuminate/database 12.x-dev' => ['illuminate/database', 12, '12.x-dev'];
        yield 'illuminate/auth 11.30.0' => ['illuminate/auth', 11, '11.30.0'];
        yield 'illuminate/auth 11.31.0' => ['illuminate/auth', 11, '11.31.0'];
        yield 'illuminate/auth 11.44.0' => ['illuminate/auth', 11, '11.44.0'];
        yield 'illuminate/auth 11.45.0' => ['illuminate/auth', 11, '11.45.0'];
        yield 'illuminate/auth 11.x-dev' => ['illuminate/auth', 11, '11.x-dev'];
        yield 'illuminate/auth 12.13.0' => ['illuminate/auth', 12, '12.13.0'];
        yield 'illuminate/auth 12.14.0' => ['illuminate/auth', 12, '12.14.0'];
        yield 'illuminate/auth 12.19.3' => ['illuminate/auth', 12, '12.19.3'];
        yield 'illuminate/auth 12.20.0' => ['illuminate/auth', 12, '12.20.0'];
        yield 'illuminate/auth 12.44.0' => ['illuminate/auth', 12, '12.44.0'];
        yield 'illuminate/auth 12.45.0' => ['illuminate/auth', 12, '12.45.0'];
        yield 'illuminate/auth 12.x-dev' => ['illuminate/auth', 12, '12.x-dev'];
        yield 'illuminate/console 13.1.1' => ['illuminate/console', 13, '13.1.1'];
        yield 'illuminate/console 13.2.0' => ['illuminate/console', 13, '13.2.0'];
        yield 'illuminate/console 13.x-dev' => ['illuminate/console', 13, '13.x-dev'];
        yield 'Intervention Image 3' => ['intervention/image', 3, '3.0.0'];
        yield 'Intervention Image 4' => ['intervention/image', 4, '4.0.0'];
    }

    #[DataProvider('integrationMajors')]
    public function testMetadataExactlyMirrorsSelectedStubAnnotations(
        string $integration,
        int $major,
        string $version,
    ): void {
        self::assertSame(
            $this->stubBoundaries($integration, $major, $version),
            $this->metadataBoundaries($integration, $major, $version),
            sprintf('Package integration metadata drifted from %s stubs for package version %s.', $integration, $version),
        );
    }

    public function testEveryReturnBoundaryClassHasANeonRegistration(): void
    {
        $expected = [];
        foreach (PackageIntegrationUnitBoundaryMetadata::all() as $metadata) {
            foreach ($metadata['returns'] as $boundary) {
                $expected[] = $boundary['class'];
            }
        }
        $expected = array_values(array_unique($expected));
        sort($expected);

        $neon = (string) file_get_contents(__DIR__ . '/../../apocrypha.neon');
        self::assertSame(
            count($expected),
            preg_match_all('/^ {12}class: ((?:Carbon|Illuminate|Intervention)\\\\[^\r\n]+)\r?$/m', $neon, $matches),
        );
        $actual = array_values(array_unique($matches[1]));
        sort($actual);

        self::assertSame($expected, $actual);
        self::assertSame(
            count($expected),
            substr_count($neon, '- phpstan.broker.dynamicMethodReturnTypeExtension'),
        );
        self::assertSame(
            count($expected),
            substr_count($neon, '- phpstan.broker.dynamicStaticMethodReturnTypeExtension'),
        );
    }

    /** @return iterable<string, array{int, non-empty-string, int, int, non-empty-string, non-empty-string}> */
    public static function carbonProfiles(): iterable
    {
        yield 'Carbon 2' => [2, '2.73.0', 10, 8, 'addRealSeconds', 'addUTCSeconds'];
        yield 'Carbon 3 Real' => [3, '3.1.1', 15, 7, 'addRealSeconds', 'addUTCSeconds'];
        yield 'Carbon 3 UTC' => [3, '3.13.2', 15, 12, 'addUTCSeconds', 'addRealSeconds'];
    }

    #[DataProvider('carbonProfiles')]
    public function testCarbonMetadataSelectsOnlyTheInstalledProfile(
        int $major,
        string $version,
        int $argumentCount,
        int $returnCount,
        string $includedAdjustment,
        string $excludedAdjustment,
    ): void {
        $metadata = PackageIntegrationUnitBoundaryMetadata::all()['nesbot/carbon'];
        $arguments = array_values(array_filter(
            $metadata['arguments'],
            static fn (array $boundary): bool => PackageIntegrationUnitBoundaryMetadata::supportsVersion(
                $boundary,
                $major,
                $version,
            ),
        ));
        $returns = array_values(array_filter(
            $metadata['returns'],
            static fn (array $boundary): bool => PackageIntegrationUnitBoundaryMetadata::supportsVersion(
                $boundary,
                $major,
                $version,
            ),
        ));
        $argumentMethods = array_column($arguments, 'method');

        self::assertCount($argumentCount, $arguments);
        self::assertCount($returnCount, $returns);
        self::assertContains($includedAdjustment, $argumentMethods);
        self::assertNotContains($excludedAdjustment, $argumentMethods);
    }

    /**
     * @return array{
     *     arguments: list<array<string, int|string>>,
     *     properties: list<array<string, string>>,
     *     returns: list<array<string, string>>
     * }
     */
    private function stubBoundaries(string $integration, int $major, string $version): array
    {
        $boundaries = ['arguments' => [], 'properties' => [], 'returns' => []];
        $parser = (new ParserFactory())->createForNewestSupportedVersion();

        foreach ($this->stubFiles($integration, $major, $version) as $file) {
            $nodes = $parser->parse((string) file_get_contents($file));
            self::assertNotNull($nodes);

            foreach ($nodes as $node) {
                if (!$node instanceof Namespace_) {
                    continue;
                }

                $namespace = $node->name?->toString() ?? '';
                foreach ($node->stmts as $statement) {
                    if (!$statement instanceof ClassLike || $statement->name === null) {
                        continue;
                    }

                    $class = ltrim($namespace . '\\' . $statement->name->toString(), '\\');
                    foreach ($statement->getMethods() as $method) {
                        $methodName = $method->name->toString();
                        $kind = $methodName === '__construct'
                            ? 'constructor'
                            : ($method->isStatic() ? 'static' : 'method');
                        $positions = [];
                        foreach ($method->params as $position => $parameter) {
                            self::assertInstanceOf(Variable::class, $parameter->var);
                            self::assertIsString($parameter->var->name);
                            $positions[$parameter->var->name] = $position;
                        }

                        foreach ($this->tags($method, 'yumemi-param') as $tag) {
                            self::assertSame(1, preg_match('/^(.+)\s+\$([A-Za-z_][A-Za-z0-9_]*)$/', $tag, $matches));
                            $name = $matches[2];
                            self::assertArrayHasKey($name, $positions);
                            $boundaries['arguments'][] = [
                                'class' => $class,
                                'kind' => $kind,
                                'method' => $methodName,
                                'position' => $positions[$name],
                                'name' => $name,
                                'type' => $this->normalizeType($matches[1]),
                            ];
                        }

                        foreach ($this->tags($method, 'yumemi-return') as $tag) {
                            $boundaries['returns'][] = [
                                'class' => $class,
                                'kind' => $kind,
                                'method' => $methodName,
                                'type' => $this->normalizeType($tag),
                            ];
                        }
                    }

                    foreach ($statement->getProperties() as $property) {
                        foreach ($this->tags($property, 'yumemi-var') as $tag) {
                            foreach ($property->props as $item) {
                                $boundaries['properties'][] = [
                                    'class' => $class,
                                    'property' => $item->name->toString(),
                                    'type' => $this->normalizeType($tag),
                                ];
                            }
                        }
                    }
                }
            }
        }

        return $this->sortBoundaries($boundaries);
    }

    /**
     * @return array{
     *     arguments: list<array<string, int|string>>,
     *     properties: list<array<string, string>>,
     *     returns: list<array<string, string>>
     * }
     */
    private function metadataBoundaries(string $integration, int $major, string $version): array
    {
        $metadata = PackageIntegrationUnitBoundaryMetadata::all()[$integration];
        $boundaries = ['arguments' => [], 'properties' => [], 'returns' => []];

        foreach ($boundaries as $kind => $_) {
            foreach ($metadata[$kind] as $boundary) {
                if (($boundary['adapterOnly'] ?? false) === true) {
                    continue;
                }

                if (!PackageIntegrationUnitBoundaryMetadata::supportsVersion($boundary, $major, $version)) {
                    continue;
                }

                unset(
                    $boundary['majors'],
                    $boundary['minimumVersions'],
                    $boundary['beforeVersions'],
                    $boundary['strategy'],
                    $boundary['adapterOnly'],
                );
                $boundary['type'] = $this->normalizeType($boundary['type']);
                $boundaries[$kind][] = $boundary;
            }
        }

        return $this->sortBoundaries($boundaries);
    }

    /** @return list<string> */
    private function stubFiles(string $integration, int $major, string $version): array
    {
        $base = __DIR__ . '/../../stubs/illuminate/';

        return match ($integration) {
            'illuminate/auth' => $this->authStubFiles($base, $major, $version),
            'illuminate/cache' => [$base . 'cache.stub'],
            'illuminate/console' => [$base . (
                $major === 13
                && ($version === '13.x-dev' || version_compare(ltrim($version, 'v'), '13.2.0', '>='))
                    ? 'console-13.stub'
                    : 'console-11.stub'
            )],
            'illuminate/cookie' => [$base . 'cookie.stub'],
            'illuminate/database' => [
                $base . 'database.stub',
                ...($major === 13 || (
                    $major === 12
                    && ($version === '12.x-dev' || version_compare(ltrim($version, 'v'), '12.51.0', '>='))
                ) ? [$base . 'database-timeout.stub'] : []),
            ],
            'illuminate/filesystem' => [$base . 'filesystem.stub'],
            'illuminate/http' => [$base . (
                $major === 11
                && $version !== '11.x-dev'
                && version_compare(ltrim($version, 'v'), '11.35.1', '<')
                    ? 'http-11.stub'
                    : 'http.stub'
            )],
            'illuminate/process' => [$base . ($major === 13 ? 'process-13.stub' : 'process.stub')],
            'illuminate/queue' => [
                $base . 'queue.stub',
                $base . (
                    $version !== $major . '.x-dev'
                    && version_compare(
                        ltrim($version, 'v'),
                        [11 => '11.53.0', 12 => '12.60.0', 13 => '13.10.0'][$major],
                        '<',
                    )
                        ? 'queue-worker-11.stub'
                        : 'queue-worker-12.stub'
                ),
            ],
            'illuminate/redis' => [$base . 'redis.stub'],
            'illuminate/routing' => [$base . 'routing.stub'],
            'illuminate/session' => [$base . 'session.stub'],
            'illuminate/support' => [$base . 'support.stub'],
            'illuminate/validation' => [$base . 'validation.stub'],
            'intervention/image' => [
                __DIR__ . sprintf('/../../stubs/intervention-image/intervention-image-%d.stub', $major),
            ],
            default => throw new \LogicException(sprintf('Unknown adapter integration %s.', $integration)),
        };
    }

    /** @return list<string> */
    private function authStubFiles(string $base, int $major, string $version): array
    {
        $normalizedVersion = ltrim($version, 'v');
        $developmentVersion = $normalizedVersion === $major . '.x-dev';
        $database = $base . ($major === 11
            ? 'auth-database-token-repository-11.stub'
            : 'auth-database-token-repository-12.stub');
        $cache = $major === 11
            || ($major === 12 && !$developmentVersion && version_compare($normalizedVersion, '12.20.0', '<'))
            ? 'auth-cache-token-repository-with-prefix.stub'
            : 'auth-cache-token-repository.stub';
        $files = [$base . 'auth.stub', $base . 'auth-session-guard.stub', $database];

        if ($major !== 11 || $developmentVersion || version_compare($normalizedVersion, '11.31.0', '>=')) {
            $files[] = $base . $cache;
        }

        $supportsTimebox = $major === 13
            || $developmentVersion
            || version_compare($normalizedVersion, $major === 11 ? '11.45.0' : '12.14.0', '>=');
        if (!$supportsTimebox) {
            return $files;
        }

        $hasHashKey = $major === 13
            || ($major === 12 && (
                $developmentVersion || version_compare($normalizedVersion, '12.45.0', '>=')
            ));
        $files[1] = $base . ($hasHashKey
            ? 'auth-session-guard-timebox-hash-key.stub'
            : 'auth-session-guard-timebox.stub');
        array_splice($files, 2, 0, [$base . 'auth-password-broker-timebox.stub']);

        return $files;
    }

    /** @return list<string> */
    private function tags(Node $node, string $tag): array
    {
        $docComment = $node->getDocComment();
        if ($docComment === null) {
            return [];
        }

        preg_match_all(
            sprintf('/@%s\s+(.+?)(?=\n\s*\*\s*@|\n\s*\*\/)/s', preg_quote($tag, '/')),
            $docComment->getText(),
            $matches,
        );

        return array_map($this->normalizeType(...), $matches[1]);
    }

    private function normalizeType(string $type): string
    {
        $type = preg_replace('/\n\s*\*\s?/', ' ', trim($type));
        self::assertNotNull($type);
        $type = preg_replace('/\s+/', ' ', $type);
        self::assertNotNull($type);

        return trim($type);
    }

    /**
     * @template TArguments of array<string, int|string>
     * @template TProperties of array<string, string>
     * @template TReturns of array<string, string>
     *
     * @param array{
     *     arguments: list<TArguments>,
     *     properties: list<TProperties>,
     *     returns: list<TReturns>
     * } $boundaries
     *
     * @return array{
     *     arguments: list<TArguments>,
     *     properties: list<TProperties>,
     *     returns: list<TReturns>
     * }
     */
    private function sortBoundaries(array $boundaries): array
    {
        foreach ($boundaries as &$items) {
            usort(
                $items,
                static fn (array $left, array $right): int => json_encode($left, JSON_THROW_ON_ERROR)
                    <=> json_encode($right, JSON_THROW_ON_ERROR),
            );
        }
        unset($items);

        return $boundaries;
    }
}
