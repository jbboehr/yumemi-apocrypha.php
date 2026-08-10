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

/** @param non-empty-list<float|int> $values */
function bookstackBenchmarkMedian(array $values): float
{
    sort($values, SORT_NUMERIC);
    $middle = intdiv(count($values), 2);

    return count($values) % 2 === 1
        ? (float) $values[$middle]
        : ((float) $values[$middle - 1] + (float) $values[$middle]) / 2;
}

$resultsFile = $argv[1] ?? null;
if (!is_string($resultsFile) || !is_file($resultsFile)) {
    fwrite(STDERR, "Usage: summarize.php <results.tsv>\n");
    exit(2);
}

$lines = file($resultsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if ($lines === false || array_shift($lines) === null) {
    throw new RuntimeException(sprintf('Unable to read BookStack benchmark results from %s.', $resultsFile));
}

/** @var array<string, list<array{elapsed: float, rss: int}>> $measurements */
$measurements = [];

foreach ($lines as $line) {
    $columns = explode("\t", $line);
    if (count($columns) !== 8) {
        throw new RuntimeException(sprintf('Malformed BookStack benchmark result: %s', $line));
    }

    [$phase, , , $scenario, $elapsed, $rss] = $columns;
    if ($phase !== 'measure') {
        continue;
    }

    $measurements[$scenario][] = [
        'elapsed' => (float) $elapsed,
        'rss' => (int) $rss,
    ];
}

/**
 * @var array<string, array{
 *     runs: int,
 *     elapsed: float,
 *     elapsedMin: float,
 *     elapsedMax: float,
 *     rss: float,
 *     rssMin: float,
 *     rssMax: float
 * }> $summary
 */
$summary = [];

foreach (['baseline', 'inert', 'autodetect'] as $scenario) {
    $samples = $measurements[$scenario] ?? [];
    if ($samples === []) {
        throw new RuntimeException(sprintf('No measured BookStack samples exist for %s.', $scenario));
    }

    $elapsedSamples = array_column($samples, 'elapsed');
    $rssSamples = array_column($samples, 'rss');
    $summary[$scenario] = [
        'runs' => count($samples),
        'elapsed' => bookstackBenchmarkMedian($elapsedSamples),
        'elapsedMin' => min($elapsedSamples),
        'elapsedMax' => max($elapsedSamples),
        'rss' => bookstackBenchmarkMedian($rssSamples) / 1024,
        'rssMin' => min($rssSamples) / 1024,
        'rssMax' => max($rssSamples) / 1024,
    ];
}

$baseline = $summary['baseline'];

echo "BookStack application benchmark\n\n";
echo "| Scenario | Runs | Median wall | Wall range | vs baseline | Median peak RSS | RSS range | vs baseline |\n";
echo "| --- | ---: | ---: | ---: | ---: | ---: | ---: | ---: |\n";

foreach (['baseline', 'inert', 'autodetect'] as $scenario) {
    $result = $summary[$scenario];
    $elapsedDelta = $result['elapsed'] - $baseline['elapsed'];
    $rssDelta = $result['rss'] - $baseline['rss'];

    printf(
        "| %s | %d | %.3f s | %.3f–%.3f s | %+.1f%% | %.1f MiB | %.1f–%.1f MiB | %+.1f%% |\n",
        $scenario,
        $result['runs'],
        $result['elapsed'],
        $result['elapsedMin'],
        $result['elapsedMax'],
        $baseline['elapsed'] === 0.0 ? 0.0 : $elapsedDelta / $baseline['elapsed'] * 100,
        $result['rss'],
        $result['rssMin'],
        $result['rssMax'],
        $baseline['rss'] === 0.0 ? 0.0 : $rssDelta / $baseline['rss'] * 100,
    );
}

$inert = $summary['inert'];
$autodetect = $summary['autodetect'];
printf(
    "\nAutodetection increment over inert: %+.3f s (%+.1f%%), %+.1f MiB (%+.1f%%).\n",
    $autodetect['elapsed'] - $inert['elapsed'],
    $inert['elapsed'] === 0.0 ? 0.0 : ($autodetect['elapsed'] - $inert['elapsed']) / $inert['elapsed'] * 100,
    $autodetect['rss'] - $inert['rss'],
    $inert['rss'] === 0.0 ? 0.0 : ($autodetect['rss'] - $inert['rss']) / $inert['rss'] * 100,
);
