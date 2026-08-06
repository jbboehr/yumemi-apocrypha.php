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

use CurlHandle;
use CurlMultiHandle;

final class CachedHttpClient
{
    /** @var positive-int */
    private readonly int $concurrency;

    /** @var non-empty-string */
    private readonly string $userAgent;

    public function __construct(
        int $concurrency,
        private readonly int $retries,
        string $userAgent,
    ) {
        if ($concurrency < 1) {
            throw new HttpException('HTTP concurrency must be positive.');
        }
        if ('' === $userAgent) {
            throw new HttpException('HTTP User-Agent must not be empty.');
        }
        $this->concurrency = $concurrency;
        $this->userAgent = $userAgent;
    }

    /**
     * @param array<string, string> $urlsByKey
     * @return array<string, string>
     */
    public function fetchMany(
        array $urlsByKey,
        string $cacheDirectory,
        bool $offline,
        bool $ignoreNotFound = false,
    ): array {
        JsonStorage::ensureDirectory($cacheDirectory);
        $results = [];
        $pending = [];

        foreach ($urlsByKey as $key => $url) {
            $cached = $this->readCachedBody($cacheDirectory, $url);
            if ($offline) {
                if ($ignoreNotFound && $this->isMissing($cacheDirectory, $url)) {
                    continue;
                }
                if (null === $cached) {
                    throw new HttpException(sprintf('Offline cache miss: %s', $url));
                }
                $results[$key] = $cached;
                continue;
            }
            $pending[$key] = $url;
        }

        foreach (array_chunk($pending, max(1, $this->concurrency), true) as $chunk) {
            $results += $this->fetchChunk($chunk, $cacheDirectory, $ignoreNotFound);
        }

        return $results;
    }

    public function download(string $url, string $destination, int $maximumBytes, bool $offline): int
    {
        if (is_file($destination)) {
            $size = filesize($destination);
            if (false !== $size && $size <= $maximumBytes) {
                return $size;
            }
        }
        if ($offline) {
            throw new HttpException(sprintf('Offline archive cache miss: %s', $url));
        }

        JsonStorage::ensureDirectory(dirname($destination));
        $part = $destination . '.part';
        $lastError = 'unknown download error';

        for ($attempt = 0; $attempt <= $this->retries; ++$attempt) {
            $handle = fopen($part, 'wb');
            if (false === $handle) {
                throw new HttpException(sprintf('Unable to open archive destination: %s', $part));
            }

            $written = 0;
            $overflow = false;
            $curl = curl_init($url);
            if (false === $curl) {
                fclose($handle);
                throw new HttpException(sprintf('Unable to initialize archive request: %s', $url));
            }

            curl_setopt_array($curl, [
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 5,
                CURLOPT_CONNECTTIMEOUT => 20,
                CURLOPT_TIMEOUT => 180,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2TLS,
                CURLOPT_USERAGENT => $this->userAgent,
                CURLOPT_WRITEFUNCTION => static function (CurlHandle $unused, string $data) use ($handle, $maximumBytes, &$written, &$overflow): int {
                    $length = strlen($data);
                    if ($written + $length > $maximumBytes) {
                        $overflow = true;

                        return 0;
                    }
                    $result = fwrite($handle, $data);
                    if (false === $result) {
                        return 0;
                    }
                    $written += $result;

                    return $result;
                },
            ]);

            $ok = curl_exec($curl);
            $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
            $lastError = curl_error($curl);
            curl_close($curl);
            fclose($handle);

            if ($overflow) {
                @unlink($part);
                throw new HttpException(sprintf('Archive exceeds %d compressed bytes: %s', $maximumBytes, $url));
            }
            if (true === $ok && $status >= 200 && $status < 300) {
                if (!rename($part, $destination)) {
                    @unlink($part);
                    throw new HttpException(sprintf('Unable to finalize archive: %s', $destination));
                }

                return $written;
            }

            @unlink($part);
            if ($attempt < $this->retries) {
                usleep(100_000 * (2 ** $attempt));
            }
        }

        throw new HttpException(sprintf('Archive request failed (%s): %s', $lastError, $url));
    }

