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

namespace jbboehr\Yumemi\Apocrypha\Tools\StubCandidateSurvey;

/**
 * @phpstan-import-type UnitMatch from Schema
 */
final class UnitLexicon
{
    /** @var array<non-empty-string, array<non-empty-string, list<non-empty-string>>> */
    private const TERMS = [
        'time' => [
            'nanosecond' => ['nanosecond', 'nanoseconds', 'ns'],
            'microsecond' => ['microsecond', 'microseconds', 'us', 'µs'],
            'millisecond' => ['millisecond', 'milliseconds', 'ms'],
            'second' => ['second', 'seconds', 'sec', 'secs'],
            'minute' => ['minute', 'minutes'],
            'hour' => ['hour', 'hours', 'hr', 'hrs'],
            'day' => ['day', 'days'],
        ],
        'data' => [
            'byte' => ['byte', 'bytes', 'octet', 'octets'],
            'kilobyte' => ['kilobyte', 'kilobytes', 'kb'],
            'kibibyte' => ['kibibyte', 'kibibytes', 'kib'],
            'megabyte' => ['megabyte', 'megabytes', 'mb'],
            'mebibyte' => ['mebibyte', 'mebibytes', 'mib'],
            'gigabyte' => ['gigabyte', 'gigabytes', 'gb'],
            'gibibyte' => ['gibibyte', 'gibibytes', 'gib'],
        ],
        'length' => [
            'micrometre' => ['micrometer', 'micrometers', 'micrometre', 'micrometres'],
            'millimetre' => ['millimeter', 'millimeters', 'millimetre', 'millimetres'],
            'centimetre' => ['centimeter', 'centimeters', 'centimetre', 'centimetres'],
            'metre' => ['meter', 'meters', 'metre', 'metres'],
            'kilometre' => ['kilometer', 'kilometers', 'kilometre', 'kilometres'],
            'inch' => ['inch', 'inches'],
            'foot' => ['foot', 'feet'],
            'yard' => ['yard', 'yards'],
            'mile' => ['mile', 'miles'],
            'pixel' => ['pixel', 'pixels', 'px'],
            'point' => ['typographic point', 'typographic points', 'point (pt)', 'points (pt)', 'pt'],
            'pica' => ['pica', 'picas'],
            'twip' => ['twip', 'twips'],
            'emu' => ['english metric unit', 'english metric units', 'emu'],
        ],
        'angle' => [
            'degree' => ['degree', 'degrees', 'deg'],
            'radian' => ['radian', 'radians', 'rad'],
        ],
        'ratio' => [
            'fraction' => ['fraction', 'fractions', 'ratio', 'ratios'],
            'percent' => ['percent', 'percentage', 'percentages'],
            'basis-point' => ['basis point', 'basis points'],
        ],
        'mass' => [
            'milligram' => ['milligram', 'milligrams'],
            'gram' => ['gram', 'grams'],
            'kilogram' => ['kilogram', 'kilograms'],
            'ounce' => ['ounce', 'ounces'],
            'pound' => ['pound', 'pounds'],
        ],
        'temperature' => [
            'celsius' => ['celsius', 'centigrade'],
            'fahrenheit' => ['fahrenheit'],
            'kelvin' => ['kelvin', 'kelvins'],
        ],
        'frequency' => [
            'hertz' => ['hertz', 'hz'],
            'kilohertz' => ['kilohertz', 'khz'],
            'megahertz' => ['megahertz', 'mhz'],
            'gigahertz' => ['gigahertz', 'ghz'],
        ],
        'resolution' => [
            'dpi' => ['dots per inch', 'dpi'],
            'ppi' => ['pixels per inch', 'ppi'],
        ],
        'frame-rate' => [
            'fps' => ['frames per second', 'frame per second', 'fps'],
        ],
        'energy' => [
            'joule' => ['joule', 'joules'],
            'kilojoule' => ['kilojoule', 'kilojoules'],
            'watt-hour' => ['watt hour', 'watt hours'],
            'kilowatt-hour' => ['kilowatt hour', 'kilowatt hours'],
        ],
        'power' => [
            'watt' => ['watt', 'watts'],
            'kilowatt' => ['kilowatt', 'kilowatts'],
        ],
        'pressure' => [
            'pascal' => ['pascal', 'pascals'],
            'kilopascal' => ['kilopascal', 'kilopascals'],
            'psi' => ['pounds per square inch', 'psi'],
        ],
        'volume' => [
            'millilitre' => ['milliliter', 'milliliters', 'millilitre', 'millilitres'],
            'litre' => ['liter', 'liters', 'litre', 'litres'],
            'gallon' => ['gallon', 'gallons'],
        ],
    ];

    /**
     * Terms no longer than this are accepted only in structured source contexts.
     */
    private const SHORT_TERM_LENGTH = 3;

    /** @return list<UnitMatch> */
    public function detect(string $text, string $context): array
    {
        if ('' === $text) {
            return [];
        }

        $normalized = preg_replace('/(?<=[a-z0-9])(?=[A-Z])/u', ' ', $text) ?? $text;
        $normalized = strtolower(str_replace(['_', '-'], ' ', $normalized));
        $matches = [];

        foreach (self::TERMS as $dimension => $scales) {
            foreach ($scales as $scale => $terms) {
                foreach ($terms as $term) {
                    if (!$this->contains($normalized, $text, $term, $context)) {
                        continue;
                    }
                    $key = $dimension . "\0" . $scale;
                    $matches[$key] = [
                        'dimension' => $dimension,
                        'scale' => $scale,
                        'term' => $term,
                        'context' => $this->excerpt($text, $term),
                    ];
                    break;
                }
            }
        }

        return array_values($matches);
    }

    private function contains(string $normalized, string $original, string $term, string $context): bool
    {
        $termLength = function_exists('mb_strlen') ? mb_strlen($term) : strlen($term);
        if ($termLength <= self::SHORT_TERM_LENGTH) {
            if ('signature' !== $context) {
                return 1 === preg_match(
                    '/(?:[`\[(]|\d\s*)' . preg_quote($term, '/') . '(?:[`\])]|\b)/iu',
                    $original,
                );
            }
        }

        return 1 === preg_match('/(?<![a-z])' . preg_quote(strtolower($term), '/') . '(?![a-z])/u', $normalized);
    }

    private function excerpt(string $text, string $term): string
    {
        $flat = trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
        $position = stripos($flat, $term);
        $start = false === $position ? 0 : max(0, $position - 70);
        $excerpt = substr($flat, $start, 200);

        return '' === $excerpt ? $term : $excerpt;
    }
}
