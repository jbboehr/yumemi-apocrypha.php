# Generated PHPStan Stress Fixture

The stress fixture checks that unit diagnostics stay complete and analysis costs remain bounded as the number of
distinct call sites grows. It complements the small consumer fixtures and the fixed BookStack application benchmark.

## Run It

Install development dependencies, then run:

```shell
nix develop --command composer benchmark:stress
```

The command installs the committed PHP 8.2 / Laravel 12 / Larastan consumer lock into a temporary project, enables full
Apocrypha autodetection, and checks upstream signatures before measuring PHPStan. Package installation is outside the
timed region. It requires GNU time and GNU coreutils' timeout; it does not require a running Laravel application or
external services.

The Linux `phpstan-stress` Nix check runs the same workload with network access disabled and dependencies from the
consumer archive cache. Nix may reuse a successful cached result. For fresh measurements, rerun the Composer command
above; it runs all nine samples and uses a new local result directory by default.

`nix build --rebuild` compares rebuilt outputs with existing store paths. Retained timing and RSS measurements vary
between runs, so that comparison can fail even when all samples pass.

The Nix output retains the results. For local runs, artifacts remain beneath `.phpbench/stress/<run>/`; set
`CONSUMER_STRESS_OUTPUT` to select a different, new directory. Existing result directories are never overwritten.

## Workload And Diagnostic Contract

Each generated function represents checkout code that caches receipts and configures an HTTP timeout. It contains
accepted and rejected Cache calls through five entry forms: direct receiver, facade, helper, reordered named arguments,
and a finite unpacked array. HTTP timeout calls add a second boundary. An unrelated class with a `put()` method accepts
ordinary integers and branded minutes, guarding against accidental annotation by method name alone.

The generator emits distinct declarations and keys, so PHPStan must analyze new syntax nodes as the workload grows.
There is no runtime loop standing in for repeated source calls. Each group has six valid unit-bearing calls, six invalid
calls, and two unrelated controls:

| Groups | Boundary and control calls | Expected diagnostics |
| ------ | -------------------------- | -------------------- |
| 50     | 700                        | 300                  |
| 200    | 2,800                      | 1,200                |
| 800    | 11,200                     | 4,800                |

Every run requires exit status 1, empty stderr, no general PHPStan errors, and exactly one `apocrypha.unit` diagnostic
at each expected source line with the expected incompatible unit fragment. Missing, duplicate, misplaced, unrelated, and
extra diagnostics fail the check. Positive cases and unrelated controls must remain silent. Checking locations and
multiplicity prevents a lost diagnostic and a new false positive from cancelling each other in a count-only check.

## Measurement And Regression Limits

Three rounds rotate the size order. Each sample uses a fresh PHPStan temporary directory and `--debug`, disabling the
result cache and parallel analysis. CLI OPcache, PCOV, and Xdebug are disabled in the measured process. GNU time reports
whole-command elapsed seconds and operating-system peak RSS; these are not PHP allocator-only measurements. The measured
process unsets PHPStan's agent-detection environment flags so agent-specific help does not appear on stderr. PHP
warnings remain enabled and fail the empty-stderr guard.

The committed limits in `benchmarks/StressAssertions.php` require:

- Every sample to finish within 180 seconds and use no more than 768 MiB peak RSS. GNU timeout sends SIGKILL to a hung
  sample's process group after 185 seconds, including children in that group. GNU time runs outside that group so it can
  reap timeout and retain measurements. Symfony's separate timeout is disabled. PHPStan also has a 1 GiB PHP memory
  limit.
- Median wall time between adjacent sizes to remain below `2.5 × input growth × smaller median + 1 second`.
- Median peak RSS to remain below `2 × input growth × smaller median + 16 MiB`.

These generous relative limits target large scaling regressions while allowing startup costs and ordinary timing noise.
They are not latency promises for application projects. A failing limit needs investigation on a comparable machine; do
not raise it or remove calls merely to clear CI.

Initial calibration found that a single 2,000-group source file exceeded PHPStan's 1 GiB memory limit. The default
800-group ceiling keeps the regression workload within that budget. This is a bounded source-analysis model, not a
capacity guarantee for arbitrary project sizes, branch complexity, warm caches, or PHPStan parallel workers.

## Inspect And Replay

Each result directory preserves generated PHP, the expected line-to-unit map, PHP version, every command and exit
status, raw stdout/stderr, GNU time output, collected samples, and the final guard result when all samples complete.
Failed runs keep the artifacts available up to the failure, including process output and measurements after a timeout.
Each `.process.json` also records the explicit environment overrides; it does not capture the inherited environment.

The consumer's installed lock is retained as `consumer.lock`, its effective Composer manifest as
`consumer-composer.json`, and its PHPStan configuration as `consumer-phpstan.neon`. `metadata.json` records hashes of
these retained inputs and of the source, stubs, benchmark helpers, setup scripts, signature verifier, and repository
configuration. Hashes identify the checkout required for reconstruction; they do not retain its source files.

To reconstruct an individual sample:

1. Restore a checkout matching the recorded source hashes and use the recorded PHP version.
2. Create a fresh consumer directory with the retained manifest and lock renamed to `composer.json` and `composer.lock`.
   Link `.apocrypha-package` to that checkout as required by the manifest, then run `composer install` with development
   dependencies and the allowed extension-installer plugin. Copy `consumer-phpstan.neon` into the consumer as
   `phpstan-autodetect.neon`, copy the hashed `tests/Consumer/laravel-framework/verify.php` there, and run
   `php verify.php 12`.
3. Update the retained round configuration and command to use the new consumer, generated fixture, and result paths,
   with a fresh cache directory. Apply the recorded `environment` overrides from `.process.json`: `false` means to unset
   that variable for the process. Run the command from the new consumer directory.

The generated inputs are deterministic; directory names vary by run. Temporary consumer projects are removed afterward.
Recorded absolute commands and configuration includes therefore require rebasing, and dependency installation still
requires available package archives. The result directory is not a standalone runnable consumer.

Focused tests for generation and the diagnostic/resource guards run with:

```shell
vendor/bin/phpunit tests/Benchmarks --no-coverage
```

They include wrong-line and wrong-unit diagnostics with unchanged counts, duplicate reports, stderr warnings, process
failures and timeout cleanup, retained consumer inputs, quadratic growth, noisy samples, and individual resource-budget
violations. Keep these guard tests separate from the real Laravel run: synthetic reports verify the checker, while the
isolated consumer verifies the integration.
