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

use function jbboehr\Yumemi\unit;

/** @param unit_int<'byte'> $bytes */
function acceptGetId3Bytes(int $bytes): void
{
}

/** @param unit_int<'second'>|unit_float<'second'> $duration */
function acceptGetId3Duration(int|float $duration): void
{
}

/** @param unit_int<'css_pixel'> $pixels */
function acceptGetId3CssPixels(int $pixels): void
{
}

$getId3 = new getID3();
$getId3->openfile(__FILE__, 1024);
$getId3->analyze(__FILE__, unit(1, 'kilobyte'));
$info = $getId3->analyze(__FILE__);

if (isset($info['filesize'])) {
    acceptGetId3Duration($info['filesize']);
}

if (isset($info['playtime_seconds'])) {
    acceptGetId3Bytes($info['playtime_seconds']);
}

if (isset($info['bitrate'])) {
    acceptGetId3Duration($info['bitrate']);
}

if (isset($info['audio']['sample_rate'])) {
    acceptGetId3Duration($info['audio']['sample_rate']);
}

if (isset($info['video']['frame_rate'])) {
    acceptGetId3Bytes($info['video']['frame_rate']);
}

if (isset($info['video']['resolution_x'])) {
    acceptGetId3CssPixels($info['video']['resolution_x']);
}
