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

namespace jbboehr\Yumemi\Apocrypha\Tests\Benchmarks;

use jbboehr\Yumemi\Apocrypha\Benchmarks\StressProcess;
use PHPUnit\Framework\Attributes\RequiresOperatingSystemFamily;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

#[RequiresOperatingSystemFamily('Linux')]
final class StressRunnerTest extends TestCase
{
    private string $temporaryRoot = '';

    protected function setUp(): void
    {
        foreach (['time', 'timeout'] as $tool) {
            if ((new ExecutableFinder())->find($tool) === null) {
                self::markTestSkipped('The stress runner integration tests require GNU time and timeout.');
            }
        }
        $this->temporaryRoot = sys_get_temp_dir() . '/apocrypha-stress-test-' . bin2hex(random_bytes(8));
        (new Filesystem())->mkdir($this->temporaryRoot);
    }

    protected function tearDown(): void
    {
        if ($this->temporaryRoot !== '') {
            (new Filesystem())->remove($this->temporaryRoot);
        }
    }

    public function testAgentHintsDoNotChangeMeasuredProcessOutput(): void
    {
        $previous = getenv('CODEX_THREAD_ID');
        putenv('CODEX_THREAD_ID=stress-fixture-test');
        try {
            $process = StressProcess::create(
                [PHP_BINARY, '-r', 'echo getenv("CODEX_THREAD_ID") === false ? "isolated" : "inherited";'],
                $this->temporaryRoot . '/agent.time',
                $this->temporaryRoot,
                10.0,
            );
            $process->run();
            self::assertSame(0, $process->getExitCode());
            self::assertSame('isolated', $process->getOutput());
        } finally {
            putenv($previous === false ? 'CODEX_THREAD_ID' : 'CODEX_THREAD_ID=' . $previous);
        }
    }

    #[RequiresPhpExtension('posix')]
    public function testTimeoutStopsTheMeasuredChild(): void
    {
        $process = StressProcess::create(
            [PHP_BINARY, '-r', 'echo getmypid(), "\n"; fflush(STDOUT); sleep(30);'],
            $this->temporaryRoot . '/sample.time',
            $this->temporaryRoot,
            0.1,
        );
        $pid = 0;
        try {
            try {
                $process->run();
            } catch (ProcessTimedOutException) {
                // A timeout exception alone does not establish that its child stopped.
            }
            self::assertMatchesRegularExpression('/^[1-9][0-9]*\n$/D', $process->getOutput());
            $pid = (int) trim($process->getOutput());
            self::assertNotSame(0, $process->getExitCode());
            $state = @file_get_contents('/proc/' . $pid . '/stat');
            if ($state !== false) {
                self::assertMatchesRegularExpression('/^[0-9]+ \(.+\) [ZX] /', $state, 'The timed child is still running.');
            }
        } finally {
            if ($pid > 0 && posix_kill($pid, 0)) {
                posix_kill($pid, 9);
            }
        }
    }

    public function testFailureArtifactsRetainTheEffectiveConsumerSetup(): void
    {
        $root = dirname(__DIR__, 2);
        $consumer = $this->temporaryRoot . '/consumer';
        $output = $this->temporaryRoot . '/results';
        (new Filesystem())->mkdir($consumer . '/vendor/bin');
        $configuration = "parameters:\n    level: max\n    yumemiApocrypha:\n        autoDetect: true\n";
        $manifest = '{"name":"example/stress-consumer"}';
        file_put_contents($consumer . '/phpstan-autodetect.neon', $configuration);
        file_put_contents($consumer . '/composer.json', $manifest);
        file_put_contents($consumer . '/composer.lock', '{}');
        file_put_contents($consumer . '/vendor/bin/phpstan', '<?php exit(0);');
        $process = new Process([PHP_BINARY, $root . '/benchmarks/stress/run.php', $consumer, $output], $root);
        $process->run();

        self::assertSame(1, $process->getExitCode(), $process->getOutput() . $process->getErrorOutput());
        self::assertStringContainsString('got exit 0', $process->getErrorOutput());
        self::assertFileExists($output . '/consumer-phpstan.neon');
        self::assertSame($configuration, file_get_contents($output . '/consumer-phpstan.neon'));
        self::assertSame($manifest, file_get_contents($output . '/consumer-composer.json'));
        $metadata = json_decode((string) file_get_contents($output . '/metadata.json'), true, flags: JSON_THROW_ON_ERROR);
        self::assertIsArray($metadata);
        self::assertIsArray($metadata['sourceSha256'] ?? null);
        foreach ([
            'benchmarks/stress/run',
            'tests/Consumer/run',
            'tests/Consumer/laravel-framework/phpstan-autodetect.neon',
            'tests/Consumer/laravel-framework/verify.php',
            'composer.json',
            'stubs/illuminate/cache.stub',
        ] as $path) {
            self::assertSame(hash_file('sha256', $root . '/' . $path), $metadata['sourceSha256'][$path] ?? null, $path);
        }
    }
}
