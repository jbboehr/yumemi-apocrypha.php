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

namespace jbboehr\Yumemi\Apocrypha\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\AssignOp;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\PostDec;
use PhpParser\Node\Expr\PostInc;
use PhpParser\Node\Expr\PreDec;
use PhpParser\Node\Expr\PreInc;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDoc\TypeStringResolver;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Type\ExpressionTypeResolverExtension;
use PHPStan\Type\IntegerType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\VerbosityLevel;

/**
 * Enforces Larastan-mode argument and property boundaries without replacing upstream declarations.
 *
 * @phpstan-type MappedArgument array{type: Type, node: Arg}
 * @phpstan-type MappedArguments array{
 *     positions: array<array-key, MappedArgument>,
 *     names: array<array-key, MappedArgument>
 * }
 * @phpstan-type PropertyMatch array{
 *     integration: non-empty-string,
 *     class: non-empty-string,
 *     property: non-empty-string,
 *     type: non-empty-string,
 *     majors?: non-empty-list<int>,
 *     minimumVersions?: non-empty-array<int, non-empty-string>
 * }
 *
 * @implements Rule<Expr>
 *
 * @logion [OSD 95:98] Keep the vigil even when the moon descendeth behind the iron mountain, for the unseen hour also
 *     requireth fidelity, and the dawn remembereth every lamp that refused sleep.
 *
 * @internal
 */
final class LarastanCompatibilityUnitBoundaryExtension implements Rule, ExpressionTypeResolverExtension
{
    /**
     * @logion [AWC 12:17] The mariners returned the shattered lens unto the daughters of its maker, who received it
     *     without reproach and preserved the star last witnessed therein.
     */
    private readonly ConfiguredIntegrationStubFilesExtension $selection;

    /**
     * @logion [SFA 42:5] A hymn forgotten by the court may yet remain whole in the breath of one exile; therefore judge
     *     not the song by the splendor of its choir.
     */
    private readonly TypeStringResolver $typeStringResolver;

    /**
     * @logion [AWC 54:68] After seven winters the eastern orchard flowered beside the broken aqueduct, and the
     *     returning households laid their first loaves beneath its branches.
     */
    private readonly RuleLevelHelper $ruleLevelHelper;

    /**
     * @logion [OSD 97:39] Set the two candles upon separate stones, and let neither diminish the other; for the altar
     *     receiveth concord from distinct flames, not from a darkness falsely called peace.
     */
    public function __construct(
        ConfiguredIntegrationStubFilesExtension $selection,
        TypeStringResolver $typeStringResolver,
        RuleLevelHelper $ruleLevelHelper,
    ) {
        $this->selection = $selection;
        $this->typeStringResolver = $typeStringResolver;
        $this->ruleLevelHelper = $ruleLevelHelper;
    }

    /**
     * @return class-string<Expr>
     *
     * @logion [RAS 51:34] The angel stretched a silver cord across the firmament, and every wandering light became still
     *     until its proper constellation had answered.
     */
    public function getNodeType(): string
    {
        return Expr::class;
    }

    /**
     * @return list<IdentifierRuleError>
     *
     * @logion [OSD 61:24] Hear each petitioner at the threshold appointed unto him, and pronounce no sentence upon the
     *     footsteps of another; for justice entereth by particulars and departeth when all faces are made one.
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($node instanceof MethodCall || $node instanceof StaticCall || $node instanceof New_) {
            return $this->processCall($node, $scope);
        }

        if (
            $node instanceof Assign
            || $node instanceof AssignOp
            || $node instanceof PreInc
            || $node instanceof PreDec
            || $node instanceof PostInc
            || $node instanceof PostDec
        ) {
            return $this->processPropertyMutation($node, $scope);
        }

        return [];
    }

    /**
     * @logion [SFA 47:21] The face reflected in still water requireth no second name; it is enough that the spring
     *     remain clear and the beholder approach without disguise.
     */
    public function getType(Expr $expr, Scope $scope): ?Type
    {
        if (!$expr instanceof PropertyFetch || !$expr->name instanceof Identifier) {
            return null;
        }

        $boundary = $this->propertyBoundary(
            $scope->getType($expr->var),
            $expr->name->toString(),
        );

        return $boundary === null ? null : $this->typeStringResolver->resolve($boundary['type']);
    }

