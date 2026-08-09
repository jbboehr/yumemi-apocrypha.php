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

require __DIR__ . '/vendor/autoload.php';

$version = Composer\InstalledVersions::getPrettyVersion('guzzlehttp/guzzle')
    ?? Composer\InstalledVersions::getVersion('guzzlehttp/guzzle');
if ($version === null || preg_match('/^v?([78])\./', $version, $matches) !== 1) {
    throw new RuntimeException(sprintf('Unexpected installed Guzzle version %s.', $version ?? 'unknown'));
}

$stubFiles = (new jbboehr\Yumemi\Apocrypha\PHPStan\ConfiguredIntegrationStubFilesExtension(
    ['guzzlehttp/guzzle'],
    false,
    true,
))->getFiles();
$expectedVersionStub = $matches[1] === '7' && version_compare(ltrim($version, 'v'), '7.11.0', '<')
    ? 'guzzle-7-pre-7.11.stub'
    : sprintf('guzzle-%s.stub', $matches[1]);
$selectedExpectedStub = false;
foreach ($stubFiles as $stubFile) {
    if (basename($stubFile) === $expectedVersionStub) {
        $selectedExpectedStub = true;
        break;
    }
}
if (!$selectedExpectedStub) {
    throw new RuntimeException(sprintf(
        'Guzzle %s selected [%s] instead of %s.',
        $version,
        implode(', ', array_map('basename', $stubFiles)),
        $expectedVersionStub,
    ));
}

/**
 * @param class-string $class
 * @param array<string, list<string>> $methods
 */
function verifyGuzzleMethods(string $class, array $methods): void
{
    $reflection = new ReflectionClass($class);

    foreach ($methods as $methodName => $parameterNames) {
        if (!$reflection->hasMethod($methodName)) {
            throw new RuntimeException(sprintf('%s::%s() does not exist.', $class, $methodName));
        }

        $method = $reflection->getMethod($methodName);
        if (!$method->isPublic()) {
            throw new RuntimeException(sprintf('%s::%s() is not public.', $class, $methodName));
        }

        $actualNames = array_map(
            static fn (ReflectionParameter $parameter): string => $parameter->getName(),
            $method->getParameters(),
        );
        if ($actualNames !== $parameterNames) {
            throw new RuntimeException(sprintf(
                '%s::%s() parameters are [%s]; expected [%s].',
                $class,
                $methodName,
                implode(', ', $actualNames),
                implode(', ', $parameterNames),
            ));
        }
    }
}

verifyGuzzleMethods(GuzzleHttp\Client::class, [
    '__construct' => ['config'],
    'send' => ['request', 'options'],
    'sendAsync' => ['request', 'options'],
    'request' => ['method', 'uri', 'options'],
    'requestAsync' => ['method', 'uri', 'options'],
    'get' => ['uri', 'options'],
    'head' => ['uri', 'options'],
    'put' => ['uri', 'options'],
    'post' => ['uri', 'options'],
    'patch' => ['uri', 'options'],
    'delete' => ['uri', 'options'],
    'getAsync' => ['uri', 'options'],
    'headAsync' => ['uri', 'options'],
    'putAsync' => ['uri', 'options'],
    'postAsync' => ['uri', 'options'],
    'patchAsync' => ['uri', 'options'],
    'deleteAsync' => ['uri', 'options'],
]);
verifyGuzzleMethods(GuzzleHttp\ClientInterface::class, [
    'send' => ['request', 'options'],
    'sendAsync' => ['request', 'options'],
    'request' => ['method', 'uri', 'options'],
    'requestAsync' => ['method', 'uri', 'options'],
]);
verifyGuzzleMethods(GuzzleHttp\Middleware::class, [
    'retry' => ['decider', 'delay'],
]);
verifyGuzzleMethods(GuzzleHttp\TransferStats::class, [
    '__construct' => ['request', 'response', 'transferTime', 'handlerErrorData', 'handlerStats'],
    'getTransferTime' => [],
]);

$constants = [
    'CONNECT_TIMEOUT' => 'connect_timeout',
    'DELAY' => 'delay',
    'EXPECT' => 'expect',
    'PROGRESS' => 'progress',
    'READ_TIMEOUT' => 'read_timeout',
    'TIMEOUT' => 'timeout',
];
$reflection = new ReflectionClass(GuzzleHttp\RequestOptions::class);
foreach ($constants as $name => $value) {
    if (!$reflection->hasConstant($name) || $reflection->getConstant($name) !== $value) {
        throw new RuntimeException(sprintf('GuzzleHttp\\RequestOptions::%s has an unexpected value.', $name));
    }
}
