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

use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\TransferStats;

use function jbboehr\Yumemi\unit;

/** @param unit_float<'millisecond'> $milliseconds */
function recordGuzzleMilliseconds(float $milliseconds): void
{
}

/**
 * @param unit_int<'second'> $downloadTotal
 * @param unit_int<'second'> $downloadedBytes
 * @param unit_int<'second'> $uploadTotal
 * @param unit_int<'second'> $uploadedBytes
 */
function trackGuzzleProgressInSeconds(
    int $downloadTotal,
    int $downloadedBytes,
    int $uploadTotal,
    int $uploadedBytes,
): void {
}

$client = new Client([
    RequestOptions::TIMEOUT => 10,
]);

$client->request('GET', '/reports', [
    RequestOptions::CONNECT_TIMEOUT => unit(500, 'millisecond'),
]);
$client->get('/reports', [
    RequestOptions::READ_TIMEOUT => 5.0,
]);
$client->post('/reports', [
    RequestOptions::DELAY => unit(1, 'second'),
]);
$client->send(new Request('GET', '/reports'), [
    RequestOptions::EXPECT => 1048576,
]);
$client->request('GET', '/reports', [
    RequestOptions::EXPECT => unit(1, 'kilobyte'),
]);
$client->request('GET', '/reports', [
    RequestOptions::PROGRESS => trackGuzzleProgressInSeconds(...),
]);

Middleware::retry(
    static fn (): bool => true,
    static fn (int $retries): int => 100,
);
Middleware::retry(
    static fn (): bool => true,
    static fn (int $retries): int => unit(1, 'second'),
);

new TransferStats(new Request('GET', '/reports'), transferTime: 0.25);
$stats = new TransferStats(
    new Request('GET', '/reports'),
    transferTime: unit(250.0, 'millisecond'),
);
if (($transferTime = $stats->getTransferTime()) !== null) {
    recordGuzzleMilliseconds($transferTime);
}
