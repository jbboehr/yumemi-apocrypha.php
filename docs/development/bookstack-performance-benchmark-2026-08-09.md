# BookStack Application Benchmark

This benchmark measures the analysis overhead of registering and activating Yumemi Apocrypha in the same pinned
BookStack application used by the [application smoke test](bookstack-application-smoke-test-2026-08-09.md). It is a
performance comparison, not another compatibility matrix: every run also verifies the established zero-, zero-, and
seven-diagnostic contracts so a faster incorrect analysis cannot be accepted as an improvement.

## Subject and Method

| Item                       | Value                                                                                                                                  |
| -------------------------- | -------------------------------------------------------------------------------------------------------------------------------------- |
| BookStack revision         | [`c813c1b3628c0b6bd757c12cadaa56f50724117d`](https://codeberg.org/BookStack/bookstack/commit/c813c1b3628c0b6bd757c12cadaa56f50724117d) |
| Apocrypha runtime revision | `9fc273a`                                                                                                                              |
| Yumemi revision            | `e9047bf` from `dev-master`                                                                                                            |
| Analysis stack             | PHP 8.2.33, Laravel 12.64.0, Larastan 3.10.0, PHPStan 2.2.8                                                                            |
| Measured runs              | Three, with one warm-up per scenario                                                                                                   |
| Result cache and workers   | Disabled through PHPStan `--debug`                                                                                                     |
| PHP CLI opcache            | Disabled                                                                                                                               |
| Measurement                | GNU `time`: elapsed wall time and maximum resident set size                                                                            |

The harness prepared one dependency tree containing Yumemi and a symlinked path installation of the Apocrypha working
copy. All three scenarios therefore used identical versions of PHP, PHPStan, Larastan, Laravel, and application
dependencies:

1. **baseline** loaded BookStack's ordinary Larastan configuration without Yumemi or Apocrypha;
2. **inert** additionally loaded Yumemi and Apocrypha with no selected or autodetected integrations;
3. **autodetect** enabled strict package autodetection across the complete applicable integration set.

Each scenario ran once in each execution position. This rotation reduces systematic advantage from filesystem warmth,
host load, or scenario order. [PHPStan's debug mode](https://phpstan.org/user-guide/command-line-usage#--debug) disables
both the result cache and parallel processing, so peak RSS describes one analyzer process and elapsed time emphasizes
extension work. These figures do not predict cached or parallel wall-clock behavior directly.

## Results

| Scenario   | Wall samples          | Median wall | Peak-RSS samples        | Median peak RSS |
| ---------- | --------------------- | ----------: | ----------------------- | --------------: |
| baseline   | 16.71, 17.15, 17.15 s |     17.15 s | 403.4, 403.6, 403.8 MiB |       403.6 MiB |
| inert      | 17.36, 17.42, 17.45 s |     17.42 s | 407.6, 407.7, 408.0 MiB |       407.7 MiB |
| autodetect | 18.10, 18.12, 18.34 s |     18.12 s | 409.6, 409.7, 409.9 MiB |       409.7 MiB |

Relative to baseline, inert registration added 0.27 seconds (1.6%) and 4.1 MiB (1.0%) at the median. Full autodetection
added 0.97 seconds (5.7%) and 6.1 MiB (1.5%) relative to baseline. The increment attributable to selecting and enforcing
the autodetected integrations over the already-loaded inert configuration was 0.70 seconds (4.0%) and 2.0 MiB (0.5%).

All baseline and inert runs passed without diagnostics. Every autodetection run returned the expected analysis failure
and the same seven cache-duration diagnostics recorded by the smoke test. No unrelated diagnostic appeared.

## Interpretation

Cold integration selection and cached selection access were too small in the PHPBench suite to explain the initial
result. At the time of that run, the stronger candidate was the metadata rule's application-wide call path. For every
ordinary method call, static call, or construction, `PackageIntegrationUnitBoundaryExtension::processCall()` mapped the
arguments, visited each metadata integration, checked whether its adapter was active, copied and sorted its complete
argument-boundary list, and then scanned for matching kind, method, version, and receiver information. The indexed
implementation measured afterward is described in the follow-up below.

The initial autodetection increment was consistent across execution positions and justified a targeted optimization:
index active argument boundaries by call kind and lowercased method name, filter version profiles once per configured
selection, and preserve the existing receiver checks. The follow-up below tests that change against the relevant
autodetect-versus-inert comparison while retaining the seven-diagnostic contract.

The smaller baseline-to-inert delta includes Yumemi's own PHPStan extension and optional tag-promotion parser as well as
Apocrypha's inactive services. This benchmark does not isolate those components, and the baseline sample spread is large
enough that no further conclusion should be drawn from the 1.6% figure alone.

## Indexed Call-Path Follow-up

A same-host six-round control immediately before indexing the metadata call path measured a 0.285-second (1.6%)
autodetection increment over inert registration. After the rule began indexing active, version-applicable argument
boundaries by call kind and lowercase method name, the same six-round benchmark measured a 0.050-second (0.3%)
increment:

| Revision state       | Baseline median | Inert median | Autodetect median | Autodetect over inert | Incremental peak RSS |
| -------------------- | --------------: | -----------: | ----------------: | --------------------: | -------------------: |
| Pre-index control    |        17.145 s |     18.215 s |          18.500 s |        0.285 s (1.6%) |              2.5 MiB |
| Indexed working tree |        16.810 s |     17.340 s |          17.390 s |        0.050 s (0.3%) |              1.7 MiB |

The indexed result reduces the measured incremental wall time by 82%. It also removes repeated argument mapping,
integration selection, version filtering, complete boundary-list sorting, and receiver checks from calls whose kind and
method name have no active boundary. Matching candidates retain the original catalog order within each integration, with
exact receiver classes stably preceding compatible interfaces and traits.

The pre-index control contained several host-load outliers: its baseline and inert ranges extended to 22.630 and 23.750
seconds, while the indexed run's ranges were 16.790–16.930 and 17.240–17.450 seconds. The conclusion therefore rests on
the scenario-relative autodetect-versus-inert increment rather than the absolute cross-run totals. All baseline and
inert analyses remained clean, and every autodetection analysis produced exactly the established seven diagnostics in
both runs.

The raw follow-up runs are stored locally beneath `.phpbench/bookstack/results/20260810T064626Z-48193` and
`.phpbench/bookstack/results/20260810T070637Z-59303`. These paths are intentionally ignored benchmark artifacts rather
than repository fixtures.

## Reproduction

Run the benchmark from this repository through the Nix development shell:

```console
nix develop --command composer benchmark:bookstack
```

The default performs one warm-up and six measured runs per scenario. The harness caches the pinned BookStack checkout
and dependency installation beneath `.phpbench/bookstack/`, rotates scenario order, validates diagnostics, and writes
raw logs, metadata, a tab-separated sample file, and a generated summary beneath `.phpbench/bookstack/results/`.
