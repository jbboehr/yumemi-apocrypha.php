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

use jbboehr\Yumemi\Apocrypha\Benchmarks\StressAssertions;
use jbboehr\Yumemi\Apocrypha\Benchmarks\StressFixture;
use jbboehr\Yumemi\Apocrypha\Benchmarks\StressProcess;

require __DIR__ . '/../../vendor/autoload.php';

$projectRoot = dirname(__DIR__, 2);
$consumer = isset($argv[1]) ? realpath($argv[1]) : false;
$output = $argv[2] ?? '';
if ($consumer === false || $output === '' || !is_file($consumer . '/vendor/bin/phpstan')) {
    fwrite(STDERR, "Usage: run.php <installed Laravel consumer> <new results directory>\n");
    exit(2);
}

$write = static function (string $path, string $contents): void {
    if (file_put_contents($path, $contents) === false) {
        throw new RuntimeException('Cannot write stress artifact: ' . $path);
    }
};

try {
    if (file_exists($output) || !mkdir($output, 0777, true)) {
        throw new RuntimeException('The stress results directory must be new: ' . $output);
    }
    $outputPath = realpath($output);
    if ($outputPath === false) {
        throw new RuntimeException('Cannot resolve stress results directory.');
    }
    $output = $outputPath;
    printf("Stress artifacts: %s\n", $output);

    $sourceHashes = [];
    foreach (['src', 'stubs'] as $directory) {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($projectRoot . '/' . $directory)) as $file) {
            if ($file instanceof SplFileInfo && $file->isFile()) {
                $sourceHashes[substr($file->getPathname(), strlen($projectRoot) + 1)] = hash_file('sha256', $file->getPathname());
            }
        }
    }
    foreach ([
        'apocrypha.neon',
        'extension.neon',
        'composer.json',
        'composer.lock',
        'flake.nix',
        'flake.lock',
        'benchmarks/StressAssertions.php',
        'benchmarks/StressFixture.php',
        'benchmarks/StressProcess.php',
        'benchmarks/stress/run',
        'benchmarks/stress/run.php',
        'tests/Consumer/run',
        'tests/Consumer/laravel-framework/composer.json',
        'tests/Consumer/laravel-framework/phpstan-autodetect.neon',
        'tests/Consumer/laravel-framework/verify.php',
    ] as $path) {
        $sourceHashes[$path] = hash_file('sha256', $projectRoot . '/' . $path);
    }
    ksort($sourceHashes);
    $consumerHashes = [];
    foreach ([
        'composer.lock' => 'consumer.lock',
        'composer.json' => 'consumer-composer.json',
        'phpstan-autodetect.neon' => 'consumer-phpstan.neon',
    ] as $source => $artifact) {
        if (!copy($consumer . '/' . $source, $output . '/' . $artifact)) {
            throw new RuntimeException('Cannot retain consumer input: ' . $source);
        }
        $consumerHashes[$artifact] = hash_file('sha256', $output . '/' . $artifact);
    }
    $write($output . '/metadata.json', json_encode([
        'php' => PHP_VERSION,
        'sizes' => StressAssertions::SIZES,
        'rounds' => StressAssertions::ROUNDS,
        'generatorSha256' => hash_file('sha256', __DIR__ . '/../StressFixture.php'),
        'sourceSha256' => $sourceHashes,
        'consumerSha256' => $consumerHashes,
        'maxSeconds' => StressAssertions::MAX_SECONDS,
        'maxRssKiB' => StressAssertions::MAX_RSS_KIB,
    ], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT) . "\n");
    $fixtures = [];
    $samples = [];
    foreach (StressAssertions::SIZES as $size) {
        $fixtures[$size] = StressFixture::generate($size);
        $samples[$size] = [];
        $write($output . '/fixture-' . $size . '.php', $fixtures[$size]['source']);
        $write($output . '/expected-' . $size . '.json', json_encode(
            $fixtures[$size]['expected'],
            JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT,
        ) . "\n");
    }

    for ($round = 0; $round < StressAssertions::ROUNDS; ++$round) {
        for ($position = 0; $position < count(StressAssertions::SIZES); ++$position) {
            $size = StressAssertions::SIZES[($round + $position) % count(StressAssertions::SIZES)];
            $stem = $output . '/round-' . ($round + 1) . '-groups-' . $size;
            $fixturePath = $output . '/fixture-' . $size . '.php';
            $configuration = "includes:\n    - " . json_encode($consumer . '/phpstan-autodetect.neon', JSON_THROW_ON_ERROR)
                . "\nparameters:\n    tmpDir: " . json_encode($stem . '-cache', JSON_THROW_ON_ERROR) . "\n";
            $write($stem . '.neon', $configuration);
            $process = StressProcess::create([
                PHP_BINARY, '-d', 'opcache.enable_cli=0', '-d', 'pcov.enabled=0', '-d', 'xdebug.mode=off',
                '-d', 'display_errors=stderr',
                $consumer . '/vendor/bin/phpstan', 'analyse', '--debug', '--no-progress', '--no-ansi',
                '--memory-limit=1G', '--error-format=json', '--configuration=' . $stem . '.neon', $fixturePath,
            ], $stem . '.time', $consumer, StressAssertions::MAX_SECONDS);
            try {
                $exitCode = $process->run();
            } finally {
                $write($stem . '.stdout', $process->getOutput());
                $write($stem . '.stderr', $process->getErrorOutput());
                $write($stem . '.process.json', json_encode([
                    'command' => $process->getCommandLine(),
                    'environment' => $process->getEnv(),
                    'exitCode' => $process->getExitCode(),
                ], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT) . "\n");
            }
            $errors = StressAssertions::diagnosticErrors(
                $process->getOutput(),
                $fixtures[$size]['expected'],
                $fixturePath,
                $exitCode,
                $process->getErrorOutput(),
            );
            if ($errors !== []) {
                throw new RuntimeException(implode("\n", $errors) . "\nSee " . $stem . '.stdout and .stderr');
            }
            $metrics = file_get_contents($stem . '.time');
            if ($metrics === false || preg_match('/^([0-9]+\.[0-9]+)\t([1-9][0-9]*)\s*$/D', $metrics, $match) !== 1) {
                throw new RuntimeException('GNU time did not report elapsed seconds and peak RSS.');
            }
            $sample = ['seconds' => (float) $match[1], 'rssKiB' => (int) $match[2]];
            $samples[$size][] = $sample;
            $write($output . '/samples.json', json_encode($samples, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT) . "\n");
            printf(
                "Round %d: %d groups, %d diagnostics, %.2f s, %.1f MiB peak RSS\n",
                $round + 1,
                $size,
                count($fixtures[$size]['expected']),
                $sample['seconds'],
                $sample['rssKiB'] / 1024,
            );
        }
    }
    $errors = StressAssertions::scalingErrors($samples);
    $write($output . '/result.json', json_encode(['errors' => $errors], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT) . "\n");
    if ($errors !== []) {
        throw new RuntimeException(implode("\n", $errors));
    }
    echo "Stress diagnostic and scaling checks passed.\n";
} catch (Throwable $exception) {
    fwrite(STDERR, $exception->getMessage() . "\nStress artifacts: " . $output . "\n");
    exit(1);
}
