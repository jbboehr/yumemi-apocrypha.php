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

use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorContract;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Route;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\URL;

use function jbboehr\Yumemi\unit;
use function PHPStan\Testing\assertType;

function configureRoutingDurations(
    UrlGeneratorContract $contract,
    UrlGenerator $generator,
    Redirector $redirector,
    Route $route,
    ThrottleRequests $throttle,
): void {
    $seconds = unit(30, 'second');
    $minutes = unit(2, 'minute');
    $date = new DateTimeImmutable('+30 seconds');
    $interval = new DateInterval('PT30S');

    $contract->signedRoute('report', expiration: $seconds);
    $contract->signedRoute('report', expiration: $date);
    $contract->signedRoute('report', expiration: $interval);
    $contract->signedRoute('report', expiration: null);
    $generator->temporarySignedRoute('report', $seconds);
    $redirector->signedRoute('report', expiration: $seconds);
    $redirector->temporarySignedRoute('report', null);
    URL::signedRoute('report', expiration: $seconds);
    URL::temporarySignedRoute('report', $seconds);
    $route->block($seconds, $seconds)->block(null, null)->middleware('auth');
    ThrottleRequests::with(decayMinutes: $minutes);
    $throttle->handle(new Request(), static fn (Request $request): Request => $request, decayMinutes: unit(0.5, 'minute'));

    assertType("unit_int<'second'>|null", $route->locksFor());
    assertType("unit_int<'second'>|null", $route->waitsFor());
}
