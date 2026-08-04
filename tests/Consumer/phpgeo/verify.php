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

$version = Composer\InstalledVersions::getPrettyVersion('mjaschen/phpgeo')
    ?? Composer\InstalledVersions::getVersion('mjaschen/phpgeo');
if ($version === null || preg_match('/^v?([456])\./', $version, $matches) !== 1) {
    throw new RuntimeException(sprintf('Unexpected installed phpgeo version %s.', $version ?? 'unknown'));
}

$stubFiles = (new jbboehr\Yumemi\Apocrypha\PHPStan\ConfiguredIntegrationStubFilesExtension(
    ['mjaschen/phpgeo'],
    false,
    true,
))->getFiles();
if (count($stubFiles) !== 1 || basename($stubFiles[0]) !== 'phpgeo.stub') {
    throw new RuntimeException(sprintf(
        'phpgeo %s selected unexpected stubs [%s].',
        $version,
        implode(', ', array_map('basename', $stubFiles)),
    ));
}

/**
 * @param class-string $class
 * @param array<string, list<string>> $methods
 */
function verifyPhpgeoMethods(string $class, array $methods): void
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

verifyPhpgeoMethods(Location\Distance\DistanceInterface::class, [
    'getDistance' => ['point1', 'point2'],
]);
verifyPhpgeoMethods(Location\Distance\Haversine::class, [
    'getDistance' => ['point1', 'point2'],
]);
verifyPhpgeoMethods(Location\Distance\Vincenty::class, [
    'getDistance' => ['point1', 'point2'],
]);
verifyPhpgeoMethods(Location\Coordinate::class, [
    'getDistance' => ['coordinate', 'calculator'],
    'hasSameLocation' => ['coordinate', 'allowedDistance'],
]);
verifyPhpgeoMethods(Location\Line::class, [
    'getLength' => ['calculator'],
    'getBearing' => ['bearingCalculator'],
    'getFinalBearing' => ['bearingCalculator'],
]);
verifyPhpgeoMethods(Location\Polyline::class, [
    'addUniquePoint' => ['point', 'allowedDistance'],
    'containsPoint' => ['point', 'allowedDistance'],
    'getLength' => ['calculator'],
]);
verifyPhpgeoMethods(Location\Polygon::class, [
    'getPerimeter' => ['calculator'],
    'getArea' => [],
]);
verifyPhpgeoMethods(Location\Bearing\BearingInterface::class, [
    'calculateBearing' => ['point1', 'point2'],
    'calculateFinalBearing' => ['point1', 'point2'],
    'calculateDestination' => ['point', 'bearing', 'distance'],
]);
verifyPhpgeoMethods(Location\Bearing\BearingSpherical::class, [
    'calculateBearing' => ['point1', 'point2'],
    'calculateFinalBearing' => ['point1', 'point2'],
    'calculateDestination' => ['point', 'bearing', 'distance'],
]);
verifyPhpgeoMethods(Location\Bearing\BearingEllipsoidal::class, [
    'calculateBearing' => ['point1', 'point2'],
    'calculateFinalBearing' => ['point1', 'point2'],
    'calculateDestination' => ['point', 'bearing', 'distance'],
    'calculateDestinationFinalBearing' => ['point', 'bearing', 'distance'],
]);
verifyPhpgeoMethods(Location\CardinalDirection\CardinalDirectionDistances::class, [
    'setNorth' => ['north'],
    'setEast' => ['east'],
    'setSouth' => ['south'],
    'setWest' => ['west'],
    'getNorth' => [],
    'getEast' => [],
    'getSouth' => [],
    'getWest' => [],
]);
verifyPhpgeoMethods(Location\Factory\BoundsFactory::class, [
    'expandFromCenterCoordinate' => ['center', 'distance', 'bearing'],
]);
verifyPhpgeoMethods(Location\Utility\PerpendicularDistance::class, [
    'getPerpendicularDistance' => ['point', 'line'],
]);
verifyPhpgeoMethods(Location\Utility\PointToLineDistance::class, [
    '__construct' => ['distanceCalculator', 'epsilon'],
    'getDistance' => ['point', 'line'],
]);
verifyPhpgeoMethods(Location\Processor\Polyline\SimplifyDouglasPeucker::class, [
    '__construct' => ['tolerance'],
]);

$origin = new Location\Coordinate(0.0, 0.0);
$east = new Location\Coordinate(0.0, 1.0);
$distance = (new Location\Distance\Haversine())->getDistance($origin, $east);
if ($distance < 111000.0 || $distance > 111300.0) {
    throw new RuntimeException(sprintf('Expected a one-degree equatorial distance in meters, got %f.', $distance));
}

$bearing = (new Location\Bearing\BearingSpherical())->calculateBearing($origin, $east);
if (abs($bearing - 90.0) > 0.000001) {
    throw new RuntimeException(sprintf('Expected an eastward bearing in degrees, got %f.', $bearing));
}
