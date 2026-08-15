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

$version = Composer\InstalledVersions::getVersion('illuminate/console')
    ?? Composer\InstalledVersions::getVersion('laravel/framework');
if ($version === null || preg_match('/^v?(11|12|13)\./', $version, $matches) !== 1) {
    throw new RuntimeException(sprintf('Unable to determine a supported Illuminate Console version from %s.', $version ?? 'null'));
}

$major = (int) $matches[1];
$method = new ReflectionMethod(Illuminate\Console\Scheduling\Event::class, 'withoutOverlapping');
$actualParameters = array_map(
    static fn (ReflectionParameter $parameter): string => $parameter->getName(),
    $method->getParameters(),
);
$expectedParameters = $major === 13 && version_compare(ltrim($version, 'v'), '13.2.0', '>=')
    ? ['expiresAt', 'releaseOnTerminationSignals']
    : ['expiresAt'];
if ($actualParameters !== $expectedParameters) {
    throw new RuntimeException(sprintf(
        'Illuminate Console %d has an unexpected Event::withoutOverlapping() signature.',
        $major,
    ));
}

$reflection = new ReflectionClass(Illuminate\Console\Scheduling\Event::class);
foreach (['repeatSeconds', 'expiresAt'] as $property) {
    if (!$reflection->hasProperty($property)) {
        throw new RuntimeException(sprintf('Illuminate Console Event::$%s does not exist.', $property));
    }
}

$mutex = new class () implements Illuminate\Console\Scheduling\EventMutex {
    public function create(Illuminate\Console\Scheduling\Event $event): bool
    {
        return true;
    }

    public function exists(Illuminate\Console\Scheduling\Event $event): bool
    {
        return false;
    }

    public function forget(Illuminate\Console\Scheduling\Event $event): void
    {
    }
};

$event = new Illuminate\Console\Scheduling\Event($mutex, 'reports:refresh');
$event->everyFiveSeconds()->withoutOverlapping(30);
if ($event->repeatSeconds !== 5 || $event->expiresAt !== 30) {
    throw new RuntimeException('Illuminate Console did not preserve the scheduler second and minute scales.');
}
