# Integrations

Apocrypha deliberately covers a small set of stable, useful, and unambiguous unit-bearing boundaries. The table below is
a record of verified behavior, not a promise to support every future or historical Laravel major indefinitely.

## Version Policy

All current integrations accept Laravel majors 11, 12, and 13. CI resolves the latest compatible release of each major
instead of pinning the snapshots below. An unknown future major is rejected until its signatures and semantics have been
reviewed.

The latest documented verification snapshots are `v11.51.0`, `v12.64.0`, and `v13.23.0`, checked on 2026-08-03.

## Illuminate Cache

Enable `illuminate/cache` to brand duration boundaries in cache contracts and repositories, stores, locks,
`RateLimiter`, and `RateLimiting\Limit`.

| API concern                             | Unit                           |
| --------------------------------------- | ------------------------------ |
| Cache TTLs, lock durations, rate limits | `second`                       |
| Lock retry sleep                        | `millisecond`                  |
| `Limit::perMinute()` and `perMinutes()` | `minute`                       |
| `Limit::perHour()`                      | `hour`                         |
| `Limit::perDay()`                       | `day`                          |
| Available/default cache time            | returned or stored as `second` |

<!-- yumemi-example: illuminate-cache-invalid -->

```php
<?php

use Illuminate\Cache\RateLimiter;

use function jbboehr\Yumemi\unit;

function recordCacheAttempt(RateLimiter $limiter): void
{
    $limiter->hit('report', unit(30, 'second'));
    $limiter->hit('report', unit(2, 'minute')); // PHPStan rejects minutes at this seconds boundary.
}
```

Plain integers are rejected at annotated boundaries. Existing `DateTimeInterface`, `DateInterval`, closure, and nullable
alternatives remain valid where Laravel accepts them, as do calls that omit an optional duration.

## Illuminate Cookie

Enable `illuminate/cookie` to require a `minute` value for the lifetime accepted by
`Illuminate\Contracts\Cookie\Factory::make()` and `CookieJar::make()`.

The variadic cookie queue API is not annotated because the same call may receive either a prebuilt cookie or the
positional arguments forwarded to `make()`. Absolute expiration timestamps also remain unbranded.

## Illuminate Filesystem

Enable `illuminate/filesystem` to brand file-size results and the byte counts returned by local `put()`, `prepend()`, and
`append()` operations as `byte` values. This covers the filesystem contract, local filesystem, filesystem adapter, and
`LockableFile` size APIs.

Last-modification values are timestamps rather than durations or byte counts and remain unbranded. Adapter write methods
that report only success or failure also remain ordinary booleans.

## Illuminate HTTP

Enable `illuminate/http` to brand request timeouts, retry delays, and fake-upload sizes.

| API concern                               | Unit          |
| ----------------------------------------- | ------------- |
| Request and connection timeout            | `second`      |
| Retry scalar, schedule, or callback delay | `millisecond` |
| Fake-upload size argument                 | `1024 * byte` |
| Fake-upload reported size                 | `byte`        |

Laravel's fake-upload API describes its input as kilobytes but computes it by multiplying the supplied value by 1024.
Apocrypha therefore uses the exact unit `1024 * byte`; the decimal UDUNITS `kilobyte` is not definitionally equivalent.

<!-- yumemi-example: illuminate-http-invalid -->

```php
<?php

use Illuminate\Http\Client\PendingRequest;

use function jbboehr\Yumemi\unit;

function configureRemoteArchive(PendingRequest $request): void
{
    $request->timeout(unit(30, 'second'));
    $request->timeout(unit(250, 'millisecond')); // PHPStan rejects milliseconds at this seconds boundary.
}
```

## Illuminate Support

Enable `illuminate/support` to brand framework timing helpers and benchmark results.

| API concern                         | Unit          |
| ----------------------------------- | ------------- |
| `Benchmark` measurements            | `millisecond` |
| `Sleep::sleep()` duration           | `second`      |
| `Sleep::usleep()` duration          | `microsecond` |
| `Timebox::call()` minimum duration  | `microsecond` |

The fluent `Sleep::for($value)->seconds()` form is not annotated because the unit is selected after the numeric value
crosses the first method boundary. The direct `sleep()` and `usleep()` entry points have unambiguous units.

## Illuminate Process

Enable `illuminate/process` to require `second` values for `PendingProcess::timeout()` and `idleTimeout()`.

Laravel 13 also accepts `CarbonInterval` at these boundaries. Apocrypha selects a major-specific stub so this alternative
remains valid on Laravel 13 without incorrectly allowing it on Laravel 11 or 12.

## Illuminate Queue

Enable `illuminate/queue` to brand delayed dispatches, job release delays, and physical `WorkerOptions` values.

| API concern                                      | Unit               |
| ------------------------------------------------ | ------------------ |
| Delayed dispatch and job release                 | `second`           |
| Worker backoff, timeout, sleep, rest, and lifetime | `second`         |
| Worker memory limit                              | `1048576 * byte`   |

The memory limit is measured in binary megabytes by Laravel's worker implementation, so Apocrypha uses the exact scale
`1048576 * byte`. Attempt counts, maximum jobs, and other dimensionless controls remain ordinary integers. Laravel 12
added `stopWhenEmptyFor`; major-specific worker stubs preserve the Laravel 11 constructor and property surface.

## Limitations

- Most stubs cover signatures shared by all verified majors. A major-specific stub is selected where a supported
  signature differs, as with Illuminate Process on Laravel 13.
- Native branded arguments are not converted. Dimensionally compatible but differently scaled units remain invalid.
- Dynamic or unsupported unit expressions lose Yumemi's precise brand according to the core extension's normal rules.
- Enabling Apocrypha enables Yumemi's parser-based optional-tag promotion, with the associated PHPStan upgrade and
  extension-conflict risk.

[Documentation index](./) · [Repository README](https://github.com/jbboehr/yumemi-apocrypha.php)
