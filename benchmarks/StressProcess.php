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

namespace jbboehr\Yumemi\Apocrypha\Benchmarks;

use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

final class StressProcess
{
    /**
     * @param list<string> $command
     */
    public static function create(array $command, string $metricsFile, string $cwd, float $maxSeconds): Process
    {
        $time = (new ExecutableFinder())->find('time');
        if ($time === null) {
            throw new \RuntimeException('GNU time is required; run through nix develop.');
        }
        $version = new Process([$time, '--version']);
        $version->mustRun();
        if (!str_contains($version->getOutput(), 'GNU Time')) {
            throw new \RuntimeException('GNU time is required; run through nix develop.');
        }
        $timeout = (new ExecutableFinder())->find('timeout');
        if ($timeout === null) {
            throw new \RuntimeException('GNU timeout is required; run through nix develop.');
        }
        $version = new Process([$timeout, '--version']);
        $version->mustRun();
        if (!str_contains($version->getOutput(), 'GNU coreutils')) {
            throw new \RuntimeException('GNU timeout is required; run through nix develop.');
        }

        // PHPStan's AgentDetector enables extra stderr help when these output-mode flags are inherited.
        $environment = array_fill_keys([
            'AUGMENT_AGENT',
            'AMP_CURRENT_THREAD_ID',
            'AI_AGENT',
            'CURSOR_TRACE_ID',
            'CURSOR_AGENT',
            'GEMINI_CLI',
            'CODEX_SANDBOX',
            'CODEX_THREAD_ID',
            'OPENCODE_CLIENT',
            'OPENCODE',
            'CLAUDECODE',
            'CLAUDE_CODE',
            'REPL_ID',
        ], false);

        // Keep time outside the timed process group so it can reap timeout and retain measurements after SIGKILL.
        return new Process([
            $time, '--quiet', '--format=%e\t%M', '--output=' . $metricsFile,
            $timeout, '--signal=KILL', sprintf('%.6Fs', $maxSeconds + 5),
            ...$command,
        ], $cwd, env: $environment, timeout: null);
    }
}