    /**
     * @param array<string, string> $urlsByKey
     * @return array<string, string>
     */
    private function fetchChunk(array $urlsByKey, string $cacheDirectory, bool $ignoreNotFound): array
    {
        $remaining = $urlsByKey;
        $results = [];
        $errors = [];
        $missing = [];

        for ($attempt = 0; $attempt <= $this->retries && [] !== $remaining; ++$attempt) {
            $multi = curl_multi_init();
            /** @var array<string, CurlHandle> $handles */
            $handles = [];
            /** @var array<string, array<string, string>> $responseHeaders */
            $responseHeaders = [];

            foreach ($remaining as $key => $url) {
                $metadata = $this->readCacheMetadata($cacheDirectory, $url);
                $headers = $this->requestHeaders($metadata);

                $handle = curl_init($url);
                if (false === $handle) {
                    throw new HttpException(sprintf('Unable to initialize request: %s', $url));
                }
                $responseHeaders[$key] = [];
                curl_setopt_array($handle, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_MAXREDIRS => 5,
                    CURLOPT_CONNECTTIMEOUT => 20,
                    CURLOPT_TIMEOUT => 120,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2TLS,
                    CURLOPT_USERAGENT => $this->userAgent,
                    CURLOPT_HTTPHEADER => $headers,
                    CURLOPT_HEADERFUNCTION => static function (CurlHandle $unused, string $line) use (&$responseHeaders, $key): int {
                        $parts = explode(':', $line, 2);
                        if (2 === count($parts)) {
                            $responseHeaders[$key][strtolower(trim($parts[0]))] = trim($parts[1]);
                        }

                        return strlen($line);
                    },
                ]);
                curl_multi_add_handle($multi, $handle);
                $handles[$key] = $handle;
            }

            $this->executeMulti($multi);
            $retry = [];
            $retryAfter = 0;

            foreach ($handles as $key => $handle) {
                $url = $remaining[$key];
                $status = curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
                $error = curl_error($handle);
                $body = curl_multi_getcontent($handle);
                curl_multi_remove_handle($multi, $handle);
                curl_close($handle);

                if (304 === $status) {
                    $cached = $this->readCachedBody($cacheDirectory, $url);
                    if (null !== $cached) {
                        $results[$key] = $cached;
                        continue;
                    }
                }
                if ($status >= 200 && $status < 300 && is_string($body)) {
                    $this->writeCache($cacheDirectory, $url, $body, $responseHeaders[$key]);
                    $results[$key] = $body;
                    continue;
                }
                if ($ignoreNotFound && 404 === $status) {
                    $this->markMissing($cacheDirectory, $url);
                    $missing[$key] = true;
                    continue;
                }

                $errors[$key] = sprintf('HTTP %d%s', $status, '' !== $error ? ': ' . $error : '');
                if ($attempt < $this->retries && (0 === $status || 429 === $status || $status >= 500)) {
                    $retry[$key] = $url;
                    $retryAfter = max($retryAfter, $this->retryDelay($responseHeaders[$key]['retry-after'] ?? null, $attempt));
                }
            }
            curl_multi_close($multi);
            $remaining = $retry;
            if ([] !== $remaining) {
                usleep($retryAfter * 1_000_000);
            }
        }

        if ([] !== $remaining) {
            $key = array_key_first($remaining);
            throw new HttpException(sprintf('Request failed after retries (%s): %s', $errors[$key] ?? 'unknown error', $remaining[$key]));
        }

        foreach ($urlsByKey as $key => $url) {
            if (!isset($results[$key])) {
                if (isset($missing[$key])) {
                    continue;
                }
                throw new HttpException(sprintf('Request failed (%s): %s', $errors[$key] ?? 'unknown error', $url));
            }
        }

        return $results;
    }

    private function executeMulti(CurlMultiHandle $multi): void
    {
        do {
            $status = curl_multi_exec($multi, $running);
            if (CURLM_OK !== $status) {
                throw new HttpException(sprintf('Concurrent HTTP request failed: %s', curl_multi_strerror($status)));
            }
            if ($running > 0 && -1 === curl_multi_select($multi, 1.0)) {
                usleep(10_000);
            }
        } while ($running > 0);
    }

    private function retryDelay(?string $header, int $attempt): int
    {
        if (null !== $header && ctype_digit($header)) {
            return min(5, max(1, (int) $header));
        }

        return min(5, 2 ** $attempt);
    }

    private function readCachedBody(string $cacheDirectory, string $url): ?string
    {
        $path = $this->cachePath($cacheDirectory, $url, '.body');
        if (!is_file($path)) {
            return null;
        }
        $contents = file_get_contents($path);

        return false === $contents ? null : $contents;
    }

    /** @return array<string, mixed> */
    private function readCacheMetadata(string $cacheDirectory, string $url): array
    {
        $path = $this->cachePath($cacheDirectory, $url, '.json');
        if (!is_file($path)) {
            return [];
        }

        $metadata = [];
        foreach (JsonStorage::read($path) as $key => $value) {
            if (is_string($key)) {
                $metadata[$key] = $value;
            }
        }

        return $metadata;
    }

    /**
     * @param array<string, mixed> $metadata
     * @return list<non-empty-string>
     */
    private function requestHeaders(array $metadata): array
    {
        $headers = [];
        if (isset($metadata['etag']) && is_string($metadata['etag']) && '' !== $metadata['etag']) {
            $headers[] = 'If-None-Match: ' . $metadata['etag'];
        }
        if (isset($metadata['lastModified']) && is_string($metadata['lastModified']) && '' !== $metadata['lastModified']) {
            $headers[] = 'If-Modified-Since: ' . $metadata['lastModified'];
        }

        return $headers;
    }

    /** @param array<string, string> $headers */
    private function writeCache(string $cacheDirectory, string $url, string $body, array $headers): void
    {
        $bodyPath = $this->cachePath($cacheDirectory, $url, '.body');
        if (false === file_put_contents($bodyPath, $body)) {
            throw new HttpException(sprintf('Unable to cache response: %s', $url));
        }
        JsonStorage::write($this->cachePath($cacheDirectory, $url, '.json'), [
            'url' => $url,
            'etag' => $headers['etag'] ?? null,
            'lastModified' => $headers['last-modified'] ?? null,
            'retrievedAt' => gmdate(DATE_ATOM),
        ]);
        $missingPath = $this->cachePath($cacheDirectory, $url, '.missing');
        if (is_file($missingPath)) {
            unlink($missingPath);
        }
    }

    private function markMissing(string $cacheDirectory, string $url): void
    {
        if (false === file_put_contents($this->cachePath($cacheDirectory, $url, '.missing'), '')) {
            throw new HttpException(sprintf('Unable to cache missing response: %s', $url));
        }
    }

    private function isMissing(string $cacheDirectory, string $url): bool
    {
        return is_file($this->cachePath($cacheDirectory, $url, '.missing'));
    }

    private function cachePath(string $cacheDirectory, string $url, string $suffix): string
    {
        return $cacheDirectory . '/' . hash('sha256', $url) . $suffix;
    }
}
