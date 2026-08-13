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

namespace jbboehr\Yumemi\Apocrypha\Tests\PHPStan\Fixtures;

use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\NullSessionHandler;
use Illuminate\Session\SessionManager;
use Illuminate\Session\SymfonySessionDecorator;

use function jbboehr\Yumemi\unit;
use function PHPStan\Testing\assertType;

function exerciseSessionLarastanCompatibility(
    NullSessionHandler $nullHandler,
    SessionManager $manager,
    SymfonySessionDecorator $decorator,
): void {
    new ArraySessionHandler(unit(30, 'second'));
    $nullHandler->gc(unit(1, 'minute'));
    $decorator->invalidate(unit(1, 'minute'));
    $decorator->migrate(false, unit(1, 'minute'));

    assertType("unit_int<'second'>", $manager->defaultRouteBlockLockSeconds());
    assertType("unit_int<'second'>", $manager->defaultRouteBlockWaitSeconds());
}
