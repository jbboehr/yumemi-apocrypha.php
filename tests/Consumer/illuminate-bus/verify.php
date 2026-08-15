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

$version = Composer\InstalledVersions::getVersion('illuminate/bus')
    ?? Composer\InstalledVersions::getVersion('laravel/framework');
if ($version === null || preg_match('/^v?(11|12|13)\./', $version, $matches) !== 1) {
    throw new RuntimeException(sprintf('Unable to determine a supported Illuminate Bus version from %s.', $version ?? 'null'));
}

$major = (int) $matches[1];
$normalizedVersion = ltrim($version, 'v');
$rangedProgress = $major === 13
    || ($major === 12 && ($normalizedVersion === '12.x-dev' || version_compare($normalizedVersion, '12.52.0', '>=')));

$queueable = new ReflectionClass(Illuminate\Bus\Queueable::class);
if (!$queueable->isTrait() || !$queueable->hasProperty('delay')) {
    throw new RuntimeException('Illuminate Bus Queueable does not expose the expected $delay property.');
}

$delayParameters = array_map(
    static fn (ReflectionParameter $parameter): string => $parameter->getName(),
    $queueable->getMethod('delay')->getParameters(),
);
if ($delayParameters !== ['delay']) {
    throw new RuntimeException('Illuminate Bus Queueable::delay() has an unexpected signature.');
}

$progress = new ReflectionMethod(Illuminate\Bus\Batch::class, 'progress');
if ($progress->getNumberOfParameters() !== 0) {
    throw new RuntimeException('Illuminate Bus Batch::progress() unexpectedly accepts parameters.');
}

$progressDoc = $progress->getDocComment();
$declaresRange = is_string($progressDoc) && str_contains($progressDoc, '@return int<0, 100>');
if ($declaresRange !== $rangedProgress) {
    throw new RuntimeException(sprintf('Illuminate Bus %s has an unexpected Batch::progress() PHPDoc range.', $version));
}

$job = new class () {
    use Illuminate\Bus\Queueable;
};
$job->delay(30);
if ($job->delay !== 30) {
    throw new RuntimeException('Illuminate Bus Queueable::delay() did not preserve its scalar value.');
}
$job->withoutDelay();
if ($job->delay !== 0) {
    throw new RuntimeException('Illuminate Bus Queueable::withoutDelay() did not store zero seconds.');
}

$batch = (new ReflectionClass(Illuminate\Bus\Batch::class))->newInstanceWithoutConstructor();
$batch->totalJobs = 4;
$batch->pendingJobs = 1;
$runtimeProgress = $batch->progress();
if ($runtimeProgress !== ($rangedProgress ? 75 : 75.0)) {
    throw new RuntimeException(sprintf(
        'Illuminate Bus %s returned an unexpected progress value of %s.',
        $version,
        var_export($runtimeProgress, true),
    ));
}
