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

final class StressAssertions
{
    public const SIZES = [50, 200, 800];
    public const ROUNDS = 3;
    public const MAX_SECONDS = 180.0;
    public const MAX_RSS_KIB = 768 * 1024;

    /**
     * @param array<int, string> $expected
     * @return list<string>
     */
    public static function diagnosticErrors(
        string $output,
        array $expected,
        string $file,
        int $exitCode,
        string $stderr = '',
    ): array {
        if ($stderr !== '') {
            return ['PHPStan wrote unexpected stderr output.'];
        }
        if ($exitCode !== 1 || $expected === []) {
            return [sprintf('Expected PHPStan exit 1 and a nonempty contract; got exit %d for %d expected diagnostics.', $exitCode, count($expected))];
        }

        // Debug mode prints analyzed paths before the JSON formatter's final line.
        $lines = explode("\n", trim($output));
        try {
            $report = json_decode($lines[array_key_last($lines)], true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            return ['Invalid PHPStan JSON: ' . $exception->getMessage()];
        }
        if (
            !is_array($report)
            || !is_array($report['totals'] ?? null)
            || ($report['totals']['errors'] ?? null) !== 0
            || ($report['errors'] ?? null) !== []
            || ($report['totals']['file_errors'] ?? null) !== count($expected)
            || !is_array($report['files'] ?? null)
            || array_keys($report['files']) !== [$file]
        ) {
            return ['PHPStan reported unexpected totals, general errors, or files.'];
        }
        $fileReport = $report['files'][$file];
        $messages = is_array($fileReport) ? ($fileReport['messages'] ?? null) : null;
        if (!is_array($messages) || count($messages) !== count($expected)) {
            return ['PHPStan diagnostic count differs from the generated contract.'];
        }

        $errors = [];
        $remaining = $expected;
        foreach ($messages as $message) {
            if (!is_array($message) || !is_int($message['line'] ?? null)) {
                $errors[] = 'PHPStan returned a diagnostic without a source line.';
                continue;
            }
            $line = $message['line'];
            if (
                !isset($remaining[$line])
                || ($message['identifier'] ?? null) !== 'apocrypha.unit'
                || !is_string($message['message'] ?? null)
                || !str_contains($message['message'], $remaining[$line])
            ) {
                $errors[] = sprintf('Unexpected or duplicate diagnostic at %s:%d.', $file, $line);
                continue;
            }
            unset($remaining[$line]);
        }
        if ($remaining !== []) {
            $errors[] = 'Missing diagnostics at lines: ' . implode(', ', array_keys($remaining));
        }

        return $errors;
    }

    /**
     * @param array<int, list<array{seconds: float, rssKiB: int}>> $samples
     * @return list<string>
     */
    public static function scalingErrors(array $samples): array
    {
        if (array_keys($samples) !== self::SIZES) {
            return ['Measurements must cover all three configured workload sizes.'];
        }
        $errors = [];
        $medians = [];
        foreach ($samples as $size => $runs) {
            if (count($runs) !== self::ROUNDS) {
                return ['Every workload size requires three measured runs.'];
            }
            foreach ($runs as $run) {
                if (!is_finite($run['seconds']) || $run['seconds'] <= 0.0 || $run['rssKiB'] <= 0) {
                    return ['Elapsed time and peak RSS must be finite positive measurements.'];
                }
                if ($run['seconds'] > self::MAX_SECONDS || $run['rssKiB'] > self::MAX_RSS_KIB) {
                    $errors[] = sprintf('Workload %d exceeded the 180-second or 768-MiB per-run budget.', $size);
                }
            }
            $seconds = array_column($runs, 'seconds');
            $rss = array_column($runs, 'rssKiB');
            sort($seconds, SORT_NUMERIC);
            sort($rss, SORT_NUMERIC);
            $medians[$size] = ['seconds' => $seconds[1], 'rssKiB' => $rss[1]];
        }
        foreach (array_slice(self::SIZES, 1) as $index => $large) {
            $small = self::SIZES[$index];
            $growth = $large / $small;
            if ($medians[$large]['seconds'] > $medians[$small]['seconds'] * $growth * 2.5 + 1.0) {
                $errors[] = sprintf('Wall time grew too quickly between %d and %d groups.', $small, $large);
            }
            if ($medians[$large]['rssKiB'] > $medians[$small]['rssKiB'] * $growth * 2 + 16 * 1024) {
                $errors[] = sprintf('Peak RSS grew too quickly between %d and %d groups.', $small, $large);
            }
        }

        return $errors;
    }
}
