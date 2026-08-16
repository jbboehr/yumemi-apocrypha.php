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

function rejectInvalidMailUnits(
    MailQueue $mailQueue,
    MailableContract $contractMailable,
    Mailable $mailable,
    QueueFactory $queue,
    Mailer $mailer,
    PendingMail $pendingMail,
    SendQueuedMailable $job,
): void {
    $mailQueue->later(unit(1, 'minute'), $mailable);
    $contractMailable->later(unit(1, 'minute'), $queue);
    $mailable->later(unit(1, 'minute'), $queue);
    $mailer->later(unit(1, 'minute'), $mailable);
    $mailer->laterOn('mail', unit(1, 'minute'), $mailable);
    $pendingMail->later(unit(1, 'minute'), $contractMailable);
    Mail::later(unit(1, 'minute'), $mailable);
    Mail::laterOn('mail', unit(1, 'minute'), $mailable);
    $job->timeout = unit(1, 'minute');
    $job->timeout = 30;

    $mailQueue->later(unit(30, 'second'), 123, new stdClass());
    $mailer->later(unit(30, 'second'), 123, new stdClass());
    $mailer->laterOn(new stdClass(), unit(30, 'second'), 123);
}
