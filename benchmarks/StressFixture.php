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

namespace jbboehr\Yumemi\Apocrypha\Benchmarks;

final class StressFixture
{
    /** @return array{source: string, expected: array<int, string>} */
    public static function generate(int $groups): array
    {
        if ($groups < 1 || $groups > 10000) {
            throw new \InvalidArgumentException('Stress fixture groups must be between 1 and 10000.');
        }

        $lines = explode("\n", <<<'PHP'
<?php

declare(strict_types=1);

namespace ApocryphaStress;

use Illuminate\Cache\Repository;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use function jbboehr\Yumemi\unit;

final class PlainCache
{
    public function put(string $key, mixed $value, int $ttl): void {}
}

PHP);
        $body = <<<'PHP'
function analyseCheckout__INDEX__(Repository $cache, PendingRequest $request, PlainCache $ordinary): void
{
    $seconds = unit(60, 'second');
    $minutes = unit(1, 'minute');
    $milliseconds = unit(500, 'millisecond');

    $cache->put('direct-__INDEX__', 'receipt', $seconds);
    $cache->put('direct-__INDEX__', 'receipt', $minutes); // expect: 1&unit_int<'minute'> given
    Cache::put('facade-__INDEX__', 'receipt', $seconds);
    Cache::put('facade-__INDEX__', 'receipt', $minutes); // expect: 1&unit_int<'minute'> given
    cache()->put('helper-__INDEX__', 'receipt', $seconds);
    cache()->put('helper-__INDEX__', 'receipt', $minutes); // expect: 1&unit_int<'minute'> given
    $cache->put(ttl: $seconds, value: 'receipt', key: 'named-__INDEX__');
    $cache->put(ttl: $minutes, value: 'receipt', key: 'named-__INDEX__'); // expect: 1&unit_int<'minute'> given
    $cache->put(...['unpacked-__INDEX__', 'receipt', $seconds]);
    $cache->put(...['unpacked-__INDEX__', 'receipt', $minutes]); // expect: 1&unit_int<'minute'> given
    $request->timeout($seconds);
    $request->timeout($milliseconds); // expect: 500&unit_int<'1/1000 * second'> given

    $ordinary->put('ordinary-__INDEX__', 'receipt', 60);
    $ordinary->put('ordinary-__INDEX__', 'receipt', $minutes);
}

PHP;
        $expected = [];
        for ($group = 1; $group <= $groups; ++$group) {
            foreach (explode("\n", str_replace('__INDEX__', sprintf('%06d', $group), $body)) as $line) {
                $lines[] = $line;
                $marker = strpos($line, '// expect: ');
                if ($marker !== false) {
                    $expected[count($lines)] = substr($line, $marker + strlen('// expect: '));
                }
            }
        }

        return ['source' => implode("\n", $lines), 'expected' => $expected];
    }
}
