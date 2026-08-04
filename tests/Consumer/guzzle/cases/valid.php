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
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\TransferStats;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use function jbboehr\Yumemi\unit;
use function PHPStan\Testing\assertType;

/** @param unit_int<'byte'> $bytes */
function recordGuzzleBytes(int $bytes): void
{
}

/** @param unit_float<'second'> $seconds */
function recordGuzzleSeconds(float $seconds): void
{
}

/**
 * @param unit_int<'byte'> $downloadTotal
 * @param unit_int<'byte'> $downloadedBytes
 * @param unit_int<'byte'> $uploadTotal
 * @param unit_int<'byte'> $uploadedBytes
 */
function trackGuzzleProgress(
    int $downloadTotal,
    int $downloadedBytes,
    int $uploadTotal,
    int $uploadedBytes,
): void {
    recordGuzzleBytes($downloadedBytes);
}

function exerciseGuzzleClientInterface(ClientInterface $client, RequestInterface $request): void
{
    $client->send($request, [
        RequestOptions::READ_TIMEOUT => unit(5, 'second'),
    ]);
    $client->request('GET', '/status', [
        RequestOptions::DELAY => unit(25, 'millisecond'),
    ]);
}

$client = new Client([
    RequestOptions::CONNECT_TIMEOUT => unit(0.5, 'second'),
    RequestOptions::TIMEOUT => unit(10, 'second'),
    'headers' => ['X-Trace-ID' => 'report-42'],
]);

$options = [
    RequestOptions::DELAY => unit(250, 'millisecond'),
    RequestOptions::EXPECT => unit(1048576, 'byte'),
];

$client->request('GET', '/reports', [
    RequestOptions::PROGRESS => trackGuzzleProgress(...),
]);

$client->request('GET', '/reports', $options);
$client->requestAsync('GET', '/reports', $options);
$client->get('/reports', $options);
$client->getAsync('/reports', $options);
exerciseGuzzleClientInterface($client, new Request('GET', '/reports'));

Middleware::retry(
    static fn (
        int $retries,
        RequestInterface $request,
        ?ResponseInterface $response,
        mixed $reason,
    ): bool => $retries < 3,
    static fn (int $retries): int => unit($retries * 100, 'millisecond'),
);

$stats = new TransferStats(new Request('GET', '/reports'), transferTime: unit(0.25, 'second'));
assertType("unit_float<'second'>|null", $stats->getTransferTime());
if (($transferTime = $stats->getTransferTime()) !== null) {
    recordGuzzleSeconds($transferTime);
}
