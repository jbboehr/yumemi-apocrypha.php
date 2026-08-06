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

use RuntimeException;
use ZipArchive;

/**
 * @phpstan-import-type RepositoryRecord from Schema
 */
final class ArchiveManager
{
    public function __construct(private readonly CachedHttpClient $http)
    {
    }

    /**
     * @param list<RepositoryRecord> $repositories
     * @return list<RepositoryRecord>
     */
    public function downloadAll(array $repositories, string $workspace, Config $config, bool $offline): array
    {
        $archiveDirectory = $workspace . '/archives';
        JsonStorage::ensureDirectory($archiveDirectory);
        $downloadedBytes = 0;

        foreach ($repositories as &$repository) {
            $destination = $archiveDirectory . '/' . hash('sha256', $repository['key'] . "\0" . $repository['distUrl']) . '.zip';
            $remaining = $config->archiveLimits['totalDownloadBytes'] - $downloadedBytes;
            if ($remaining <= 0) {
                $repository['archiveStatus'] = 'oversized';
                $repository['archiveError'] = 'Aggregate archive download limit reached.';
                continue;
            }

            try {
                $maximum = min($remaining, $config->archiveLimits['compressedBytes']);
                $bytes = $this->http->download($repository['distUrl'], $destination, $maximum, $offline);
                $this->inspect($destination, $config);
                $repository['archivePath'] = $destination;
                $sha256 = hash_file('sha256', $destination);
                $repository['archiveSha256'] = false === $sha256 ? null : $sha256;
                $repository['archiveBytes'] = $bytes;
                $repository['archiveStatus'] = 'downloaded';
                $repository['archiveError'] = null;
                $downloadedBytes += $bytes;
            } catch (HttpException $exception) {
                $repository['archiveStatus'] = str_contains($exception->getMessage(), 'exceeds') ? 'oversized' : 'failed';
                $repository['archiveError'] = $exception->getMessage();
            } catch (RuntimeException $exception) {
                $repository['archiveStatus'] = 'unsafe';
                $repository['archiveError'] = $exception->getMessage();
            }
        }
        unset($repository);

        return $repositories;
    }

    public function inspect(string $path, Config $config): void
    {
        $archive = new ZipArchive();
        $result = $archive->open($path, ZipArchive::RDONLY);
        if (true !== $result) {
            throw new RuntimeException(sprintf('Unable to open ZIP archive (%s): %s', $result, $path));
        }

        try {
            $this->validateArchiveMetrics($archive->numFiles, 0, $config);

            $uncompressedBytes = 0;
            for ($index = 0; $index < $archive->numFiles; ++$index) {
                $stat = $archive->statIndex($index);
                if (false === $stat) {
                    throw new RuntimeException(sprintf('Unable to inspect ZIP entry %d.', $index));
                }
                $size = $stat['size'];
                $uncompressedBytes += $size;
                $this->validateArchiveMetrics($archive->numFiles, $uncompressedBytes, $config);
            }
        } finally {
            $archive->close();
        }
    }

    public function validateArchiveMetrics(int $entries, int $uncompressedBytes, Config $config): void
    {
        if ($entries > $config->archiveLimits['entries']) {
            throw new RuntimeException(sprintf(
                'Archive contains %d entries, exceeding the %d-entry limit.',
                $entries,
                $config->archiveLimits['entries'],
            ));
        }
        if ($uncompressedBytes > $config->archiveLimits['uncompressedBytes']) {
            throw new RuntimeException(sprintf(
                'Archive exceeds the %d-byte uncompressed limit.',
                $config->archiveLimits['uncompressedBytes'],
            ));
        }
    }
}
