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

$version = Composer\InstalledVersions::getPrettyVersion('nmarfurt/measurements')
    ?? Composer\InstalledVersions::getVersion('nmarfurt/measurements');
$normalizedVersion = is_string($version) ? ltrim($version, 'v') : null;
if (
    $normalizedVersion === null
    || preg_match('/^1\./', $normalizedVersion) !== 1
    || version_compare($normalizedVersion, '1.4.0', '<')
) {
    throw new RuntimeException(sprintf('Unexpected installed Measurements version %s.', $version ?? 'unknown'));
}

$selection = new jbboehr\Yumemi\Apocrypha\PHPStan\ConfiguredIntegrationStubFilesExtension(
    ['nmarfurt/measurements'],
    false,
    true,
);
$stubFiles = $selection->getFiles();
if ($stubFiles !== []) {
    throw new RuntimeException(sprintf(
        'Measurements %s unexpectedly enabled stubs [%s].',
        $version,
        implode(', ', array_map('basename', $stubFiles)),
    ));
}
if (!$selection->usesUnitBoundaryAdapter('nmarfurt/measurements')) {
    throw new RuntimeException('Measurements did not select the unit-boundary adapter.');
}

$factories = [
    Measurements\Quantities\Length::class => [
        'megameters',
        'kilometers',
        'hectometers',
        'decameters',
        'meters',
        'decimeters',
        'centimeters',
        'millimeters',
        'micrometers',
        'nanometers',
        'picometers',
        'inches',
        'feet',
        'yards',
        'miles',
        'lightyears',
        'nauticalMiles',
        'fathoms',
        'furlongs',
        'astronomicalUnits',
        'parsecs',
    ],
    Measurements\Quantities\Duration::class => [
        'seconds',
        'minutes',
        'hours',
    ],
];

foreach ($factories as $class => $methods) {
    $reflection = new ReflectionClass($class);
    $docComment = $reflection->getDocComment();
    if ($docComment === false) {
        throw new RuntimeException(sprintf('%s has no upstream PHPDoc.', $class));
    }

    $shortName = $reflection->getShortName();
    foreach ($methods as $method) {
        $declaration = sprintf('@method static %s %s(float $value)', $shortName, $method);
        if (!str_contains($docComment, $declaration)) {
            throw new RuntimeException(sprintf('%s is missing upstream declaration %s.', $class, $declaration));
        }
    }
}

$magicFactory = new ReflectionMethod(Measurements\Measurement::class, '__callStatic');
if (!$magicFactory->isPublic() || !$magicFactory->isStatic()) {
    throw new RuntimeException('Measurement::__callStatic() is not public and static.');
}
$parameters = array_map(
    static fn (ReflectionParameter $parameter): string => $parameter->getName(),
    $magicFactory->getParameters(),
);
if ($parameters !== ['method', 'parameters']) {
    throw new RuntimeException(sprintf(
        'Measurement::__callStatic() parameters are [%s]; expected [method, parameters].',
        implode(', ', $parameters),
    ));
}

$meters = Measurements\Quantities\Length::meters(4.48);
$feet = Measurements\Quantities\Length::feet(3.0);
$hours = Measurements\Quantities\Duration::hours(1.5);
if ((string) $meters->unit() !== 'm' || (string) $feet->unit() !== 'ft' || (string) $hours->unit() !== 'hr') {
    throw new RuntimeException('Measurements magic factories returned unexpected units.');
}
