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

use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Http\Request;

use function jbboehr\Yumemi\unit;

function configureAuthenticationDurations(
    SessionGuard $guard,
    RequirePassword $middleware,
    Request $request,
    Closure $next,
    ResponseFactory $responses,
    UrlGenerator $urls,
    ConnectionInterface $connection,
    Hasher $hasher,
): void {
    $guard->setRememberDuration(unit(30, 'minute'));

    new RequirePassword($responses, $urls, unit(10800, 'second'));
    new RequirePassword($responses, $urls, null);
    RequirePassword::using(null, unit(10800, 'second'));
    RequirePassword::using(null, '10800');
    RequirePassword::using(null, null);
    $middleware->handle($request, $next, null, unit(10800, 'second'));
    $middleware->handle($request, $next, null, '10800');
    $middleware->handle($request, $next, null, null);

    new DatabaseTokenRepository(
        $connection,
        $hasher,
        'password_reset_tokens',
        'hash-key',
        throttle: unit(60, 'second'),
    );
}
