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

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;

use function jbboehr\Yumemi\unit;

/** @param unit_int<'meter'> $distance */
function recordHttpFoundationMeters(int $distance): void
{
}

/** @param unit_int<'second'>|unit_float<'second'> $duration */
function recordHttpFoundationUploadSeconds(int|float $duration): void
{
}

$response = new Response();
$response->setMaxAge(unit(250, 'millisecond'));
$response->setSharedMaxAge(unit(1, 'minute'));
$response->setStaleIfError(30);
$response->setStaleWhileRevalidate(unit(250, 'millisecond'));
$response->setTtl(unit(1, 'minute'));
$response->setClientTtl(unit(1, 'minute'));
$response->setCache([
    's_maxage' => unit(2, 'minute'),
    'public' => true,
]);
recordHttpFoundationMeters($response->getAge());

$cookie = new Cookie('report', 'ready', new DateTimeImmutable('+1 hour'));
recordHttpFoundationMeters($cookie->getMaxAge());
recordHttpFoundationUploadSeconds(UploadedFile::getMaxFilesize());

function misuseHttpFoundationSession(SessionInterface $session): void
{
    $session->migrate(false, unit(5, 'minute'));
}

function misuseHttpFoundationStorage(SessionStorageInterface $storage): void
{
    $storage->regenerate(false, unit(5, 'minute'));
}
