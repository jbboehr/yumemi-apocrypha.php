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

use Intervention\Image\Geometry\Circle;
use Intervention\Image\Geometry\Ellipse;
use Intervention\Image\Geometry\Rectangle;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\FontInterface;
use Intervention\Image\Interfaces\ImageInterface;

use function jbboehr\Yumemi\unit;
use function PHPStan\Testing\assertType;

$manager = new ImageManager(new Intervention\Image\Drivers\Gd\Driver());
$image = $manager->create(unit(640, 'pixel'), unit(480, 'pixel'));

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
$image->resize(unit(320, 'pixel'), null);
$image->resizeDown(null, unit(240, 'pixel'));
$image->scale(unit(160, 'pixel'), null);
$image->scaleDown(null, unit(120, 'pixel'));
$image->cover(unit(80, 'pixel'), unit(60, 'pixel'));
$image->coverDown(unit(80, 'pixel'), unit(60, 'pixel'));
$image->resizeCanvas(unit(100, 'pixel'), unit(80, 'pixel'));
$image->resizeCanvasRelative(unit(-10, 'pixel'), unit(10, 'pixel'));
$image->pad(unit(80, 'pixel'), unit(60, 'pixel'));
$image->contain(unit(80, 'pixel'), unit(60, 'pixel'));
$image->crop(
    unit(40, 'pixel'),
    unit(30, 'pixel'),
    unit(5, 'pixel'),
    unit(-5, 'pixel'),
);
$image->place($image, 'top-left', unit(2, 'pixel'), unit(3, 'pixel'));
$image->fill('ffffff', unit(1, 'pixel'), unit(2, 'pixel'));
$image->drawPixel(unit(1, 'pixel'), unit(2, 'pixel'), '000000');
$image->drawRectangle(unit(1, 'pixel'), unit(2, 'pixel'), static function (Rectangle $rectangle): void {
});
$image->drawEllipse(unit(1, 'pixel'), unit(2, 'pixel'), static function (Ellipse $ellipse): void {
});
$image->drawCircle(unit(1, 'pixel'), unit(2, 'pixel'), static function (Circle $circle): void {
});

/** @param unit_int<'pixel'> $pixels */
function recordInterventionThreePixels(int $pixels): void
{
}

recordInterventionThreePixels($image->width());

/** @param Intervention\Image\Image<mixed> $image */
function inspectConcreteInterventionThreeImage(Intervention\Image\Image $image): void
{
    assertType("unit_int<'pixel'>", $image->width());
    $image->resize(unit(24, 'pixel'), unit(16, 'pixel'));
}
