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

use Illuminate\Concurrency\ConcurrencyManager;
use Illuminate\Concurrency\ForkDriver;
use Illuminate\Concurrency\ProcessDriver;
use Illuminate\Concurrency\SyncDriver;
use Illuminate\Contracts\Concurrency\Driver;
use Illuminate\Support\Facades\Concurrency;

use function jbboehr\Yumemi\unit;

function rejectInvalidConcurrencyTimeouts(
    Driver $driver,
    ForkDriver $fork,
    ProcessDriver $process,
    SyncDriver $sync,
    ConcurrencyManager $manager,
): void {
    $tasks = [static fn (): int => 1];

    $driver->run($tasks, 30);
    $driver->run($tasks, unit(1, 'minute'));
    $fork->run($tasks, unit(1, 'minute'));
    $process->run($tasks, unit(1, 'minute'));
    $sync->run($tasks, unit(1, 'minute'));
    $manager->run($tasks, unit(1, 'minute'));
    Concurrency::run($tasks, unit(1, 'minute'));
}
