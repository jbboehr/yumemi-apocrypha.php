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

namespace jbboehr\Yumemi\Apocrypha\Tools;

final readonly class PackageArchiveChecker
{
    /** @var non-empty-list<non-empty-string> */
    private const REQUIRED_FILES = [
        'CHANGELOG.md',
        'CODE_OF_CONDUCT.md',
        'CONTRIBUTING.md',
        'LICENSE.md',
        'README.md',
        'apocrypha.neon',
        'composer.json',
        'docs/CLA-v1.md',
        'docs/LICENSE_EXCEPTION.md',
        'docs/STEWARD.md',
        'docs/THIRD_PARTY_NOTICES.md',
        'docs/pages/README.md',
        'docs/pages/images/yumemi-banner.png',
        'extension.neon',
        'src/Exception/ExceptionInterface.php',
        'src/Exception/InvalidConfigurationException.php',
        'src/Exception/LogicException.php',
        'src/PHPStan/ConfiguredIntegrationStubFilesExtension.php',
        'src/PHPStan/PackageIntegrationUnitBoundaryExtension.php',
        'src/PHPStan/PackageIntegrationUnitBoundaryMetadata.php',
        'src/PHPStan/PackageIntegrationUnitReturnTypeExtension.php',
        'stubs/carbon/carbon-2.stub',
        'stubs/carbon/carbon-3-real.stub',
        'stubs/carbon/carbon-3-utc.stub',
        'stubs/getid3/getid3-1.stub',
        'stubs/getid3/getid3-2.stub',
        'stubs/guzzle/guzzle-7-pre-7.11.stub',
        'stubs/guzzle/guzzle-7.stub',
        'stubs/guzzle/guzzle-8.stub',
        'stubs/guzzle/guzzle.stub',
        'stubs/illuminate/cache.stub',
        'stubs/illuminate/cookie.stub',
        'stubs/illuminate/filesystem.stub',
        'stubs/illuminate/http-11.stub',
        'stubs/illuminate/http.stub',
        'stubs/illuminate/process-13.stub',
        'stubs/illuminate/process.stub',
        'stubs/illuminate/queue-worker-11.stub',
        'stubs/illuminate/queue-worker-12.stub',
        'stubs/illuminate/queue.stub',
        'stubs/illuminate/redis.stub',
        'stubs/illuminate/support.stub',
        'stubs/intervention-image/intervention-image-3.stub',
        'stubs/intervention-image/intervention-image-4.stub',
        'stubs/measurements/measurements.stub',
        'stubs/phpgeo/phpgeo.stub',
        'stubs/symfony/http-foundation-ip.stub',
        'stubs/symfony/http-foundation-sse.stub',
        'stubs/symfony/http-foundation.stub',
        'stubs/symfony/stopwatch.stub',
    ];

    /** @var non-empty-list<non-empty-string> */
    private const ALLOWED_FILES = [
        'CHANGELOG.md',
        'CODE_OF_CONDUCT.md',
        'CONTRIBUTING.md',
        'LICENSE.md',
        'README.md',
        'apocrypha.neon',
        'composer.json',
        'docs/CLA-v1.md',
        'docs/LICENSE_EXCEPTION.md',
        'docs/STEWARD.md',
        'docs/THIRD_PARTY_NOTICES.md',
        'extension.neon',
    ];

    /** @var non-empty-list<non-empty-string> */
    private const ALLOWED_PREFIXES = [
        'docs/pages/',
        'src/',
        'stubs/',
    ];

    /** @var non-empty-list<non-empty-string> */
    private const BLACKLISTED_PREFIXES = [
        'docs/pages/images/logia/',
    ];

    /**
     * @param non-empty-list<non-empty-string> $requiredFiles
     * @param list<non-empty-string> $allowedFiles
     * @param list<non-empty-string> $allowedPrefixes
     * @param list<non-empty-string> $blacklistedPrefixes
     */
    public function __construct(
        private array $requiredFiles,
        private array $allowedFiles,
        private array $allowedPrefixes,
        private array $blacklistedPrefixes = [],
    ) {
    }

    public static function forApocrypha(): self
    {
        return new self(
            self::REQUIRED_FILES,
            self::ALLOWED_FILES,
            self::ALLOWED_PREFIXES,
            self::BLACKLISTED_PREFIXES,
        );
    }

    /**
     * @param non-empty-string $archive
     *
     * @return positive-int
     */
    public function check(string $archive): int
    {
        $resolvedArchive = realpath($archive);
        if ($resolvedArchive === false || !is_file($resolvedArchive)) {
            throw new \RuntimeException(sprintf('Package archive %s does not exist or is not a file.', $archive));
        }

        $package = new \PharData($resolvedArchive);
        $archivePrefix = 'phar://' . str_replace('\\', '/', $resolvedArchive) . '/';
        $files = [];

        foreach (new \RecursiveIteratorIterator($package) as $entry) {
            if (!$entry instanceof \PharFileInfo) {
                throw new \RuntimeException('Package archive contained an uninspectable entry.');
            }

            $normalizedPath = str_replace('\\', '/', $entry->getPathname());
            if (!str_starts_with($normalizedPath, $archivePrefix)) {
                throw new \RuntimeException(sprintf('Package archive returned an unexpected path %s.', $normalizedPath));
            }

            $relativePath = substr($normalizedPath, strlen($archivePrefix));
            if (
                $relativePath === ''
                || str_starts_with($relativePath, '/')
                || in_array('..', explode('/', $relativePath), true)
            ) {
                throw new \RuntimeException(sprintf('Package archive contained an unsafe path %s.', $relativePath));
            }

            if ($entry->isLink()) {
                throw new \RuntimeException(sprintf('Package archive contained symbolic link %s.', $relativePath));
            }

            if (!$entry->isFile()) {
                continue;
            }

            if (!$this->isAllowed($relativePath)) {
                throw new \RuntimeException(sprintf('Package archive contained unexpected file %s.', $relativePath));
            }

            $files[$relativePath] = true;
        }

        foreach ($this->requiredFiles as $requiredFile) {
            if (!array_key_exists($requiredFile, $files)) {
                throw new \RuntimeException(sprintf('Package archive omitted required file %s.', $requiredFile));
            }
        }

        return count($files);
    }

    /** @param non-empty-string $path */
    private function isAllowed(string $path): bool
    {
        foreach ($this->blacklistedPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return false;
            }
        }

        if (in_array($path, $this->allowedFiles, true)) {
            return true;
        }

        foreach ($this->allowedPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return false;
    }
}
