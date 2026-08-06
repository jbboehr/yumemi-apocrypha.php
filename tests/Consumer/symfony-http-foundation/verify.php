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

use Composer\InstalledVersions;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;

require __DIR__ . '/vendor/autoload.php';

/** @param array<int, string> $parameterTypes */
function assertHttpFoundationMethod(
    string $class,
    string $methodName,
    array $parameterTypes,
    ?string $returnType,
    bool $static = false,
): void {
    $method = new ReflectionMethod($class, $methodName);
    if (!$method->isPublic() || $method->isStatic() !== $static) {
        throw new RuntimeException(sprintf('%s::%s() does not have the expected public/static shape.', $class, $methodName));
    }

    $parameters = $method->getParameters();
    foreach ($parameterTypes as $position => $expectedType) {
        $parameter = $parameters[$position] ?? null;
        $actualType = $parameter?->getType();
        if ($parameter === null || $actualType === null || (string) $actualType !== $expectedType) {
            throw new RuntimeException(sprintf(
                '%s::%s() parameter %d has type %s; expected %s.',
                $class,
                $methodName,
                $position + 1,
                $actualType === null ? 'none' : (string) $actualType,
                $expectedType,
            ));
        }
    }

    $actualReturnType = $method->getReturnType();
    if (($actualReturnType === null ? null : (string) $actualReturnType) !== $returnType) {
        throw new RuntimeException(sprintf(
            '%s::%s() has return type %s; expected %s.',
            $class,
            $methodName,
            $actualReturnType === null ? 'none' : (string) $actualReturnType,
            $returnType ?? 'none',
        ));
    }
}

$version = InstalledVersions::getPrettyVersion('symfony/http-foundation')
    ?? throw new RuntimeException('Unable to determine the installed Symfony HttpFoundation version.');
$normalizedVersion = ltrim($version, 'v');

foreach (['setMaxAge', 'setStaleIfError', 'setStaleWhileRevalidate', 'setSharedMaxAge'] as $method) {
    assertHttpFoundationMethod(Response::class, $method, [0 => 'int'], 'static');
}
foreach (['setTtl', 'setClientTtl'] as $method) {
    assertHttpFoundationMethod(Response::class, $method, [0 => 'int'], 'static');
}
assertHttpFoundationMethod(Response::class, 'setCache', [0 => 'array'], 'static');
assertHttpFoundationMethod(Response::class, 'getAge', [], 'int');
assertHttpFoundationMethod(Response::class, 'getMaxAge', [], '?int');
assertHttpFoundationMethod(Response::class, 'getTtl', [], '?int');
assertHttpFoundationMethod(Cookie::class, 'getMaxAge', [], 'int');
assertHttpFoundationMethod(UploadedFile::class, 'getMaxFilesize', [], 'int|float', true);
assertHttpFoundationMethod(SessionInterface::class, 'invalidate', [0 => '?int'], 'bool');
assertHttpFoundationMethod(SessionInterface::class, 'migrate', [1 => '?int'], 'bool');
assertHttpFoundationMethod(Session::class, 'invalidate', [0 => '?int'], 'bool');
assertHttpFoundationMethod(Session::class, 'migrate', [1 => '?int'], 'bool');
assertHttpFoundationMethod(SessionStorageInterface::class, 'regenerate', [1 => '?int'], 'bool');
assertHttpFoundationMethod(NativeSessionStorage::class, 'regenerate', [1 => '?int'], 'bool');
assertHttpFoundationMethod(MetadataBag::class, 'getLifetime', [], 'int');

$hasSse = version_compare($normalizedVersion, '7.3.0', '>=');
foreach (['Symfony\\Component\\HttpFoundation\\EventStreamResponse', 'Symfony\\Component\\HttpFoundation\\ServerEvent'] as $class) {
    if (class_exists($class) !== $hasSse) {
        throw new RuntimeException(sprintf('%s presence does not match the expected version profile.', $class));
    }
}

if ($hasSse) {
    assertHttpFoundationMethod('Symfony\\Component\\HttpFoundation\\EventStreamResponse', '__construct', [3 => '?int'], null);
    assertHttpFoundationMethod('Symfony\\Component\\HttpFoundation\\EventStreamResponse', 'getRetry', [], '?int');
    assertHttpFoundationMethod('Symfony\\Component\\HttpFoundation\\EventStreamResponse', 'setRetry', [0 => 'int'], 'void');
    assertHttpFoundationMethod('Symfony\\Component\\HttpFoundation\\ServerEvent', '__construct', [2 => '?int'], null);
    assertHttpFoundationMethod('Symfony\\Component\\HttpFoundation\\ServerEvent', 'getRetry', [], '?int');
    assertHttpFoundationMethod('Symfony\\Component\\HttpFoundation\\ServerEvent', 'setRetry', [0 => '?int'], 'static');
}

$anonymize = new ReflectionMethod(IpUtils::class, 'anonymize');
$expectedAnonymizeParameters = version_compare($normalizedVersion, '8.0.0', '>=') ? 3 : 1;
if ($anonymize->getNumberOfParameters() !== $expectedAnonymizeParameters) {
    throw new RuntimeException(sprintf(
        'IpUtils::anonymize() has %d parameters; expected %d.',
        $anonymize->getNumberOfParameters(),
        $expectedAnonymizeParameters,
    ));
}
if ($expectedAnonymizeParameters === 3) {
    assertHttpFoundationMethod(IpUtils::class, 'anonymize', [1 => 'int', 2 => 'int'], 'string', true);
}