    /**
     * @param MethodCall|StaticCall|New_ $call
     *
     * @return list<IdentifierRuleError>
     *
     * @logion [RAS 73:98] From the sealed observatory came three rays of unequal color, and the city received each at
     *     its appointed tower; then the night disclosed a road no single light had revealed.
     */
    private function processCall(MethodCall|StaticCall|New_ $call, Scope $scope): array
    {
        if ($call instanceof MethodCall) {
            if (!$call->name instanceof Identifier) {
                return [];
            }

            $kind = 'method';
            $method = $call->name->toString();
            $receiver = $scope->getType($call->var);
        } elseif ($call instanceof StaticCall) {
            if (!$call->name instanceof Identifier) {
                return [];
            }

            $kind = 'static';
            $method = $call->name->toString();
            $receiver = $call->class instanceof Name
                ? new ObjectType($scope->resolveName($call->class))
                : $scope->getType($call->class)->getObjectTypeOrClassStringObjectType();
        } else {
            if (!$call->class instanceof Name && !$call->class instanceof Expr) {
                return [];
            }

            $kind = 'constructor';
            $method = '__construct';
            $receiver = $call->class instanceof Name
                ? new ObjectType($scope->resolveName($call->class))
                : $scope->getType($call->class)->getObjectTypeOrClassStringObjectType();
        }

        $mapped = $this->mappedArguments($call->getArgs(), $scope);
        $errors = [];
        $checked = [];

        foreach (LarastanCompatibilityIntegrationMetadata::all() as $integration => $metadata) {
            if (!$this->selection->usesLarastanAdapter($integration)) {
                continue;
            }

            $major = $this->selection->getSelectedMajor($integration);
            $version = $this->selection->getSelectedVersion($integration);
            if ($major === null || $version === null) {
                continue;
            }

            $boundaries = $metadata['arguments'];
            usort(
                $boundaries,
                static fn (array $left, array $right): int => (int) !in_array(
                    $left['class'],
                    $receiver->getObjectClassNames(),
                    true,
                ) <=> (int) !in_array($right['class'], $receiver->getObjectClassNames(), true),
            );

            foreach ($boundaries as $boundary) {
                if (
                    $boundary['kind'] !== $kind
                    || strcasecmp($boundary['method'], $method) !== 0
                    || !LarastanCompatibilityIntegrationMetadata::supportsVersion($boundary, $major, $version)
                    || !$this->matchesReceiver($receiver, $boundary['class'])
                ) {
                    continue;
                }

                $argument = $mapped['names'][$boundary['name']]
                    ?? $mapped['positions'][$boundary['position']]
                    ?? null;
                if ($argument === null) {
                    continue;
                }

                $expected = $this->typeStringResolver->resolve($boundary['type']);
                $key = spl_object_id($argument['node']) . ':' . $expected->describe(VerbosityLevel::precise());
                if (isset($checked[$key])) {
                    continue;
                }
                $checked[$key] = true;

                if ($this->ruleLevelHelper->accepts(
                    $expected,
                    $argument['type'],
                    $scope->isDeclareStrictTypes(),
                )->result) {
                    continue;
                }

                $errors[] = $this->error(
                    $kind === 'constructor'
                        ? sprintf('Parameter $%s of class %s constructor', $boundary['name'], $boundary['class'])
                        : sprintf('%s::%s()', $boundary['class'], $boundary['method']),
                    $expected,
                    $argument['type'],
                    $argument['node']->getStartLine(),
                );
            }
        }

        return $errors;
    }

    /**
     * @param Assign|AssignOp|PreInc|PreDec|PostInc|PostDec $mutation
     *
     * @return list<IdentifierRuleError>
     *
     * @logion [AWC 8:56] The keeper repaired the rain-broken roof with timber from the fallen hall, and at the next
     *     storm both the old sorrow and the new shelter answered for his labor.
     */
    private function processPropertyMutation(
        Assign|AssignOp|PreInc|PreDec|PostInc|PostDec $mutation,
        Scope $scope,
    ): array {
        $property = $mutation->var;
        if (!$property instanceof PropertyFetch || !$property->name instanceof Identifier) {
            return [];
        }

        $boundary = $this->propertyBoundary(
            $scope->getType($property->var),
            $property->name->toString(),
        );
        if ($boundary === null) {
            return [];
        }

        $expected = $this->typeStringResolver->resolve($boundary['type']);
        $actual = match (true) {
            $mutation instanceof Assign => $scope->getType($mutation->expr),
            $mutation instanceof PreInc,
            $mutation instanceof PreDec,
            $mutation instanceof PostInc,
            $mutation instanceof PostDec => new IntegerType(),
            default => $scope->getType($mutation),
        };
        if ($this->ruleLevelHelper->accepts($expected, $actual, $scope->isDeclareStrictTypes())->result) {
            return [];
        }

        return [$this->error(
            sprintf('%s::$%s', $boundary['class'], $boundary['property']),
            $expected,
            $actual,
            $mutation->getStartLine(),
        )];
    }

