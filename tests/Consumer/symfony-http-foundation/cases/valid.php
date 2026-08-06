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
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;

use function jbboehr\Yumemi\unit;
use function PHPStan\Testing\assertType;

$response = new Response();
$response->setMaxAge(unit(60, 'second'));
$response->setSharedMaxAge(unit(120, 'second'));
$response->setStaleIfError(unit(30, 'second'));
$response->setStaleWhileRevalidate(unit(15, 'second'));
$response->setTtl(unit(90, 'second'));
$response->setClientTtl(unit(45, 'second'));
$response->setCache([
    'max_age' => unit(60, 'second'),
    's_maxage' => unit(120, 'second'),
    'stale_if_error' => unit(30, 'second'),
    'stale_while_revalidate' => unit(15, 'second'),
    'public' => true,
    'etag' => 'report-v1',
]);

assertType("unit_int<'second'>", $response->getAge());
assertType("unit_int<'second'>|null", $response->getMaxAge());
assertType("unit_int<'second'>|null", $response->getTtl());

$cookie = new Cookie('report', 'ready', new DateTimeImmutable('+1 hour'));
assertType("unit_int<'second'>", $cookie->getMaxAge());
assertType("unit_float<'octet'>|unit_int<'octet'>", UploadedFile::getMaxFilesize());

function refreshHttpFoundationSession(SessionInterface $session, Session $concrete): void
{
    $session->invalidate(unit(3600, 'second'));
    $session->migrate(false, unit(1800, 'second'));
    $session->migrate(lifetime: null);
    $concrete->invalidate(unit(3600, 'second'));
    $concrete->migrate(false, unit(1800, 'second'));
}

function refreshHttpFoundationStorage(
    SessionStorageInterface $storage,
    NativeSessionStorage $nativeStorage,
    MetadataBag $metadata,
): void {
    $storage->regenerate(false, unit(3600, 'second'));
    $nativeStorage->regenerate(false, unit(3600, 'second'));
    assertType("unit_int<'second'>", $metadata->getLifetime());
}
