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
use function PHPStan\Testing\assertType;

/** @param unit_int<'byte'> $bytes */
function recordGetId3Bytes(int $bytes): void
{
}

/** @param unit_int<'second'>|unit_float<'second'> $duration */
function recordGetId3Duration(int|float $duration): void
{
}

/** @param unit_int<'bit / second'>|unit_float<'bit / second'> $bitrate */
function recordGetId3Bitrate(int|float $bitrate): void
{
}

/** @param unit_int<'hertz'>|unit_float<'hertz'> $frequency */
function recordGetId3Frequency(int|float $frequency): void
{
}

/** @param unit_int<'pixel'> $pixels */
function recordGetId3Pixels(int $pixels): void
{
}

$getId3 = new getID3();
$getId3->openfile(__FILE__, unit(1, 'byte'));
$info = $getId3->analyze(__FILE__, unit(1, 'byte'));

if (isset($info['filesize'])) {
    assertType("unit_int<'octet'>", $info['filesize']);
    recordGetId3Bytes($info['filesize']);
}

if (isset($info['avdataoffset'])) {
    recordGetId3Bytes($info['avdataoffset']);
}

if (isset($info['avdataend'])) {
    recordGetId3Bytes($info['avdataend']);
}

if (isset($info['playtime_seconds'])) {
    assertType("unit_float<'second'>|unit_int<'second'>", $info['playtime_seconds']);
    recordGetId3Duration($info['playtime_seconds']);
}

if (isset($info['bitrate'])) {
    recordGetId3Bitrate($info['bitrate']);
}

if (isset($info['audio']['bitrate']) && $info['audio']['bitrate'] !== 'free') {
    recordGetId3Bitrate($info['audio']['bitrate']);
}

if (isset($info['audio']['sample_rate'])) {
    recordGetId3Frequency($info['audio']['sample_rate']);
}

if (isset($info['video']['bitrate'])) {
    recordGetId3Bitrate($info['video']['bitrate']);
}

if (isset($info['video']['frame_rate'])) {
    recordGetId3Frequency($info['video']['frame_rate']);
}

if (isset($info['video']['resolution_x'])) {
    assertType("unit_int<'pixel'>", $info['video']['resolution_x']);
    recordGetId3Pixels($info['video']['resolution_x']);
}

if (isset($info['video']['resolution_y'])) {
    assertType("unit_int<'pixel'>", $info['video']['resolution_y']);
    recordGetId3Pixels($info['video']['resolution_y']);
}
