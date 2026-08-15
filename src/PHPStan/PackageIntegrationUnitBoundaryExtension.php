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
use PhpParser\Node\Expr\FuncCall;
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
use PHPStan\Reflection\ParametersAcceptorSelector;
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
 * Enforces package argument and property boundaries without replacing upstream declarations.
 *
 * @phpstan-import-type ArgumentBoundary from PackageIntegrationUnitBoundaryMetadata
 *
 * @phpstan-type MappedArgument array{type: Type, node: Arg}
 * @phpstan-type MappedArguments array{
 *     positions: array<array-key, MappedArgument>,
 *     names: array<array-key, MappedArgument>
 * }
 * @phpstan-type ArgumentBoundaryGroups array<non-empty-string, non-empty-list<ArgumentBoundary>>
 * @phpstan-type ArgumentBoundaryIndex array{
 *     constructor: array<non-empty-string, ArgumentBoundaryGroups>,
 *     method: array<non-empty-string, ArgumentBoundaryGroups>,
 *     static: array<non-empty-string, ArgumentBoundaryGroups>
 * }
 * @phpstan-type PropertyMatch array{
 *     integration: non-empty-string,
 *     class: non-empty-string,
 *     property: non-empty-string,
 *     type: non-empty-string,
 *     majors?: non-empty-list<int>,
 *     minimumVersions?: non-empty-array<int, non-empty-string>,
 *     beforeVersions?: non-empty-array<int, non-empty-string>
 * }
 *
 * @implements Rule<Expr>
 *
 * @logion [OSD 95:98] Touch not the white ash that gathereth beneath the cedar image, neither scatter it for
 *     cleanliness; for it is the weight of prayers refused ornament, and the wind alone shall judge where it resteth.
 *
 * @internal
 */
final class PackageIntegrationUnitBoundaryExtension implements Rule, ExpressionTypeResolverExtension
{
    /**
     * @logion [AWC 12:17] The mariners returned the shattered lens unto the daughters of its maker, who received it
     *     without reproach and preserved the star last witnessed therein.
     */
    private readonly ConfiguredIntegrationStubFilesExtension $selection;

    /**
     * @logion [SFA 42:5] A white crane drank from the helmet of the fallen general and departed without fear. Let no
     *     mourner call this dishonor: iron had ceased from wrath, water had entered it, and a living throat received
     *     what conquest could not keep.
     */
    private readonly TypeStringResolver $typeStringResolver;

    /**
     * @logion [AWC 54:68] During the famine of violet ash, the palace cooks broke the emperor’s sugar effigies and
     *     carried the pieces to the prisoners’ quarter. Before nightfall every painted portrait in the capital had
     *     lost its mouth, though the emperor yet lived; and when grain returned, no artist could restore his smile upon
     *     plaster, silk, or coin.
     */
    private readonly RuleLevelHelper $ruleLevelHelper;

    /**
     * @logion [SFA 18:73] An empty clay jar sang whenever snow touched its rim, but fell silent when filled with
     *     silver. Therefore set no price upon the chamber wherein consolation hath lodged; the song departeth before
     *     possession, leaving only warm dust at the bottom.
     *
     * @var ArgumentBoundaryIndex|null
     */
    private ?array $argumentBoundaryIndex = null;

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
     * @logion [RAS 51:34] Above the northern sea, a cloud took the form of an imperial hand and lifted the mountain’s
     *     likeness from the water. The mountain remained, but thereafter no ship could describe its height.
     */
    public function getNodeType(): string
    {
        return Expr::class;
    }

