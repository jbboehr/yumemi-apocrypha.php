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

namespace jbboehr\Yumemi\Apocrypha\Tools\StubCandidateSurvey;

use JsonException;
use RuntimeException;

final class JsonStorage
{
    private const ENCODE_FLAGS = JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR;

    /** @param array<mixed> $data */
    public static function write(string $path, array $data): void
    {
        self::ensureDirectory(dirname($path));

        try {
            $contents = json_encode($data, JSON_PRETTY_PRINT | self::ENCODE_FLAGS) . "\n";
        } catch (JsonException $exception) {
            throw new RuntimeException($exception->getMessage(), 0, $exception);
        }

        if (false === file_put_contents($path, $contents)) {
            throw new RuntimeException(sprintf('Unable to write JSON file: %s', $path));
        }
    }

    /** @return array<mixed> */
    public static function read(string $path): array
    {
        $contents = file_get_contents($path);
        if (false === $contents) {
            throw new RuntimeException(sprintf('Unable to read JSON file: %s', $path));
        }

        try {
            $data = json_decode($contents, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException($exception->getMessage(), 0, $exception);
        }

        if (!is_array($data)) {
            throw new RuntimeException(sprintf('JSON file did not contain an object or array: %s', $path));
        }

        return $data;
    }

    /** @param iterable<array<mixed>> $records */
    public static function writeLines(string $path, iterable $records): void
    {
        self::ensureDirectory(dirname($path));
        $handle = fopen($path, 'wb');
        if (false === $handle) {
            throw new RuntimeException(sprintf('Unable to open JSONL file: %s', $path));
        }

        try {
            foreach ($records as $record) {
                fwrite($handle, json_encode($record, self::ENCODE_FLAGS) . "\n");
            }
        } catch (JsonException $exception) {
            throw new RuntimeException($exception->getMessage(), 0, $exception);
        } finally {
            fclose($handle);
        }
    }

    /** @return list<array<mixed>> */
    public static function readLines(string $path): array
    {
        $handle = fopen($path, 'rb');
        if (false === $handle) {
            throw new RuntimeException(sprintf('Unable to open JSONL file: %s', $path));
        }

        $records = [];
        try {
            while (false !== ($line = fgets($handle))) {
                if ('' === trim($line)) {
                    continue;
                }
                $record = json_decode($line, true, flags: JSON_THROW_ON_ERROR);
                if (!is_array($record)) {
                    throw new RuntimeException(sprintf('Invalid JSONL record in %s.', $path));
                }
                $records[] = $record;
            }
        } catch (JsonException $exception) {
            throw new RuntimeException($exception->getMessage(), 0, $exception);
        } finally {
            fclose($handle);
        }

        return $records;
    }

    public static function ensureDirectory(string $path): void
    {
        if (!is_dir($path) && !mkdir($path, 0o777, true) && !is_dir($path)) {
            throw new RuntimeException(sprintf('Unable to create directory: %s', $path));
        }
    }
}
