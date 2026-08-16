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

$version = Composer\InstalledVersions::getVersion('illuminate/mail')
    ?? Composer\InstalledVersions::getVersion('laravel/framework');
if ($version === null || preg_match('/^v?(11|12|13)\./', $version) !== 1) {
    throw new RuntimeException(sprintf('Unable to determine a supported Illuminate Mail version from %s.', $version ?? 'null'));
}

$checks = [
    Illuminate\Contracts\Mail\MailQueue::class => ['later' => ['delay', 'view', 'queue']],
    Illuminate\Contracts\Mail\Mailable::class => ['later' => ['delay', 'queue']],
    Illuminate\Mail\Mailable::class => ['later' => ['delay', 'queue']],
    Illuminate\Mail\Mailer::class => [
        'later' => ['delay', 'view', 'queue'],
        'laterOn' => ['queue', 'delay', 'view'],
    ],
    Illuminate\Mail\PendingMail::class => ['later' => ['delay', 'mailable']],
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

$queuedMailable = new ReflectionClass(Illuminate\Mail\SendQueuedMailable::class);
if (!$queuedMailable->hasProperty('timeout')) {
    throw new RuntimeException('Illuminate Mail SendQueuedMailable does not expose the expected $timeout property.');
}

$mailable = new class () extends Illuminate\Mail\Mailable {
    public int $timeout = 30;
};
$job = new Illuminate\Mail\SendQueuedMailable($mailable);
if ($job->timeout !== 30) {
    throw new RuntimeException('SendQueuedMailable did not preserve its timeout in seconds.');
}

$jobWithoutTimeout = new Illuminate\Mail\SendQueuedMailable(new class () extends Illuminate\Mail\Mailable {
});
if ($jobWithoutTimeout->timeout !== null) {
    throw new RuntimeException('SendQueuedMailable did not preserve its nullable timeout state.');
}
