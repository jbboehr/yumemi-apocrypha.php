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

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\FactoryImmutable;
use Carbon\WrapperClock;
use Composer\InstalledVersions;

require __DIR__ . '/vendor/autoload.php';

$profile = $argv[1] ?? throw new RuntimeException('Expected a Carbon profile.');
$version = InstalledVersions::getPrettyVersion('nesbot/carbon')
    ?? throw new RuntimeException('Unable to resolve the installed Carbon version.');
$normalizedVersion = ltrim($version, 'v');

$ranges = [
    '2' => ['2.62.1', '3.0.0'],
    '3-real' => ['3.0.0', '3.2.0'],
    '3-utc' => ['3.2.0', '4.0.0'],
];

[$minimum, $maximum] = $ranges[$profile] ?? throw new RuntimeException(sprintf('Unknown Carbon profile %s.', $profile));

if (version_compare($normalizedVersion, $minimum, '<') || version_compare($normalizedVersion, $maximum, '>=')) {
    throw new RuntimeException(sprintf(
        'Carbon %s does not belong to requested profile %s (%s <= version < %s).',
        $version,
        $profile,
        $minimum,
        $maximum,
    ));
}

$dateClasses = [CarbonInterface::class, Carbon::class, CarbonImmutable::class];

if ($profile === '2') {
    $methods = [
        'diffInRealMicroseconds',
        'diffInRealMilliseconds',
        'diffInRealSeconds',
        'diffInRealMinutes',
        'diffInRealHours',
        'floatDiffInRealSeconds',
        'floatDiffInRealMinutes',
        'floatDiffInRealHours',
    ];

    foreach ($dateClasses as $class) {
        $reflection = new ReflectionClass($class);

        foreach ($methods as $methodName) {
            $method = $reflection->getMethod($methodName);

            if (!$method->isPublic() || $method->getNumberOfParameters() !== 2 || $method->getReturnType() !== null) {
                throw new RuntimeException(sprintf('%s::%s() does not have the expected Carbon 2 shape.', $class, $methodName));
            }
        }
    }

    $date = CarbonImmutable::parse('2026-01-01 00:00:00');
    foreach (['Microseconds', 'Milliseconds', 'Seconds', 'Minutes', 'Hours'] as $unit) {
        $added = $date->{'addReal'.$unit}(2);
        $difference = $date->{'diffInReal'.$unit}($added);

        if ($difference !== 2) {
            throw new RuntimeException(sprintf('Carbon 2 real-time %s methods did not preserve their fixed unit.', $unit));
        }
    }

    foreach (['Seconds', 'Minutes', 'Hours'] as $unit) {
        $difference = $date->{'floatDiffInReal'.$unit}($date->{'addReal'.$unit}(2));

        if ((float) $difference !== 2.0) {
            throw new RuntimeException(sprintf('Carbon 2 floating real-time %s difference was not preserved.', $unit));
        }
    }

    if ($date->addRealSeconds(2)->getTimestamp() !== $date->getTimestamp() + 2) {
        throw new RuntimeException('Carbon 2 addRealSeconds() did not add timestamp seconds.');
    }

    exit(0);
}

$methods = [
    'diffInMicroseconds',
    'diffInMilliseconds',
    'diffInSeconds',
    'diffInMinutes',
    'diffInHours',
    'secondsSinceMidnight',
    'secondsUntilEndOfDay',
    'sleep',
];

foreach ($dateClasses as $class) {
    $reflection = new ReflectionClass($class);

    foreach ($methods as $methodName) {
        $method = $reflection->getMethod($methodName);
        $returnType = $method->getReturnType();

        if (!$method->isPublic() || $returnType === null) {
            throw new RuntimeException(sprintf('%s::%s() does not have the expected Carbon 3 shape.', $class, $methodName));
        }
    }
}

foreach ([FactoryImmutable::class, WrapperClock::class] as $class) {
    $method = (new ReflectionClass($class))->getMethod('sleep');

    if (!$method->isPublic() || (string) $method->getReturnType() !== 'void') {
        throw new RuntimeException(sprintf('%s::sleep() does not have the expected shape.', $class));
    }
}

$prefix = $profile === '3-real' ? 'Real' : 'UTC';
$date = CarbonImmutable::parse('2026-01-01 00:00:00');
foreach (['Microseconds', 'Milliseconds', 'Seconds', 'Minutes', 'Hours'] as $unit) {
    $added = $date->{'add'.$prefix.$unit}(2);
    $differenceMethod = $profile === '3-real' ? 'diffIn'.$unit : 'diffInUTC'.$unit;
    $difference = $date->{$differenceMethod}($added);

    if ($difference !== 2.0) {
        throw new RuntimeException(sprintf('Carbon %s %s methods did not preserve their fixed unit.', $prefix, $unit));
    }
}

if ($date->{'add'.$prefix.'Seconds'}(2)->getTimestamp() !== $date->getTimestamp() + 2) {
    throw new RuntimeException(sprintf('Carbon %s timestamp methods did not preserve fixed-second semantics.', $prefix));
}
