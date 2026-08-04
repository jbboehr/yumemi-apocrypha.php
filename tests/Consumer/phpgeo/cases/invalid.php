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

use Location\Bearing\BearingSpherical;
use Location\CardinalDirection\CardinalDirectionDistances;
use Location\Coordinate;
use Location\Distance\Haversine;
use Location\Polygon;
use Location\Processor\Polyline\SimplifyDouglasPeucker;
use Location\Utility\PointToLineDistance;

use function jbboehr\Yumemi\unit;

/** @param unit_float<'kilometer'> $distance */
function recordPhpgeoKilometers(float $distance): void
{
}

/** @param unit_float<'radian'> $bearing */
function recordPhpgeoRadians(float $bearing): void
{
}

/** @param unit_float<'meter'> $distance */
function recordPhpgeoLinearMeters(float $distance): void
{
}

$berlin = new Coordinate(52.5, 13.5);
$london = new Coordinate(51.5, -0.12);
$haversine = new Haversine();
$bearing = new BearingSpherical();

recordPhpgeoKilometers($haversine->getDistance($berlin, $london));
recordPhpgeoRadians($bearing->calculateBearing($berlin, $london));

$bearing->calculateDestination($berlin, 90.0, unit(1000.0, 'meter'));
$bearing->calculateDestination($berlin, unit(1.0, 'radian'), unit(1000.0, 'meter'));
$bearing->calculateDestination($berlin, unit(90.0, 'degree'), 1000.0);
$bearing->calculateDestination($berlin, unit(90.0, 'degree'), unit(1.0, 'kilometer'));

$berlin->hasSameLocation($london, 1.0);
$berlin->hasSameLocation($london, unit(1.0, 'kilometer'));
new SimplifyDouglasPeucker(10.0);
new SimplifyDouglasPeucker(unit(100.0, 'centimeter'));
new PointToLineDistance($haversine, 0.001);
new PointToLineDistance($haversine, unit(1.0, 'second'));

CardinalDirectionDistances::create()->setNorth(100.0);
CardinalDirectionDistances::create()->setNorth(unit(0.1, 'kilometer'));

$polygon = new Polygon();
$polygon->addPoint(new Coordinate(52.5, 13.5));
$polygon->addPoint(new Coordinate(52.6, 13.5));
$polygon->addPoint(new Coordinate(52.6, 13.6));
recordPhpgeoLinearMeters($polygon->getArea());
