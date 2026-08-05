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

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDoc\TypeStringResolver;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantArrayTypeBuilder;
use PHPStan\Type\Constant\ConstantIntegerType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\FloatType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\TypeTraverser;

/**
 * Restores unit-bearing return types while preserving Larastan's unrelated precision.
 *
 * @phpstan-type ReturnMatch array{
 *     class: non-empty-string,
 *     kind: 'method'|'static',
 *     method: non-empty-string,
 *     type: non-empty-string,
 *     strategy?: 'benchmark-measure'|'benchmark-value',
 *     majors?: non-empty-list<int>,
 *     minimumVersions?: non-empty-array<int, non-empty-string>
 * }
 *
 * @logion [AWC 42:15] The western court planted cypress beside the nameless graves, and generations afterward the road
 *     was known by their shade, though no decree had survived the war.
 *
 * @internal
 */
final class LarastanCompatibilityUnitReturnTypeExtension implements
    DynamicMethodReturnTypeExtension,
    DynamicStaticMethodReturnTypeExtension
{
    /**
     * @logion [SFA 24:46] The pilgrim need not possess the horizon; it sufficeth that the road remain true beneath his
     *     feet and the evening star refuse deception.
     */
    private readonly ConfiguredIntegrationStubFilesExtension $selection;

    /**
     * @logion [OSD 12:73] Preserve the inscription upon the weathered stone, and add no flourish unto the missing line;
     *     for reverence confesseth the limit of its witness.
     */
    private readonly TypeStringResolver $typeStringResolver;

    /**
     * @var non-empty-string
     *
     * @logion [RAS 25:77] A voice issued from the violet cloud and named the coast before it rose from the sea; then
     *     every lighthouse turned toward the land not yet visible.
     */
    private readonly string $integration;

    /**
     * @var class-string
     *
     * @logion [SFA 88:32] The chamber is not made holy by its walls alone, but by the promise kept therein when no
     *     visitor standeth before the door.
     */
    private readonly string $class;

    /**
     * @param non-empty-string $integration
     * @param class-string $class
     *
     * @logion [OSD 26:79] Receive the lesser instrument from the elder choir and sound only the note entrusted unto it;
     *     thus shall the full procession remain rich without confusion.
     */
    public function __construct(
        ConfiguredIntegrationStubFilesExtension $selection,
        TypeStringResolver $typeStringResolver,
        string $integration,
        string $class,
    ) {
        $this->selection = $selection;
        $this->typeStringResolver = $typeStringResolver;
        $this->integration = $integration;
        $this->class = $class;
    }

    /**
     * @return class-string
     *
     * @logion [SFA 56:12] The name carved beneath the foundation remaineth after banners and magistrates have passed;
     *     by it the returning children know where their first stone was laid.
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @logion [OSD 4:40] Let the cantor answer only when his verse arriveth, lest zeal outrun the hymn and the assembly
     *     mistake haste for devotion.
     */
    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return $this->boundary('method', $methodReflection->getName()) !== null;
    }

    /**
     * @logion [AWC 67:52] When the mountain road returned from beneath the landslide, the exiles walked upon the same
     *     stones and found their fathers' offerings undisturbed in the clefts.
     */
    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope,
    ): ?Type {
        $boundary = $this->boundary('method', $methodReflection->getName());
        if ($boundary === null) {
            return null;
        }

        $original = ParametersAcceptorSelector::selectFromArgs(
            $scope,
            $methodCall->getArgs(),
            $methodReflection->getVariants(),
        )->getReturnType();

        return TypeCombinator::intersect($original, $this->typeStringResolver->resolve($boundary['type']));
    }

    /**
     * @logion [RAS 94:88] Three stars stood motionless above the drowned basilica, and the fourth continued its course;
     *     then the watchers understood which light had been appointed to summon them.
     */
    public function isStaticMethodSupported(MethodReflection $methodReflection): bool
    {
        return $this->boundary('static', $methodReflection->getName()) !== null;
    }

    /**
     * @logion [OSD 59:53] Pour the restored wine into the ancestral cup, but conceal not the gold newly joined unto its
     *     fracture; for fidelity may strengthen what it receiveth without pretending the wound was never made.
     */
    public function getTypeFromStaticMethodCall(
        MethodReflection $methodReflection,
        StaticCall $methodCall,
        Scope $scope,
    ): ?Type {
        $boundary = $this->boundary('static', $methodReflection->getName());
        if ($boundary === null) {
            return null;
        }

        return match ($boundary['strategy'] ?? null) {
            'benchmark-measure' => $this->benchmarkMeasureType($methodReflection, $methodCall, $scope),
            'benchmark-value' => $this->benchmarkValueType($methodReflection, $methodCall, $scope),
            default => TypeCombinator::intersect(
                ParametersAcceptorSelector::selectFromArgs(
                    $scope,
                    $methodCall->getArgs(),
                    $methodReflection->getVariants(),
                )->getReturnType(),
                $this->typeStringResolver->resolve($boundary['type']),
            ),
        };
    }

    /**
     * @param 'method'|'static' $kind
     *
     * @return ReturnMatch|null
     *
     * @logion [SFA 95:53] A season shall be known by the fruit it ripeneth, not by the calendar proclaimed in distant
     *     halls; nevertheless the wise preserve both witness and number.
     */
    private function boundary(string $kind, string $method): ?array
    {
        if (!$this->selection->usesLarastanAdapter($this->integration)) {
            return null;
        }

        $major = $this->selection->getSelectedMajor($this->integration);
        $version = $this->selection->getSelectedVersion($this->integration);
        if ($major === null || $version === null) {
            return null;
        }

        foreach (LarastanCompatibilityIntegrationMetadata::all()[$this->integration]['returns'] ?? [] as $boundary) {
            if (
                $boundary['class'] === $this->class
                && $boundary['kind'] === $kind
                && strcasecmp($boundary['method'], $method) === 0
                && LarastanCompatibilityIntegrationMetadata::supportsVersion($boundary, $major, $version)
            ) {
                return $boundary;
            }
        }

        return null;
    }

    /**
     * @logion [RAS 79:85] Behold, the artificers awakened the ancient lens, and it divided the pale noon into colors no
     *     living eye had named; yet every ray bowed toward the same invisible sun.
     */
    private function benchmarkMeasureType(
        MethodReflection $methodReflection,
        StaticCall $methodCall,
        Scope $scope,
    ): Type {
        $original = ParametersAcceptorSelector::selectFromArgs(
            $scope,
            $methodCall->getArgs(),
            $methodReflection->getVariants(),
        )->getReturnType();
        $milliseconds = $this->typeStringResolver->resolve("unit_float<'millisecond'>");

        return TypeTraverser::map(
            $original,
            static fn (Type $type, callable $traverse): Type => $type instanceof FloatType
                ? $milliseconds
                : $traverse($type),
        );
    }

    /**
     * @logion [AWC 14:31] The widow placed the new bell beside the cracked one and rang them at dawn; the first carried
     *     her grief, and the second bore it beyond the valley as praise.
     */
    private function benchmarkValueType(
        MethodReflection $methodReflection,
        StaticCall $methodCall,
        Scope $scope,
    ): Type {
        $original = ParametersAcceptorSelector::selectFromArgs(
            $scope,
            $methodCall->getArgs(),
            $methodReflection->getVariants(),
        )->getReturnType();
        $milliseconds = $this->typeStringResolver->resolve("unit_float<'millisecond'>");

        return TypeTraverser::map(
            $original,
            static function (Type $type, callable $traverse) use ($milliseconds): Type {
                if (!$type instanceof ConstantArrayType) {
                    return $traverse($type);
                }

                $builder = ConstantArrayTypeBuilder::createFromConstantArray($type);
                $builder->setOffsetValueType(new ConstantIntegerType(1), $milliseconds);

                return $builder->getArray();
            },
        );
    }
}
