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

$version = Composer\InstalledVersions::getPrettyVersion('james-heinrich/getid3')
    ?? Composer\InstalledVersions::getVersion('james-heinrich/getid3');
$normalizedVersion = is_string($version) ? ltrim($version, 'v') : null;
if ($normalizedVersion === null || preg_match('/^([12])\./', $normalizedVersion, $matches) !== 1) {
    throw new RuntimeException(sprintf('Unexpected installed getID3 version %s.', $version ?? 'unknown'));
}

$major = (int) $matches[1];
$minimumVersion = $major === 1 ? '1.9.22' : '2.0.0-beta6';
if (version_compare($normalizedVersion, $minimumVersion, '<')) {
    throw new RuntimeException(sprintf('Unexpected installed getID3 version %s.', $version ?? 'unknown'));
}

$expectedStub = sprintf('getid3-%d.stub', $major);
$analyzerClass = $major === 1 ? getID3::class : JamesHeinrich\GetID3\GetID3::class;

$stubFiles = (new jbboehr\Yumemi\Apocrypha\PHPStan\ConfiguredIntegrationStubFilesExtension(
    ['james-heinrich/getid3'],
    false,
    true,
))->getFiles();
if (count($stubFiles) !== 1 || basename($stubFiles[0]) !== $expectedStub) {
    throw new RuntimeException(sprintf(
        'getID3 %s selected unexpected stubs [%s].',
        $version,
        implode(', ', array_map('basename', $stubFiles)),
    ));
}

$reflection = new ReflectionClass($analyzerClass);
foreach ([
    'openfile' => ['filename', 'filesize', 'fp'],
    'analyze' => ['filename', 'filesize', 'original_filename', 'fp'],
] as $methodName => $parameterNames) {
    $method = $reflection->getMethod($methodName);
    if (!$method->isPublic()) {
        throw new RuntimeException(sprintf('%s::%s() is not public.', $analyzerClass, $methodName));
    }

    $actualNames = array_map(
        static fn (ReflectionParameter $parameter): string => $parameter->getName(),
        $method->getParameters(),
    );
    if ($actualNames !== $parameterNames) {
        throw new RuntimeException(sprintf(
            '%s::%s() parameters are [%s]; expected [%s].',
            $analyzerClass,
            $methodName,
            implode(', ', $actualNames),
            implode(', ', $parameterNames),
        ));
    }
}

$sampleCount = 8000;
$sampleData = str_repeat("\x80", $sampleCount);
$wave = 'RIFF'
    . pack('V', 36 + $sampleCount)
    . 'WAVEfmt '
    . pack('VvvVVvv', 16, 1, 1, 8000, 8000, 1, 8)
    . 'data'
    . pack('V', $sampleCount)
    . $sampleData;
$sampleFile = tempnam(sys_get_temp_dir(), 'yumemi-getid3-');
if ($sampleFile === false || file_put_contents($sampleFile, $wave) !== strlen($wave)) {
    throw new RuntimeException('Unable to create the getID3 runtime fixture.');
}

try {
    $info = (new $analyzerClass())->analyze($sampleFile);
} finally {
    unlink($sampleFile);
}

$expected = [
    'filesize' => 8044,
    'avdataoffset' => 44,
    'avdataend' => 8044,
    'bitrate' => 64000,
    'playtime_seconds' => 1.0,
];
foreach ($expected as $key => $value) {
    if (!array_key_exists($key, $info) || $info[$key] !== $value) {
        throw new RuntimeException(sprintf(
            'getID3 returned unexpected %s value %s.',
            $key,
            var_export($info[$key] ?? null, true),
        ));
    }
}

if (($info['audio']['bitrate'] ?? null) !== 64000 || ($info['audio']['sample_rate'] ?? null) !== 8000) {
    throw new RuntimeException('getID3 returned unexpected audio rate metadata.');
}
