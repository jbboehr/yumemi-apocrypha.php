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

use Intervention\Image\Fraction;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\FontInterface;

use function jbboehr\Yumemi\unit;
use function PHPStan\Testing\assertType;

$manager = new ImageManager(new Intervention\Image\Drivers\Gd\Driver());
$image = $manager->createImage(unit(640, 'pixel'), unit(480, 'pixel'));

assertType("unit_int<'pixel'>", $image->width());
assertType("unit_int<'pixel'>", $image->height());

$image->pixelate(unit(4, 'pixel'));
$image->rotate(unit(45.0, 'degree'));
$image->text(
    'caption',
    unit(20, 'pixel'),
    unit(30, 'pixel'),
    static function (FontInterface $font): void {
    },
);
$image->resize(unit(320, 'pixel'), Fraction::HALF);
$image->resizeDown(Fraction::HALF, unit(240, 'pixel'));
$image->scale(unit(160, 'pixel'), null);
$image->scaleDown(null, unit(120, 'pixel'));
$image->cover(unit(80, 'pixel'), Fraction::HALF);
$image->coverDown(Fraction::HALF, unit(60, 'pixel'));
$image->resizeCanvas(unit(100, 'pixel'), Fraction::HALF);
$image->resizeCanvasRelative(Fraction::HALF, unit(10, 'pixel'));
$image->contain(unit(80, 'pixel'), Fraction::HALF);
$image->containDown(Fraction::HALF, unit(60, 'pixel'));
$image->crop(
    unit(40, 'pixel'),
    Fraction::HALF,
    unit(5, 'pixel'),
    unit(-5, 'pixel'),
);
$image->insert($image, unit(2, 'pixel'), unit(3, 'pixel'));
$image->fill('ffffff', unit(1, 'pixel'), unit(2, 'pixel'));
$image->drawPixel(unit(1, 'pixel'), unit(2, 'pixel'), '000000');

/** @param unit_int<'pixel'> $pixels */
function recordInterventionFourPixels(int $pixels): void
{
}

recordInterventionFourPixels($image->width());

/** @param Intervention\Image\Image<mixed> $image */
function inspectConcreteInterventionFourImage(Intervention\Image\Image $image): void
{
    assertType("unit_int<'pixel'>", $image->width());
    $image->resize(unit(24, 'pixel'), unit(16, 'pixel'));
}
