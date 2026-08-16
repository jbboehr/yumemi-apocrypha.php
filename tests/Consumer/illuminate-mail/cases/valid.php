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

use Illuminate\Contracts\Mail\MailQueue;
use Illuminate\Contracts\Mail\Mailable as MailableContract;
use Illuminate\Contracts\Queue\Factory as QueueFactory;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\PendingMail;
use Illuminate\Mail\SendQueuedMailable;
use Illuminate\Support\Facades\Mail;

use function jbboehr\Yumemi\unit;
use function PHPStan\Testing\assertType;

function scheduleMailInSeconds(
    MailQueue $mailQueue,
    MailableContract $contractMailable,
    Mailable $mailable,
    QueueFactory $queue,
    Mailer $mailer,
    PendingMail $pendingMail,
    SendQueuedMailable $job,
): void {
    $mailQueue->later(unit(30, 'second'), $mailable);
    $contractMailable->later(unit(30, 'second'), $queue);
    $mailable->later(unit(30, 'second'), $queue);
    $mailer->later(unit(30, 'second'), $mailable);
    $mailer->laterOn('mail', unit(30, 'second'), $mailable);
    $pendingMail->later(unit(30, 'second'), $contractMailable);
    Mail::later(unit(30, 'second'), $mailable);
    Mail::laterOn('mail', unit(30, 'second'), $mailable);

    $mailQueue->later(new DateInterval('PT30S'), $mailable);
    $mailer->later(new DateTimeImmutable('+30 seconds'), $mailable);

    assertType("unit_int<'second'>|null", $job->timeout);
    $job->timeout = unit(30, 'second');
}
