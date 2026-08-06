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

namespace jbboehr\Yumemi\Apocrypha\Tests\ConsumerDiagnostics;

final class StructuredDiagnosticMatcher
{
    /**
     * @param list<non-empty-string> $expectedFragments
     *
     * @return list<string>
     */
    public static function errors(string $json, array $expectedFragments): array
    {
        try {
            $payload = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            return ['PHPStan did not produce valid JSON: ' . $exception->getMessage()];
        }

        if (!is_array($payload)) {
            return ['PHPStan JSON output must be an object.'];
        }

        $errors = [];
        $totals = $payload['totals'] ?? null;
        if (!is_array($totals)) {
            return ['PHPStan JSON output is missing the totals object.'];
        }

        $generalErrorCount = $totals['errors'] ?? null;
        if (!is_int($generalErrorCount)) {
            $errors[] = 'PHPStan JSON output is missing the general error count.';
        } elseif ($generalErrorCount !== 0) {
            $errors[] = sprintf('PHPStan reported %d general error(s).', $generalErrorCount);
        }

        $fileErrorCount = $totals['file_errors'] ?? null;
        if (!is_int($fileErrorCount)) {
            $errors[] = 'PHPStan JSON output is missing the file error count.';
        }

        $reportedGeneralErrors = $payload['errors'] ?? null;
        if (is_array($reportedGeneralErrors)) {
            foreach ($reportedGeneralErrors as $reportedGeneralError) {
                if (is_string($reportedGeneralError) && $reportedGeneralError !== '') {
                    $errors[] = 'General PHPStan error: ' . $reportedGeneralError;
                }
            }
        }

        if (is_int($generalErrorCount) && $generalErrorCount !== 0) {
            return $errors;
        }

        $files = $payload['files'] ?? null;
        if (!is_array($files)) {
            return [...$errors, 'PHPStan JSON output is missing the files object.'];
        }

        /** @var list<array{path: string, line: int|null, identifier: string|null, message: string}> $actual */
        $actual = [];

        foreach ($files as $path => $file) {
            if (!is_string($path) || !is_array($file)) {
                $errors[] = 'PHPStan JSON output contains an invalid file entry.';

                continue;
            }

            $messages = $file['messages'] ?? null;
            if (!is_array($messages)) {
                $errors[] = sprintf('PHPStan JSON output for %s is missing its messages.', $path);

                continue;
            }

            foreach ($messages as $message) {
                if (!is_array($message) || !is_string($message['message'] ?? null) || $message['message'] === '') {
                    $errors[] = sprintf('PHPStan JSON output for %s contains an invalid diagnostic.', $path);

                    continue;
                }

                $line = $message['line'] ?? null;
                $identifier = $message['identifier'] ?? null;
                $actual[] = [
                    'path' => $path,
                    'line' => is_int($line) ? $line : null,
                    'identifier' => is_string($identifier) && $identifier !== '' ? $identifier : null,
                    'message' => $message['message'],
                ];
            }
        }

        if (is_int($fileErrorCount) && $fileErrorCount !== count($actual)) {
            $errors[] = sprintf(
                'PHPStan reported %d file error(s), but its files object contains %d diagnostic(s).',
                $fileErrorCount,
                count($actual),
            );
        }

        if ($expectedFragments === []) {
            $errors[] = 'At least one expected diagnostic fragment is required.';
        }

        foreach ($expectedFragments as $expectedFragment) {
            $matched = false;

            foreach ($actual as $diagnostic) {
                if (str_contains($diagnostic['message'], $expectedFragment)) {
                    $matched = true;

                    break;
                }
            }

            if (!$matched) {
                $errors[] = 'Missing expected PHPStan diagnostic containing: ' . $expectedFragment;
            }
        }

        foreach ($actual as $diagnostic) {
            $matched = false;

            foreach ($expectedFragments as $expectedFragment) {
                if (str_contains($diagnostic['message'], $expectedFragment)) {
                    $matched = true;

                    break;
                }
            }

            if ($matched) {
                continue;
            }

            $location = $diagnostic['path'];
            if ($diagnostic['line'] !== null) {
                $location .= ':' . $diagnostic['line'];
            }
            if ($diagnostic['identifier'] !== null) {
                $location .= ' [' . $diagnostic['identifier'] . ']';
            }

            $errors[] = sprintf('Unexpected PHPStan diagnostic at %s: %s', $location, $diagnostic['message']);
        }

        return $errors;
    }
}
