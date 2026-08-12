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

$version = Composer\InstalledVersions::getPrettyVersion('intervention/image')
    ?? Composer\InstalledVersions::getVersion('intervention/image');
$normalizedVersion = is_string($version) ? ltrim($version, 'v') : null;
if ($normalizedVersion === null || preg_match('/^([34])\./', $normalizedVersion, $matches) !== 1) {
    throw new RuntimeException(sprintf('Unexpected installed Intervention Image version %s.', $version ?? 'unknown'));
}

$major = (int) $matches[1];
$extension = new jbboehr\Yumemi\Apocrypha\PHPStan\ConfiguredIntegrationStubFilesExtension(
    ['intervention/image'],
    false,
    true,
);
if ($extension->getFiles() !== [] || !$extension->usesUnitBoundaryAdapter('intervention/image')) {
    throw new RuntimeException('Intervention Image did not select its package-boundary adapter.');
}

if ($extension->getSelectedMajor('intervention/image') !== $major) {
    throw new RuntimeException(sprintf('Intervention Image %s selected the wrong major profile.', $version));
}

$managerMethodName = $major === 3 ? 'create' : 'createImage';
$managerMethod = new ReflectionMethod(Intervention\Image\ImageManager::class, $managerMethodName);
$managerParameterNames = array_map(
    static fn (ReflectionParameter $parameter): string => $parameter->getName(),
    $managerMethod->getParameters(),
);
if (array_slice($managerParameterNames, 0, 2) !== ['width', 'height']) {
    throw new RuntimeException(sprintf(
        'Intervention Image %s manager dimensions are [%s].',
        $version,
        implode(', ', $managerParameterNames),
    ));
}

$imageReflection = new ReflectionClass(Intervention\Image\Interfaces\ImageInterface::class);
$expectedParameters = [
    'width' => [],
    'height' => [],
    'pixelate' => ['size'],
    'rotate' => ['angle', 'background'],
    'text' => ['text', 'x', 'y', 'font'],
    'resize' => ['width', 'height'],
    'resizeDown' => ['width', 'height'],
    'scale' => ['width', 'height'],
    'scaleDown' => ['width', 'height'],
    'cover' => ['width', 'height'],
    'coverDown' => ['width', 'height'],
    'resizeCanvas' => ['width', 'height'],
    'resizeCanvasRelative' => ['width', 'height'],
    'contain' => ['width', 'height'],
    'fill' => ['color', 'x', 'y'],
    'drawPixel' => ['x', 'y', 'color'],
];
if ($major === 3) {
    $expectedParameters += [
        'pad' => ['width', 'height'],
        'crop' => ['width', 'height', 'offset_x', 'offset_y'],
        'place' => ['element', 'position', 'offset_x', 'offset_y'],
        'drawRectangle' => ['x', 'y'],
        'drawEllipse' => ['x', 'y'],
        'drawCircle' => ['x', 'y'],
    ];
} else {
    $expectedParameters += [
        'containDown' => ['width', 'height'],
        'crop' => ['width', 'height', 'x', 'y'],
        'insert' => ['image', 'x', 'y'],
    ];
}

foreach ($expectedParameters as $methodName => $parameterNames) {
    $method = $imageReflection->getMethod($methodName);
    if (!$method->isPublic()) {
        throw new RuntimeException(sprintf('ImageInterface::%s() is not public.', $methodName));
    }

    $actualNames = array_map(
        static fn (ReflectionParameter $parameter): string => $parameter->getName(),
        $method->getParameters(),
    );
    if (array_slice($actualNames, 0, count($parameterNames)) !== $parameterNames) {
        throw new RuntimeException(sprintf(
            'ImageInterface::%s() parameters are [%s]; expected prefix [%s].',
            $methodName,
            implode(', ', $actualNames),
            implode(', ', $parameterNames),
        ));
    }
}

$manager = new Intervention\Image\ImageManager(new Intervention\Image\Drivers\Gd\Driver());
$image = $major === 3 ? $manager->create(6, 4) : $manager->createImage(6, 4);
if ($image->width() !== 6 || $image->height() !== 4) {
    throw new RuntimeException('Intervention Image created an image with unexpected dimensions.');
}

$image->resize(3, 2);
if ($image->width() !== 3 || $image->height() !== 2) {
    throw new RuntimeException('Intervention Image resized an image to unexpected dimensions.');
}
