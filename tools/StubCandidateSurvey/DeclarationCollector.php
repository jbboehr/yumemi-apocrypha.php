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

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

/**
 * @phpstan-type CollectedDeclaration array{class: string|null, name: non-empty-string, kind: non-empty-string, line: int, signature: string, documentation: string, implementation: string}
 */
final class DeclarationCollector extends NodeVisitorAbstract
{
    /** @var list<string> */
    private array $classStack = [];

    /** @var list<CollectedDeclaration> */
    private array $declarations = [];

    /** @param list<string> $lines */
    public function __construct(private readonly array $lines)
    {
    }

    public function enterNode(Node $node): ?int
    {
        if ($node instanceof Node\Stmt\ClassLike) {
            $name = null !== $node->name ? $node->name->toString() : 'anonymous';
            $this->classStack[] = $name;
            if (null !== $node->name) {
                $this->declarations[] = $this->record($node, $name, 'class', $name, false);
            }

            return null;
        }

        if ($node instanceof Node\Stmt\ClassMethod && $node->isPublic()) {
            $name = $node->name->toString();
            $signature = $name . ' ' . implode(' ', array_map(
                fn (Node\Param $parameter): string => $this->parameterName($parameter),
                $node->params,
            ));
            $this->declarations[] = $this->record($node, $name, 'method', $signature, true);
        } elseif ($node instanceof Node\Stmt\Function_) {
            $name = $node->name->toString();
            $signature = $name . ' ' . implode(' ', array_map(
                fn (Node\Param $parameter): string => $this->parameterName($parameter),
                $node->params,
            ));
            $this->declarations[] = $this->record($node, $name, 'function', $signature, true);
        } elseif ($node instanceof Node\Stmt\Property && $node->isPublic()) {
            foreach ($node->props as $property) {
                $name = $property->name->toString();
                $this->declarations[] = $this->record($node, $name, 'property', $name, false);
            }
        } elseif ($node instanceof Node\Stmt\ClassConst && $node->isPublic()) {
            foreach ($node->consts as $constant) {
                $name = $constant->name->toString();
                $this->declarations[] = $this->record($node, $name, 'constant', $name, false);
            }
        } elseif ($node instanceof Node\Stmt\EnumCase) {
            $name = $node->name->toString();
            $this->declarations[] = $this->record($node, $name, 'enum-case', $name, false);
        }

        return null;
    }

    public function leaveNode(Node $node): ?int
    {
        if ($node instanceof Node\Stmt\ClassLike) {
            array_pop($this->classStack);
        }

        return null;
    }

    /** @return list<CollectedDeclaration> */
    public function declarations(): array
    {
        return $this->declarations;
    }

    /** @return CollectedDeclaration */
    private function record(Node $node, string $name, string $kind, string $signature, bool $includeBody): array
    {
        $documentation = $node->getDocComment()?->getText() ?? '';
        $implementation = '';
        if ($includeBody) {
            $start = max(0, $node->getStartLine() - 1);
            $length = min(120, max(1, $node->getEndLine() - $node->getStartLine() + 1));
            $implementation = implode("\n", array_slice($this->lines, $start, $length));
        }

        /** @var non-empty-string $name */
        /** @var non-empty-string $kind */
        return [
            'class' => [] === $this->classStack ? null : $this->classStack[array_key_last($this->classStack)],
            'name' => $name,
            'kind' => $kind,
            'line' => $node->getStartLine(),
            'signature' => $signature,
            'documentation' => $documentation,
            'implementation' => $implementation,
        ];
    }

    private function parameterName(Node\Param $parameter): string
    {
        if (!$parameter->var instanceof Node\Expr\Variable || !is_string($parameter->var->name)) {
            return '';
        }

        return $parameter->var->name;
    }
}
