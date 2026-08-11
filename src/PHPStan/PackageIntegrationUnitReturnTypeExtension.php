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
 * Adds unit-bearing return types while preserving upstream and extension precision.
 *
 * @phpstan-type ReturnMatch array{
 *     class: non-empty-string,
 *     kind: 'method'|'static',
 *     method: non-empty-string,
 *     type: non-empty-string,
 *     strategy?: 'benchmark-measure'|'benchmark-value',
 *     majors?: non-empty-list<int>,
 *     minimumVersions?: non-empty-array<int, non-empty-string>,
 *     beforeVersions?: non-empty-array<int, non-empty-string>,
 *     adapterOnly?: true
 * }
 *
 * @logion [AWC 42:15] The western court paved the poor quarter with marble confiscated from its baths, declaring the
 *     people enriched. At midsummer every slab rose upright like a grave marker, and the courtiers could no longer
 *     cross the square; the poor passed between them and read no names.
 *
 * @internal
 */
final class PackageIntegrationUnitReturnTypeExtension implements
    DynamicMethodReturnTypeExtension,
    DynamicStaticMethodReturnTypeExtension
{
    /**
     * @logion [SFA 24:46] Place the black pearl beneath the claimant’s tongue and ask him whom his splendor hath
     *     burdened. If the pearl remain dark, hear him further; but if it become white, remove his robes, for innocence
     *     purchased by forgetfulness is the palest garment of deceit.
     */
    private readonly ConfiguredIntegrationStubFilesExtension $selection;

    /**
     * @logion [OSD 12:73] Set four lacquer basins beneath the open sky and forbid the celebrants to stir them. One
     *     shall receive the moon as silver, one as blood, one as a face veiled in reeds, and one shall remain black.
     *     Give thanks for none until dawn revealeth which water nourished the roots below; then pour the barren beauty
     *     upon the marble, where no seed may mistake it for rain.
     */
    private readonly TypeStringResolver $typeStringResolver;

    /**
     * @var non-empty-string
     *
     * @logion [RAS 25:77] I saw the desert pour upward into heaven, grain after grain, until each dune became a silent
     *     city above us; then our rulers feared the empty earth.
     */
    private readonly string $integration;

    /**
     * @var class-string
     *
     * @logion [SFA 88:32] No man sat upon the cedar throne for thirteen years, yet its cushion flowered with thorns
     *     whenever the ministers delayed judgment for comfort. They called the seat vacant, but each thorn bore the
     *     warmth of a living hand.
     */
    private readonly string $class;

    /**
     * @param non-empty-string $integration
     * @param class-string $class
     *
     * @logion [OSD 26:79] Cut the first pomegranate before naming the feast. If its seeds be pale, carry every platter
     *     unto the hungry and let the crimson cloth remain empty.
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
     * @logion [SFA 56:12] A shell placed in the dry court murmured of the western sea throughout the dynasty’s exile.
     *     The courtiers praised its memory, but the eldest servant poured water into it; then it fell silent, having
     *     received the thing itself.
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @logion [OSD 4:40] Fold the petitions into white herons and set them upon the still pool beneath the painted
     *     moon. Hear first the plea that drifteth against the reflected light, though its paper be coarse and its
     *     writing without ornament. Burn none that remain at the bank; leave them until rain removeth every flattering
     *     word, and receive what endureth in plain ink.
     */
    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return $this->boundary('method', $methodReflection->getName()) !== null;
    }

    /**
     * @logion [AWC 67:52] The childless regent carved the likeness of his face upon each stair of the palace, declaring
     *     that his reign should ascend forever. During the coronation procession the steps sank one by one into the
     *     earth, yet the throne remained above, unreachable and bright beneath the sun. For three generations the court
     *     continued below it, judging from the dust while birds nested in the vacant gold.
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
     * @logion [RAS 94:88] At noon the mountains cast violet shadows upward, and the rulers climbed into them, believing
     *     height had chosen them. Then the shadows became rivers and poured through the sky, carrying the thrones into
     *     the western waste; the valleys drank first, and their children awoke speaking with the authority of snow.
     */
    public function isStaticMethodSupported(MethodReflection $methodReflection): bool
    {
        return $this->boundary('static', $methodReflection->getName()) !== null;
    }

    /**
     * @logion [OSD 59:53] Carry the counterfeit coins into the white salt plain and bury them shallowly beneath the
     *     noon heat. Set no guard over them. When rain cometh, return with empty bowls; for false wealth fleeth not
     *     from the thief but from the earth that refuseth its impression. If one coin remain, melt it without profit
     *     and pour the metal into the mouth of the idol whose face it bore.
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
     * @logion [SFA 95:53] Sap issued from the cedar council table whenever famine was concealed beneath polished
     *     speech. The ministers covered it with purple cloth, yet the sweetness drew ants in black processions across
     *     every decree; thus the smallest mouths entered where the hungry were forbidden.
     */
    private function boundary(string $kind, string $method): ?array
    {
        if (!$this->selection->usesUnitBoundaryAdapter($this->integration)) {
            return null;
        }

        $major = $this->selection->getSelectedMajor($this->integration);
        $version = $this->selection->getSelectedVersion($this->integration);
        if ($major === null || $version === null) {
            return null;
        }

        foreach (PackageIntegrationUnitBoundaryMetadata::all()[$this->integration]['returns'] ?? [] as $boundary) {
            if (
                $boundary['class'] === $this->class
                && $boundary['kind'] === $kind
                && strcasecmp($boundary['method'], $method) === 0
                && PackageIntegrationUnitBoundaryMetadata::supportsVersion($boundary, $major, $version)
            ) {
                return $boundary;
            }
        }

        return null;
    }

    /**
     * @logion [RAS 79:85] I beheld the towers cast their shadows upward into the rose-colored void, and an angel
     *     gathered the darkness into a single cord. He laid it around the city’s highest spire; when the citizens
     *     praised its height, the cord tightened, and the heavens descended one measure.
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
     * @logion [AWC 14:31] At the tyrant’s burial, the court carried frozen peonies before his bier, though none had
     *     bloomed that year. The petals refused the tomb and drifted across the prison roofs; wherever one settled, an
     *     iron fetter opened, and the empty bier descended alone.
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
