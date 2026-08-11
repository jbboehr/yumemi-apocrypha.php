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

namespace jbboehr\Yumemi\Apocrypha\Tests\Architecture;

use jbboehr\Yumemi\Apocrypha\Exception\ExceptionInterface;
use jbboehr\Yumemi\Apocrypha\Exception\InvalidConfigurationException;
use jbboehr\Yumemi\Apocrypha\Exception\LogicException;
use PHPUnit\Framework\TestCase;

final class PublicApiBoundaryTest extends TestCase
{
    /** @var list<class-string> */
    private const PUBLIC_TYPE_WHITELIST = [
        ExceptionInterface::class,
        InvalidConfigurationException::class,
        LogicException::class,
    ];

    public function testPublicTypeWhitelistIsUniqueAndSorted(): void
    {
        $expected = array_values(array_unique(self::PUBLIC_TYPE_WHITELIST));
        sort($expected, SORT_STRING);

        self::assertSame($expected, self::PUBLIC_TYPE_WHITELIST);
    }

    public function testEveryAutoloadedDeclarationHasAnExplicitApiBoundary(): void
    {
        $declarations = self::declarations();
        $publicTypes = array_fill_keys(self::PUBLIC_TYPE_WHITELIST, null);

        foreach ($declarations as $name => $declaration) {
            $docComment = $declaration->getDocComment();
            $isInternal = $docComment !== false && str_contains($docComment, '@internal');

            if (array_key_exists($name, $publicTypes)) {
                self::assertFalse($isInternal, sprintf('Public API type %s must not be marked @internal.', $name));
                unset($publicTypes[$name]);

                continue;
            }

            self::assertTrue($isInternal, sprintf(
                'Declaration %s must be added to the public API whitelist or marked @internal.',
                $name,
            ));
        }

        self::assertSame([], array_keys($publicTypes), 'Every public API type must resolve to an autoloaded declaration.');
    }

    public function testPublicSignaturesDoNotExposeInternalApocryphaTypes(): void
    {
        $declarations = self::declarations();

        foreach (self::PUBLIC_TYPE_WHITELIST as $name) {
            $declaration = $declarations[$name];
            $parent = $declaration->getParentClass();
            if ($parent !== false) {
                self::assertPublicApocryphaType($parent->getName(), sprintf('%s parent', $name));
            }

            foreach ($declaration->getInterfaceNames() as $interface) {
                self::assertPublicApocryphaType($interface, sprintf('%s interface', $name));
            }

            foreach ($declaration->getTraitNames() as $trait) {
                self::assertPublicApocryphaType($trait, sprintf('%s trait', $name));
            }

            foreach ($declaration->getProperties() as $property) {
                if (!$property->isPrivate()) {
                    self::assertPublicType($property->getType(), sprintf('%s::$%s', $name, $property->getName()));
                }
            }

            foreach ($declaration->getMethods() as $method) {
                if ($method->isPrivate()) {
                    continue;
                }

                foreach ($method->getParameters() as $parameter) {
                    self::assertPublicType(
                        $parameter->getType(),
                        sprintf('%s::%s($%s)', $name, $method->getName(), $parameter->getName()),
                    );
                }

                self::assertPublicType($method->getReturnType(), sprintf('%s::%s() return', $name, $method->getName()));
            }
        }
    }

    /**
     * @return array<class-string, \ReflectionClass<object>>
     */
    private static function declarations(): array
    {
        $sourceRoot = realpath(__DIR__ . '/../../src');
        self::assertNotFalse($sourceRoot);
        $sourceRoot = str_replace('\\', '/', $sourceRoot);
        $declarations = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceRoot, \FilesystemIterator::SKIP_DOTS),
        );

        foreach ($iterator as $file) {
            if (!$file instanceof \SplFileInfo || !$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $path = str_replace('\\', '/', $file->getPathname());
            $relativePath = substr($path, strlen($sourceRoot) + 1, -4);
            $name = 'jbboehr\\Yumemi\\Apocrypha\\' . str_replace('/', '\\', $relativePath);
            self::assertTrue(self::declarationExists($name), sprintf('Unable to autoload declaration %s.', $name));

            /** @var class-string $name */
            $declarations[$name] = new \ReflectionClass($name);
        }

        ksort($declarations, SORT_STRING);

        return $declarations;
    }

    private static function declarationExists(string $name): bool
    {
        return class_exists($name)
            || interface_exists($name)
            || trait_exists($name)
            || enum_exists($name);
    }

    private static function assertPublicType(?\ReflectionType $type, string $context): void
    {
        if ($type === null) {
            return;
        }

        if ($type instanceof \ReflectionNamedType) {
            if (!$type->isBuiltin() && !in_array($type->getName(), ['self', 'parent', 'static'], true)) {
                self::assertPublicApocryphaType($type->getName(), $context);
            }

            return;
        }

        if (!$type instanceof \ReflectionUnionType && !$type instanceof \ReflectionIntersectionType) {
            throw new \LogicException(sprintf('Unsupported reflection type %s.', $type::class));
        }

        foreach ($type->getTypes() as $member) {
            self::assertPublicType($member, $context);
        }
    }

    private static function assertPublicApocryphaType(string $name, string $context): void
    {
        if (!str_starts_with($name, 'jbboehr\\Yumemi\\Apocrypha\\')) {
            return;
        }

        self::assertContains(
            $name,
            self::PUBLIC_TYPE_WHITELIST,
            sprintf('Public signature %s exposes internal Apocrypha type %s.', $context, $name),
        );
    }
}
