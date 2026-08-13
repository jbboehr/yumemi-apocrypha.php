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

$version = Composer\InstalledVersions::getVersion('illuminate/validation')
    ?? Composer\InstalledVersions::getVersion('laravel/framework');
if ($version === null || preg_match('/^v?(11|12|13)\./', $version) !== 1) {
    throw new RuntimeException(sprintf('Unable to determine a supported Illuminate Validation version from %s.', $version ?? 'null'));
}

$checks = [
    Illuminate\Validation\Rules\Dimensions::class => [
        'width' => ['value'],
        'height' => ['value'],
        'minWidth' => ['value'],
        'minHeight' => ['value'],
        'maxWidth' => ['value'],
        'maxHeight' => ['value'],
    ],
    Illuminate\Validation\Rules\File::class => [
        'size' => ['size'],
        'between' => ['minSize', 'maxSize'],
        'min' => ['size'],
        'max' => ['size'],
    ],
];

foreach ($checks as $class => $methods) {
    $reflection = new ReflectionClass($class);
    foreach ($methods as $methodName => $expectedParameters) {
        $actualParameters = array_map(
            static fn (ReflectionParameter $parameter): string => $parameter->getName(),
            $reflection->getMethod($methodName)->getParameters(),
        );
        if ($actualParameters !== $expectedParameters) {
            throw new RuntimeException(sprintf('%s::%s() has an unexpected signature.', $class, $methodName));
        }
    }
}

$phpDocChecks = [
    Illuminate\Validation\Rules\Dimensions::class => [
        'width' => [['int', 'value']],
        'height' => [['int', 'value']],
        'minWidth' => [['int', 'value']],
        'minHeight' => [['int', 'value']],
        'maxWidth' => [['int', 'value']],
        'maxHeight' => [['int', 'value']],
    ],
    Illuminate\Validation\Rules\File::class => [
        'size' => [['string|int', 'size']],
        'between' => [['string|int', 'minSize'], ['string|int', 'maxSize']],
        'min' => [['string|int', 'size']],
        'max' => [['string|int', 'size']],
    ],
];

foreach ($phpDocChecks as $class => $methods) {
    foreach ($methods as $methodName => $parameters) {
        $phpDoc = (new ReflectionMethod($class, $methodName))->getDocComment();
        foreach ($parameters as [$expectedType, $parameterName]) {
            $pattern = sprintf('/@param\s+%s\s+\$%s\b/', preg_quote($expectedType, '/'), $parameterName);
            if ($phpDoc === false || preg_match($pattern, $phpDoc) !== 1) {
                throw new RuntimeException(sprintf(
                    '%s::%s() does not publish the expected %s type for $%s.',
                    $class,
                    $methodName,
                    $expectedType,
                    $parameterName,
                ));
            }
        }
    }
}

if (!is_subclass_of(Illuminate\Validation\Rules\ImageFile::class, Illuminate\Validation\Rules\File::class)) {
    throw new RuntimeException('ImageFile no longer inherits File rule boundaries.');
}

$dimensions = (new Illuminate\Validation\Rules\Dimensions())
    ->width(1200)
    ->height(800)
    ->minWidth(320)
    ->minHeight(240)
    ->maxWidth(3840)
    ->maxHeight(2160);
if ((string) $dimensions !== 'dimensions:width=1200,height=800,min_width=320,min_height=240,max_width=3840,max_height=2160') {
    throw new RuntimeException('Dimensions does not preserve its documented pixel constraints.');
}

$file = (new Illuminate\Validation\Rules\File())->between(64, 2048);
$buildValidationRules = new ReflectionMethod($file, 'buildValidationRules');
$rules = $buildValidationRules->invoke($file);
if (!in_array('between:64,2048', $rules, true)) {
    throw new RuntimeException('File does not preserve integer sizes in Laravel validation kilobytes.');
}
