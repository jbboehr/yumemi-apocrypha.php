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

if ($argc < 4) {
    fwrite(STDERR, "Usage: php assert-lock-package-metadata.php <package> <composer.json> <composer.lock> [...]\n");
    exit(2);
}

$packageName = $argv[1];
$composerFile = $argv[2];
$lockFiles = array_slice($argv, 3);

$readJson = static function (string $file): array {
    try {
        $contents = file_get_contents($file);
        if ($contents === false) {
            throw new RuntimeException('could not read file');
        }

        $decoded = json_decode($contents, true, flags: JSON_THROW_ON_ERROR);
        if (!is_array($decoded)) {
            throw new RuntimeException('top-level JSON value is not an object');
        }

        return $decoded;
    } catch (Throwable $throwable) {
        fwrite(STDERR, sprintf("Invalid Composer JSON in %s: %s.\n", $file, $throwable->getMessage()));
        exit(1);
    }
};

$normalize = static function (mixed $value) use (&$normalize): mixed {
    if (!is_array($value)) {
        return $value;
    }

    if (!array_is_list($value)) {
        ksort($value);
    }

    foreach ($value as $key => $item) {
        $value[$key] = $normalize($item);
    }

    return $value;
};

$metadataKeys = [
    'type',
    'require',
    'require-dev',
    'conflict',
    'provide',
    'replace',
    'autoload',
    'autoload-dev',
    'include-path',
    'target-dir',
    'extra',
    'bin',
];

$selectMetadata = static function (array $package) use ($metadataKeys, $normalize): array {
    $metadata = [];
    foreach ($metadataKeys as $key) {
        $metadata[$key] = $package[$key] ?? null;
    }

    return $normalize($metadata);
};

$expectedMetadata = $selectMetadata($readJson($composerFile));
$failed = false;

foreach ($lockFiles as $lockFile) {
    $lock = $readJson($lockFile);
    if (!is_string($lock['content-hash'] ?? null) || $lock['content-hash'] === '') {
        fwrite(STDERR, sprintf("Consumer lock %s has no valid content hash.\n", $lockFile));
        $failed = true;
        continue;
    }

    $runtimePackages = $lock['packages'] ?? null;
    $developmentPackages = $lock['packages-dev'] ?? null;
    if (!is_array($runtimePackages) || !is_array($developmentPackages)) {
        fwrite(STDERR, sprintf("Consumer lock %s has malformed package lists.\n", $lockFile));
        $failed = true;
        continue;
    }

    $packages = array_merge($runtimePackages, $developmentPackages);
    $matches = array_values(array_filter(
        $packages,
        static fn (mixed $package): bool => is_array($package) && ($package['name'] ?? null) === $packageName,
    ));

    if (count($matches) !== 1) {
        fwrite(STDERR, sprintf(
            "Consumer lock %s contains %d entries for %s; expected exactly one.\n",
            $lockFile,
            count($matches),
            $packageName,
        ));
        $failed = true;
        continue;
    }

    if ($selectMetadata($matches[0]) !== $expectedMetadata) {
        fwrite(STDERR, sprintf(
            "Consumer lock %s contains stale metadata for %s; run nix run .#refresh-consumer-locks.\n",
            $lockFile,
            $packageName,
        ));
        $failed = true;
    }
}

exit($failed ? 1 : 0);