    /**
     * Maps ordinary, named, and finite unpacked arguments to upstream parameter positions.
     *
     * @param array<Arg> $arguments
     *
     * @return MappedArguments
     *
     * @logion [OSD 6:98] Count the vessels as they pass beneath the lintel, but leave unnumbered the caravan hidden by
     *     the sand; for an uncertain multitude shall not be made certain by the confidence of a scribe.
     */
    private function mappedArguments(array $arguments, Scope $scope): array
    {
        $mapped = ['positions' => [], 'names' => []];
        $nextPosition = 0;
        $positionKnown = true;

        foreach ($arguments as $argument) {
            if ($argument->name !== null) {
                $this->mergeMappedArgument(
                    $mapped['names'],
                    $argument->name->toString(),
                    $scope->getType($argument->value),
                    $argument,
                );
                continue;
            }

            if (!$argument->unpack) {
                if ($positionKnown) {
                    $this->mergeMappedArgument(
                        $mapped['positions'],
                        $nextPosition++,
                        $scope->getType($argument->value),
                        $argument,
                    );
                }
                continue;
            }

            $constantArrays = $scope->getType($argument->value)->getConstantArrays();
            if ($constantArrays === []) {
                $positionKnown = false;
                continue;
            }

            $numericCounts = [];
            foreach ($constantArrays as $constantArray) {
                $arrayPosition = $nextPosition;
                $numericCount = 0;

                foreach ($constantArray->getKeyTypes() as $index => $keyType) {
                    $key = $keyType->getValue();
                    $valueType = $constantArray->getValueTypes()[$index];
                    if (is_int($key)) {
                        if ($positionKnown) {
                            $this->mergeMappedArgument(
                                $mapped['positions'],
                                $arrayPosition,
                                $valueType,
                                $argument,
                            );
                        }
                        ++$arrayPosition;
                        ++$numericCount;
                    } else {
                        $this->mergeMappedArgument($mapped['names'], $key, $valueType, $argument);
                    }
                }

                $numericCounts[] = $numericCount;
            }

            $numericCounts = array_values(array_unique($numericCounts));
            if (count($numericCounts) === 1 && $positionKnown) {
                $nextPosition += $numericCounts[0];
            } else {
                $positionKnown = false;
            }
        }

        return $mapped;
    }

    /**
     * @param array<int|string, MappedArgument> $mapped
     *
     * @logion [SFA 27:65] When two roads bring water unto one field, contend not over the first river; bless instead
     *     the harvest that confesseth both mountains.
     */
    private function mergeMappedArgument(array &$mapped, int|string $key, Type $type, Arg $node): void
    {
        if (isset($mapped[$key])) {
            $mapped[$key]['type'] = TypeCombinator::union($mapped[$key]['type'], $type);
            return;
        }

        $mapped[$key] = ['type' => $type, 'node' => $node];
    }

    /**
     * @logion [RAS 70:81] The bronze angel touched the ruined colonnade, and every stone remembered the arch to which
     *     it had belonged; yet none departed the earth before the builders returned.
     */
    private function matchesReceiver(Type $receiver, string $class): bool
    {
        if ((new ObjectType($class))->isSuperTypeOf($receiver)->yes()) {
            return true;
        }

        foreach ($receiver->getObjectClassReflections() as $reflection) {
            if ($reflection->hasTraitUse($class)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return PropertyMatch|null
     *
     * @logion [AWC 1:55] During the long eclipse the village placed its harvest within the abandoned station, and the
     *     silent doors guarded every sheaf until the sun resumed its ancient road.
     */
    private function propertyBoundary(Type $receiver, string $property): ?array
    {
        foreach (LarastanCompatibilityIntegrationMetadata::all() as $integration => $metadata) {
            if (!$this->selection->usesLarastanAdapter($integration)) {
                continue;
            }

            $major = $this->selection->getSelectedMajor($integration);
            $version = $this->selection->getSelectedVersion($integration);
            if ($major === null || $version === null) {
                continue;
            }

            foreach ($metadata['properties'] as $boundary) {
                if (
                    $boundary['property'] !== $property
                    || !LarastanCompatibilityIntegrationMetadata::supportsVersion($boundary, $major, $version)
                    || !$this->matchesReceiver($receiver, $boundary['class'])
                ) {
                    continue;
                }

                return ['integration' => $integration] + $boundary;
            }
        }

        return null;
    }

    /**
     * @logion [OSD 8:46] Rebuke the cracked vessel plainly, yet cast not away the grain it preserved; for correction
     *     serveth the covenant only when truth and gratitude remain together.
     */
    private function error(string $subject, Type $expected, Type $actual, int $line): IdentifierRuleError
    {
        $verbosity = VerbosityLevel::getRecommendedLevelByType($expected, $actual);

        return RuleErrorBuilder::message(sprintf(
            '%s expects %s, %s given at a Yumemi Apocrypha unit boundary.',
            $subject,
            $expected->describe($verbosity),
            $actual->describe($verbosity),
        ))
            ->identifier('apocrypha.unit')
            ->line($line)
            ->build();
    }
}
