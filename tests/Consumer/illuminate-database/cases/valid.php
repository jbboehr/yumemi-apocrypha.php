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

use Carbon\CarbonInterval;
use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;

use function jbboehr\Yumemi\unit;
use function PHPStan\Testing\assertType;

function monitorDatabaseQueries(Connection $connection, QueryExecuted $event): void
{
    $connection->logQuery('select 1', [], unit(1.25, 'millisecond'));
    $connection->logQuery('select 1', [], null);
    $connection->whenQueryingForLongerThan(
        unit(500, 'millisecond'),
        static function (Connection $connection, QueryExecuted $event): void {
            assertType(Connection::class, $connection);
            assertType(QueryExecuted::class, $event);
        },
    );
    $connection->whenQueryingForLongerThan(unit(500.5, 'millisecond'), static function (): void {
    });
    $connection->whenQueryingForLongerThan(new CarbonInterval('PT1S'), static function (): void {
    });
    $connection->whenQueryingForLongerThan(new DateTimeImmutable('+1 second'), static function (): void {
    });

    assertType("unit_float<'1/1000 * second'>", $connection->totalQueryDuration());
    $event->time = unit(1.25, 'millisecond');

    $measured = new QueryExecuted('select 1', [], unit(1.25, 'millisecond'), $connection);
    $unmeasured = new QueryExecuted('select 1', [], null, $connection);
    assertType("unit_float<'1/1000 * second'>|null", $measured->time);
    assertType("unit_float<'1/1000 * second'>|null", $unmeasured->time);
}
