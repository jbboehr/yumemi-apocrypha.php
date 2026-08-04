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

use Location\Bearing\BearingEllipsoidal;
use Location\Bearing\BearingSpherical;
use Location\CardinalDirection\CardinalDirectionDistances;
use Location\Coordinate;
use Location\Distance\Haversine;
use Location\Distance\Vincenty;
use Location\Factory\BoundsFactory;
use Location\Line;
use Location\Polygon;
use Location\Polyline;
use Location\Processor\Polyline\SimplifyDouglasPeucker;
use Location\Utility\PerpendicularDistance;
use Location\Utility\PointToLineDistance;

use function jbboehr\Yumemi\unit;
use function PHPStan\Testing\assertType;

/** @param unit_float<'meter'> $distance */
function recordPhpgeoMeters(float $distance): void
{
}

/** @param unit_float<'degree'> $bearing */
function recordPhpgeoDegrees(float $bearing): void
{
}

/** @param unit_float<'meter ^ 2'> $area */
function recordPhpgeoSquareMeters(float $area): void
{
}

$berlin = new Coordinate(52.5, 13.5);
$london = new Coordinate(51.5, -0.12);
$haversine = new Haversine();
$vincenty = new Vincenty();

$distance = $haversine->getDistance($berlin, $london);
assertType("unit_float<'meter'>", $distance);
recordPhpgeoMeters($distance);
recordPhpgeoMeters($vincenty->getDistance($berlin, $london));
recordPhpgeoMeters($berlin->getDistance($london, $haversine));
$berlin->hasSameLocation($london, unit(1.0, 'meter'));

$line = new Line($berlin, $london);
$spherical = new BearingSpherical();
$ellipsoidal = new BearingEllipsoidal();
recordPhpgeoMeters($line->getLength($haversine));
recordPhpgeoDegrees($line->getBearing($spherical));
recordPhpgeoDegrees($line->getFinalBearing($ellipsoidal));
recordPhpgeoDegrees($spherical->calculateBearing($berlin, $london));
recordPhpgeoDegrees($ellipsoidal->calculateFinalBearing($berlin, $london));
$spherical->calculateDestination($berlin, unit(90.0, 'degree'), unit(1000.0, 'meter'));
$ellipsoidal->calculateDestination($berlin, unit(90.0, 'degree'), unit(1000.0, 'meter'));
recordPhpgeoDegrees($ellipsoidal->calculateDestinationFinalBearing(
    $berlin,
    unit(90.0, 'degree'),
    unit(1000.0, 'meter'),
));

$polyline = new Polyline();
$polyline->addUniquePoint($berlin, unit(0.001, 'meter'));
$polyline->addUniquePoint($london, unit(0.001, 'meter'));
$polyline->containsPoint($berlin, unit(0.001, 'meter'));
recordPhpgeoMeters($polyline->getLength($haversine));

$polygon = new Polygon();
$polygon->addPoint(new Coordinate(52.5, 13.5));
$polygon->addPoint(new Coordinate(52.6, 13.5));
$polygon->addPoint(new Coordinate(52.6, 13.6));
recordPhpgeoMeters($polygon->getPerimeter($haversine));
recordPhpgeoSquareMeters($polygon->getArea());
assertType("unit_float<'meter ^ 2'>", $polygon->getArea());

BoundsFactory::expandFromCenterCoordinate($berlin, unit(500.0, 'meter'), $spherical);
recordPhpgeoMeters((new PerpendicularDistance())->getPerpendicularDistance($berlin, $line));
recordPhpgeoMeters((new PointToLineDistance(
    $haversine,
    unit(0.001, 'meter'),
))->getDistance($berlin, $line));
new SimplifyDouglasPeucker(unit(10.0, 'meter'));

$cardinal = CardinalDirectionDistances::create()
    ->setNorth(unit(100.0, 'meter'))
    ->setEast(unit(50.0, 'meter'))
    ->setSouth(unit(25.0, 'meter'))
    ->setWest(unit(10.0, 'meter'));
recordPhpgeoMeters($cardinal->getNorth());
recordPhpgeoMeters($cardinal->getEast());
recordPhpgeoMeters($cardinal->getSouth());
recordPhpgeoMeters($cardinal->getWest());