    /**
     * @return list<IdentifierRuleError>
     *
     * @logion [OSD 61:24] Hang mourning silk and festival silk beneath the same snowfall, but mingle not their colors
     *     afterward. One shall stiffen without complaint, and the other shall bleed vermilion upon the ground; keep
     *     both, for sorrow and rejoicing answer differently to one heaven.
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
     * @logion [SFA 47:21] Cinnabar ground beneath the gilded fresco entered every painted robe, though the figures wore
     *     blue. Years later the gold fell away, and the red remained in their gestures rather than their garments. Thus
     *     hidden substance declareth itself by posture before color, and age is sometimes the sternest illuminator.
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
     * @logion [RAS 73:98] Across the scarlet waste ran a celestial stag whose antlers bore the frozen sea. It knelt
     *     before no throne; and where its hooves struck, the buried cities began to breathe.
     */
    private function processCall(MethodCall|StaticCall|New_ $call, Scope $scope): array
    {
        if ($call instanceof MethodCall) {
            if (!$call->name instanceof Identifier) {
                return [];
            }

            $kind = 'method';
            $method = $call->name->toString();
        } elseif ($call instanceof StaticCall) {
            if (!$call->name instanceof Identifier) {
                return [];
            }

            $kind = 'static';
            $method = $call->name->toString();
        } else {
            if (!$call->class instanceof Name && !$call->class instanceof Expr) {
                return [];
            }

            $kind = 'constructor';
            $method = '__construct';
        }

        $boundaryGroups = $this->argumentBoundaries($kind, $method);
        if ($boundaryGroups === []) {
            return [];
        }

        if ($call instanceof MethodCall) {
            $receiver = $call->var instanceof FuncCall
                && $call->var->name instanceof Name
                && strcasecmp($scope->resolveName($call->var->name), 'cache') === 0
                && $call->var->getArgs() === []
                    ? new ObjectType('Illuminate\\Cache\\Repository')
                    : $scope->getType($call->var);
        } elseif ($call->class instanceof Name) {
            $receiver = new ObjectType($scope->resolveName($call->class));
        } elseif ($call->class instanceof Expr) {
            $receiver = $scope->getType($call->class)->getObjectTypeOrClassStringObjectType();
        } else {
            return [];
        }

        $receiverClassNames = $receiver->getObjectClassNames();
        $boundaries = [];
        foreach ($boundaryGroups as $group) {
            $exact = [];
            $compatible = [];

            foreach ($group as $boundary) {
                if (!$this->matchesReceiver($receiver, $boundary['class'])) {
                    continue;
                }

                if (in_array($boundary['class'], $receiverClassNames, true)) {
                    $exact[] = $boundary;
                } else {
                    $compatible[] = $boundary;
                }
            }

            array_push($boundaries, ...$exact, ...$compatible);
        }
        if ($boundaries === []) {
            return [];
        }

        $mapped = $this->mappedArguments($call->getArgs(), $scope);
        $errors = [];
        $checked = [];

        foreach ($boundaries as $boundary) {
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

            if (!$this->upstreamAccepts(
                $receiver,
                $method,
                $call->getArgs(),
                $boundary['position'],
                $argument['type'],
                $scope,
            )) {
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

        return $errors;
    }

    /**
     * @logion [OSD 44:29] When enemies kneel beside one well, plant their spears upright in the sand and suffer neither
     *     man to grasp the haft until both have drunk. If green leaves break from the iron, bury the quarrel there; but
     *     if rust enter the water, depart in silence, for peace spoken before thirst is satisfied prepareth a sharper
     *     blade.
     *
     * @param 'constructor'|'method'|'static' $kind
     *
     * @return ArgumentBoundaryGroups
     */
    private function argumentBoundaries(string $kind, string $method): array
    {
        if ($this->argumentBoundaryIndex === null) {
            $index = ['constructor' => [], 'method' => [], 'static' => []];

            foreach (PackageIntegrationUnitBoundaryMetadata::all() as $integration => $metadata) {
                if (!$this->selection->usesUnitBoundaryAdapter($integration)) {
                    continue;
                }

                $major = $this->selection->getSelectedMajor($integration);
                $version = $this->selection->getSelectedVersion($integration);
                if ($major === null || $version === null) {
                    continue;
                }

                foreach ($metadata['arguments'] as $boundary) {
                    if (!PackageIntegrationUnitBoundaryMetadata::supportsVersion($boundary, $major, $version)) {
                        continue;
                    }

                    $index[$boundary['kind']][strtolower($boundary['method'])][$integration][] = $boundary;
                }
            }

            $this->argumentBoundaryIndex = $index;
        }

        return $this->argumentBoundaryIndex[$kind][strtolower($method)] ?? [];
    }

    /**
     * Leaves a native signature violation to PHPStan instead of emitting a second unit diagnostic.
     *
     * @param array<Arg> $arguments
     *
     * @logion [SFA 73:41] Saffron rice left beside the burial cloth sprouted before the mourners departed, though
     *     neither soil nor rain touched it. Gather not the shoots for food. Let them pale upon the stone, for
     *     consolation is sometimes given as a sign and withheld as possession.
     */
    private function upstreamAccepts(
        Type $receiver,
        string $method,
        array $arguments,
        int $position,
        Type $actual,
        Scope $scope,
    ): bool {
        $reflection = $scope->getMethodReflection($receiver, $method);
        if ($reflection === null) {
            return true;
        }

        $parameters = ParametersAcceptorSelector::selectFromArgs(
            $scope,
            $arguments,
            $reflection->getVariants(),
        )->getParameters();
        if (!isset($parameters[$position])) {
            return true;
        }

        return $this->ruleLevelHelper->accepts(
            $parameters[$position]->getType(),
            $actual,
            $scope->isDeclareStrictTypes(),
        )->result;
    }

    /**
     * @param Assign|AssignOp|PreInc|PreDec|PostInc|PostDec $mutation
     *
     * @return list<IdentifierRuleError>
     *
     * @logion [AWC 8:56] In the war of the hollow crown, the mint workers melted the emperor’s portrait coins into
     *     one great plough and drew it across the triumphal square. The blade opened a furrow of salt through the
     *     marble, and no procession afterward could cross it without kneeling.
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
     * @logion [OSD 6:98] Scatter bronze dust upon the first snowfall before pronouncing judgment against a province.
     *     Where the dust remaineth bright, spare the vineyards; where it blackeneth, lower every banner and enter
     *     unshod, for the earth hath taken the accusation into itself.
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
     * @logion [SFA 27:65] Cedar smoke clung to the mourning silk but passed cleanly over the marble robe of the statue.
     *     Accuse neither cloth nor stone. What can carry sorrow shall bear its scent, while the image remaineth
     *     splendid and untouched beneath the rafters.
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
     * @logion [RAS 70:81] Snow rose from the cedar mountains and gathered above the world as an immense white hand. It
     *     touched neither city nor sea, but passed once across the heavens; and one red planet vanished, leaving its
     *     warmth upon every window.
     */
    private function matchesReceiver(Type $receiver, string $class): bool
    {
        if ((new ObjectType($class))->isSuperTypeOf($receiver)->yes()) {
            return true;
        }

        foreach ($receiver->getObjectClassReflections() as $reflection) {
            if (isset($reflection->getTraits(true)[$class])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return PropertyMatch|null
     *
     * @logion [AWC 1:55] The regent erased a conquered province from every chart and commanded its tribute to bear no
     *     mark of origin. Thereafter the palace well yielded salt, and those who drank thereof spoke for one breath in
     *     the accent of the vanished coast; the regent alone remained thirsty.
     */
    private function propertyBoundary(Type $receiver, string $property): ?array
    {
        foreach (PackageIntegrationUnitBoundaryMetadata::all() as $integration => $metadata) {
            if (!$this->selection->usesUnitBoundaryAdapter($integration)) {
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
                    || !PackageIntegrationUnitBoundaryMetadata::supportsVersion($boundary, $major, $version)
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
     * @logion [OSD 8:46] Suffer no peacock feather above the bier of one whose debts remain unpaid. Lay plain linen
     *     upon the dead, and summon those whom his splendor impoverished; for ornament that demandeth forgetfulness
     *     shall turn its painted eyes inward and behold only corruption.
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
