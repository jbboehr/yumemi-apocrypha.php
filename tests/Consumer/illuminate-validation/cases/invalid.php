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

use function jbboehr\Yumemi\unit;

function misconfigureUploadValidation(Dimensions $dimensions, File $file): void
{
    $dimensions->width(1200);
    $dimensions->height(unit(800, 'css_pixel'));
    $dimensions->minWidth(unit(320, 'meter'));
    $dimensions->minHeight(unit(240, 'second'));
    $dimensions->maxWidth(unit(3840, 'css_pixel'));
    $dimensions->maxHeight(unit(2160, 'meter'));

    $file->size(512);
    $file->size(unit(512, 'kilobyte'));
    $file->between(unit(64, 'byte'), unit(2, 'megabyte'));
    $file->min(unit(64, 'second'));
    $file->max(unit(2048, 'byte'));
}
