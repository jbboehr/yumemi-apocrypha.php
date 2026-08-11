#!/usr/bin/env php
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

use jbboehr\Yumemi\Apocrypha\Tools\PackageArchiveChecker;
use Symfony\Component\Process\Process;

require dirname(__DIR__) . '/vendor/autoload.php';

$projectRoot = dirname(__DIR__);
$arguments = $_SERVER['argv'] ?? null;
if (!is_array($arguments) || !array_is_list($arguments)) {
    fwrite(STDERR, "Package check failed: command-line arguments are unavailable.\n");
    exit(1);
}

foreach ($arguments as $argument) {
    if (!is_string($argument)) {
        fwrite(STDERR, "Package check failed: a command-line argument is not a string.\n");
        exit(1);
    }
}

array_shift($arguments);
if (count($arguments) > 1) {
    fwrite(STDERR, "Usage: php tools/check-package.php [archive.tar]\n");
    exit(2);
}

$temporaryRoot = rtrim(sys_get_temp_dir(), '/\\') . '/yumemi-apocrypha-package-check-' . bin2hex(random_bytes(16));
$archive = null;
$exitCode = 0;

$removeTemporaryRoot = static function () use ($temporaryRoot): void {
    if (!is_dir($temporaryRoot)) {
        return;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($temporaryRoot, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST,
    );

    foreach ($iterator as $entry) {
        if (!$entry instanceof SplFileInfo) {
            continue;
        }

        if ($entry->isFile() || $entry->isLink()) {
            if (!unlink($entry->getPathname())) {
                throw new RuntimeException(sprintf('Unable to remove temporary package file %s.', $entry->getPathname()));
            }
        } elseif (!rmdir($entry->getPathname())) {
            throw new RuntimeException(sprintf('Unable to remove temporary package directory %s.', $entry->getPathname()));
        }
    }

    unset($entry, $iterator);

    if (!rmdir($temporaryRoot)) {
        throw new RuntimeException(sprintf('Unable to remove temporary package root %s.', $temporaryRoot));
    }
};

try {
    if ($arguments === []) {
        if (!mkdir($temporaryRoot, 0o700)) {
            throw new RuntimeException(sprintf('Unable to create temporary package root %s.', $temporaryRoot));
        }

        $archive = $temporaryRoot . '/yumemi-apocrypha-package-check.tar';
        $composerBinary = getenv('COMPOSER_BINARY');
        $process = new Process([
            is_string($composerBinary) && $composerBinary !== '' ? $composerBinary : 'composer',
            'archive',
            '--format=tar',
            '--dir=' . $temporaryRoot,
            '--file=yumemi-apocrypha-package-check',
            '--no-interaction',
        ], $projectRoot);
        $process->setTimeout(60.0);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException(sprintf(
                "Composer archive creation failed.\n%s%s",
                $process->getOutput(),
                $process->getErrorOutput(),
            ));
        }
    } else {
        $archiveArgument = $arguments[0];
        $archive = preg_match('~\A(?:[A-Za-z]:[\\\\/]|[\\\\/]{2}|/)~', $archiveArgument) === 1
            ? $archiveArgument
            : $projectRoot . '/' . $archiveArgument;
    }

    $fileCount = PackageArchiveChecker::forApocrypha()->check($archive);
    fwrite(STDOUT, sprintf("Package archive is valid (%d files).\n", $fileCount));
} catch (Throwable $exception) {
    fwrite(STDERR, sprintf("Package check failed: %s\n", $exception->getMessage()));
    $exitCode = 1;
} finally {
    try {
        $removeTemporaryRoot();
    } catch (Throwable $exception) {
        fwrite(STDERR, sprintf("Package check cleanup failed: %s\n", $exception->getMessage()));
        $exitCode = 1;
    }
}

exit($exitCode);
