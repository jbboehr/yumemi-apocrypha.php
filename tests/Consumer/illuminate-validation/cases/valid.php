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

use Illuminate\Validation\Rules\Dimensions;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rules\ImageFile;

use function jbboehr\Yumemi\unit;

function configureUploadValidation(Dimensions $dimensions, File $file): void
{
    $dimensions
        ->width(unit(1200, 'pixel'))
        ->height(unit(800, 'pixel'))
        ->minWidth(unit(320, 'pixel'))
        ->minHeight(unit(240, 'pixel'))
        ->maxWidth(unit(3840, 'pixel'))
        ->maxHeight(unit(2160, 'pixel'))
        ->ratio(1.5);

    $binaryKilobytes = unit(512, '1024 * byte');
    $file
        ->size($binaryKilobytes)
        ->between(unit(64, '1024 * byte'), unit(2048, '1024 * byte'))
        ->min(unit(64, '1024 * byte'))
        ->max(unit(2048, '1024 * byte'));

    $file->min('64kb')->max('2mb');
    (new ImageFile())->max(unit(2048, '1024 * byte'))->dimensions($dimensions);

    (new Dimensions())->width(value: unit(640, 'pixel'));
    (new File())->between(
        minSize: unit(64, '1024 * byte'),
        maxSize: unit(2048, '1024 * byte'),
    );
}
