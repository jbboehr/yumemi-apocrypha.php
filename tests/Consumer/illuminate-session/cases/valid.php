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

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Cookie\QueueingFactory;
use Illuminate\Contracts\Session\Session;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\CacheBasedSessionHandler;
use Illuminate\Session\CookieSessionHandler;
use Illuminate\Session\DatabaseSessionHandler;
use Illuminate\Session\FileSessionHandler;
use Illuminate\Session\NullSessionHandler;
use Illuminate\Session\SessionManager;
use Illuminate\Session\SymfonySessionDecorator;

use function jbboehr\Yumemi\unit;
use function PHPStan\Testing\assertType;

function configureSessionStorage(
    Repository $cache,
    QueueingFactory $cookies,
    ConnectionInterface $connection,
    Container $container,
    Filesystem $files,
    Session $store,
    SessionManager $manager,
): void {
    $array = new ArraySessionHandler(unit(30, 'minute'));
    $cacheHandler = new CacheBasedSessionHandler($cache, unit(30, 'minute'));
    $cookie = new CookieSessionHandler($cookies, unit(30, 'minute'));
    $database = new DatabaseSessionHandler($connection, 'sessions', unit(30, 'minute'), $container);
    $file = new FileSessionHandler($files, '/tmp/sessions', unit(30, 'minute'));
    $null = new NullSessionHandler();

    $array->gc(unit(3600, 'second'));
    $cacheHandler->gc(unit(3600, 'second'));
    $cookie->gc(unit(3600, 'second'));
    $database->gc(unit(3600, 'second'));
    $file->gc(unit(3600, 'second'));
    $null->gc(unit(3600, 'second'));

    $decorator = new SymfonySessionDecorator($store);
    $decorator->invalidate(unit(3600, 'second'));
    $decorator->invalidate(null);
    $decorator->migrate(false, unit(3600, 'second'));
    $decorator->migrate(false, null);

    assertType("unit_int<'second'>", $manager->defaultRouteBlockLockSeconds());
    assertType("unit_int<'second'>", $manager->defaultRouteBlockWaitSeconds());
}
