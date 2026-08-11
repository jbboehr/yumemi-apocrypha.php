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

use Measurements\Quantities\Duration;
use Measurements\Quantities\Length;
use Measurements\Quantities\Temperature;
use Measurements\Units\UnitLength;

use function jbboehr\Yumemi\unit;
use function PHPStan\Testing\assertType;

$distance = Length::meters(unit(4.48, 'meter'));
assertType(Length::class, $distance);
assertType('string', $distance->toString());
assertType('Measurements\Measurement', $distance->convertTo(UnitLength::feet()));
assertType('Measurements\Measurement', $distance->addValue(1.0));

Length::megameters(unit(1.0, 'megameter'));
Length::kilometers(unit(1.0, 'kilometer'));
Length::hectometers(unit(1.0, 'hectometer'));
Length::decameters(unit(1.0, 'dekameter'));
Length::meters(unit(1, 'meter'));
Length::decimeters(unit(1.0, 'decimeter'));
Length::centimeters(unit(1.0, 'centimeter'));
Length::millimeters(unit(1.0, 'millimeter'));
Length::micrometers(unit(1.0, 'micrometer'));
Length::nanometers(unit(1.0, 'nanometer'));
Length::picometers(unit(1.0, 'picometer'));
Length::inches(unit(1.0, 'inch'));
Length::feet(unit(1.0, 'foot'));
Length::yards(unit(1.0, 'yard'));
Length::miles(unit(1.0, 'mile'));
Length::lightyears(unit(1.0, 'light_year'));
Length::nauticalMiles(unit(1.0, 'nautical_mile'));
Length::fathoms(unit(1.0, 'fathom'));
Length::furlongs(unit(1.0, 'furlong'));
Length::astronomicalUnits(unit(1.0, 'astronomical_unit'));
Length::parsecs(unit(1.0, 'parsec'));

Duration::seconds(unit(1.0, 'second'));
Duration::minutes(unit(1.0, 'minute'));
Duration::hours(unit(1.0, 'hour'));

new Length(1.0, UnitLength::meters());
Temperature::celsius(37.0);
