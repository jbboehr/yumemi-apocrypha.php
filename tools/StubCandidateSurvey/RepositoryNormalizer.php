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

final class RepositoryNormalizer
{
    /** @return array{key: string, url: string, owner: string} */
    public function normalize(string $url, string $fallbackVendor): array
    {
        $url = trim($url);
        if ('' === $url) {
            throw new RuntimeException('Repository URL must not be empty.');
        }

        $url = preg_replace('~^git@([^:]+):~', 'https://$1/', $url) ?? $url;
        $url = preg_replace('~^(?:git|ssh)://(?:git@)?~', 'https://', $url) ?? $url;
        $url = preg_replace('~\.git/?$~i', '', $url) ?? $url;
        $url = rtrim($url, '/');
        $parts = parse_url($url);

        if (!is_array($parts) || !isset($parts['host'], $parts['path'])) {
            $key = strtolower($url);

            return ['key' => $key, 'url' => $url, 'owner' => strtolower($fallbackVendor)];
        }

        $host = strtolower($parts['host']);
        $path = trim($parts['path'], '/');
        $segments = explode('/', $path);
        $owner = '' !== $segments[0] ? strtolower($segments[0]) : strtolower($fallbackVendor);
        $normalizedUrl = sprintf('https://%s/%s', $host, $path);
        $key = strtolower(sprintf('%s/%s', $host, $path));

        return ['key' => $key, 'url' => $normalizedUrl, 'owner' => $owner];
    }
}
