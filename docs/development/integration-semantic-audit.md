# Integration Semantic Audit

This ledger records the source evidence and consumer coverage behind every branded third-party boundary. It was last
reviewed on 2026-08-15. The public supported-version matrix remains in
[`docs/pages/integrations.md`](../pages/integrations.md); this document is the maintainer-facing evidence behind it.

## Method

Each integration was checked against tagged upstream source, not inferred from a method name alone. `S` means the
consumer's `verify.php` checks that the class, method, parameter, property, or release profile exists in the installed
package. `V` means a branded value is accepted or an inferred return is asserted in `cases/valid*.php`. `I` means
`cases/invalid*.php` rejects a plain scalar, a differently scaled compatible unit, or a different dimension. `I-family`
means the boundary is exercised directly by `V`, while rejection is tested on another boundary with the identical
promoted type. Every integration runs explicit and autodetected registration; every Illuminate integration also runs
through the package-boundary adapter, whose core catalog is mechanically compared with its retained or selected
reference stub. Carbon, Illuminate Bus, Illuminate Database, Illuminate Routing, Measurements, and Intervention Image
always use the same adapter so their partial verification stubs cannot replace complete or more precise upstream
declarations.

The audit preserves upstream scalar alternatives and nullability, keeps dynamic result and option arrays open, and does
not narrow plain upstream integers from implementation behavior alone. It distinguishes relative durations from Unix
timestamps and records byte scales from the actual arithmetic rather than from friendly parameter names.

The 2026-08-14 re-audit reproduced all 138 committed consumer locks, ran the complete 38-check Linux flake matrix, and
compared the Laravel releases added since the previous source review. Those upstream diffs changed no branded boundary.
The audit did find that Composer's security policy selected `11.x-dev` after every tagged Laravel 11 framework release
entered an advisory range. Consumer fixtures now whitelist those known advisories for static-analysis testing, pin
`v11.55.1`, and reject any non-tagged `laravel/framework` version in committed locks.

The 2026-08-15 Auth audit added 16 committed profiles around the component's cache, timebox, prefix, and hash-key
cutovers. The Console audit then added eight profiles for the shared scheduler surface and its Laravel 13.2 signature
cutover. The Bus audit added eight profiles across Laravel 11–13 and both sides of its Laravel 12.52 progress-range
cutover. The resulting 170-lock, 41-check Linux flake matrix covers all three integrations.

## Verified Profiles

| Package                       | Profiles and source snapshots                                          | Profile evidence                                                                                                               |
| ----------------------------- | ---------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------ |
| Carbon                        | 2.62.1–2.x; 3.0–3.1 `Real`; 3.2+ `UTC`                                 | `2.62.1`, `2.73.0`, `3.0.0`, `3.1.1`, `3.2.0`, `3.13.2`                                                                        |
| Guzzle                        | 7 through 7.10 integer request delay; 7.11+ numeric request delay; 8   | `7.0.0`, `7.10.0`, `7.11.0`, `7.15.3`, `8.0.0`, `8.0.2`                                                                        |
| getID3                        | 1.9.22+ global class; 2.0.0-beta6+ namespaced class                    | `1.9.22`, `1.9.25`, `2.0.0-beta6`                                                                                              |
| Illuminate Auth               | 11–13, with cache, timebox, prefix, and hash-key cutovers              | `11.30.0`, `11.31.0`, `11.44.0`, `11.45.0`, `12.13.0`, `12.14.0`, `12.19.3`, `12.20.0`, `12.44.0`, `12.45.0`, and latest 11–13 |
| Illuminate Bus                | 11–13 delays and progress; ranged progress from Laravel 12.52          | `11.51.0`, `11.55.1`, `12.51.0`, `12.52.0`, `12.66.0`, `13.25.0`                                                               |
| Illuminate Console            | 11–13 shared scheduler units; Laravel 13.2 adds an unrelated flag      | `11.51.0`, `11.55.1`, `12.66.0`, `13.1.1`, `13.2.0`, `13.25.0`                                                                 |
| Illuminate HTTP               | 11 through 11.35.0 integer timeout; 11.35.1+ and 12–13 numeric timeout | `11.35.0`, `11.35.1`, `11.51.0`, `12.66.0`, `13.25.0`                                                                          |
| Illuminate Process            | 11–12 integer timeout; 13 accepts `CarbonInterval` or integer          | `11.0.0`, `11.51.0`, `12.0.0`, `12.66.0`, `13.0.0`, `13.25.0`                                                                  |
| Illuminate Database           | 11–13 query timings; query timeout from 12.51.0                        | `11.0.0`, `11.51.0`, `12.0.0`, `12.50.0`, `12.51.0`, `12.66.0`, `13.0.0`, `13.25.0`                                            |
| Illuminate Queue              | original worker; `stopWhenEmptyFor` from 11.53.0, 12.60.0, and 13.10.0 | adjacent releases at all three cutovers plus latest 11–13                                                                      |
| Illuminate Redis              | 11–13 shared limiter and command-event profile                         | `11.0.0`, `11.51.0`, `12.0.0`, `12.66.0`, `13.0.0`, `13.25.0`                                                                  |
| Illuminate Routing            | 11–13 shared throttle, route-lock, and signed-URL profile              | `11.0.0`, `11.51.0`, `12.0.0`, `12.66.0`, `13.0.0`, `13.25.0`                                                                  |
| Illuminate Session            | 11–13 shared handler and lock-lifetime profile                         | `11.0.0`, `11.51.0`, `12.0.0`, `12.66.0`, `13.0.0`, `13.25.0`                                                                  |
| Illuminate Validation         | 11–13 shared fluent-rule profile                                       | `11.0.0`, `11.51.0`, `12.0.0`, `12.66.0`, `13.0.0`, `13.25.0`                                                                  |
| Other Illuminate integrations | 11–13                                                                  | initial and current tagged releases of each major                                                                              |
| Laravel framework replacement | 11–13                                                                  | `11.55.1`, `12.66.0`, `13.25.0`                                                                                                |
| Intervention Image            | 3–4                                                                    | `3.0.0`, `3.11.8`, `4.0.0`, `4.2.1`                                                                                            |
| Measurements                  | 1.4+ `Length` and `Duration` magic factories                           | `v1.4.0`                                                                                                                       |
| phpgeo                        | 4–6                                                                    | `4.0.0`, `4.2.1`, `5.0.0`, `6.0.0`, `6.0.4`                                                                                    |
| Symfony HttpFoundation        | 6.4+ base; 7.3+ SSE; 8+ IP byte counts                                 | `6.4.0`, `6.4.43`, `7.0.0`, `7.2.9`, `7.3.0`, `7.4.16`, `8.0.0`, `8.1.4`                                                       |
| Symfony Stopwatch             | 6–8                                                                    | `6.0.0`, `6.4.24`, `7.0.0`, `7.4.8`, `8.0.0`, `8.1.0`                                                                          |

The source links below point to a representative current snapshot. The profile table names the additional tags checked
for signature changes.

## Carbon

> **Application-audit resolution:** the [BookStack smoke test](bookstack-application-smoke-test-2026-08-09.md) showed
> that a partial Carbon 3 `CarbonInterface` stub could hide unrelated valid methods after a branded call. Carbon now
> always uses the metadata adapter, leaving upstream declarations authoritative. The consumer chains a branded
> adjustment into an unrelated calendar method and verifies that concrete `CarbonImmutable` precision is preserved.

Evidence: Carbon's
[`Difference` trait](https://github.com/briannesbitt/Carbon/blob/3.13.2/src/Carbon/Traits/Difference.php),
[`Date` trait](https://github.com/briannesbitt/Carbon/blob/3.13.2/src/Carbon/Traits/Date.php), and
[`CarbonInterface`](https://github.com/briannesbitt/Carbon/blob/3.13.2/src/Carbon/CarbonInterface.php), plus the
corresponding files at every profile boundary.

| Boundaries                                                                                                               | Promoted type                                                      | Coverage     |
| ------------------------------------------------------------------------------------------------------------------------ | ------------------------------------------------------------------ | ------------ |
| Carbon 2 `diffInRealMicroseconds`, `diffInRealMilliseconds`, `diffInRealSeconds`, `diffInRealMinutes`, `diffInRealHours` | integer microseconds through hours                                 | S/V/I-family |
| Carbon 2 `floatDiffInRealSeconds`, `floatDiffInRealMinutes`, `floatDiffInRealHours`                                      | float seconds through hours                                        | S/V/I-family |
| Carbon 2 `addReal*` and `subReal*` fixed-time adjustments                                                                | integer unit matching the suffix                                   | S/V/I        |
| Carbon 3 `diffInMicroseconds` through `diffInHours`, `secondsSinceMidnight`, `secondsUntilEndOfDay`                      | float unit matching the method                                     | S/V/I-family |
| Carbon 3 `sleep()` on `CarbonInterface`, `Carbon`, `CarbonImmutable`, `FactoryImmutable`, and `WrapperClock`             | integer or float seconds                                           | S/V/I        |
| Carbon 3.0–3.1 `addReal*`, `subReal*`; 3.2+ `diffInUTC*`, `addUTC*`, `subUTC*`                                           | float returns and integer-or-float adjustments matching the suffix | S/V/I        |

Calendar-relative days, weeks, months, and years, timezone offsets, and timestamps remain unbranded. The profile tests
also confirm that the inactive `Real` or `UTC` family is not manufactured by the adapter.

## Guzzle

Evidence: [`RequestOptions`](https://github.com/guzzle/guzzle/blob/8.0.2/src/RequestOptions.php),
[`Client`](https://github.com/guzzle/guzzle/blob/8.0.2/src/Client.php),
[`Middleware`](https://github.com/guzzle/guzzle/blob/8.0.2/src/Middleware.php), and
[`TransferStats`](https://github.com/guzzle/guzzle/blob/8.0.2/src/TransferStats.php), checked against both major
profiles.

| Boundaries                                                                                                           | Promoted type                 | Coverage |
| -------------------------------------------------------------------------------------------------------------------- | ----------------------------- | -------- |
| Open request-option keys `connect_timeout`, `read_timeout`, `timeout` on `Client` and `ClientInterface` entry points | integer or float seconds      | S/V/I    |
| Open request-option key `delay` through Guzzle 7.10                                                                  | integer milliseconds          | S/V/I    |
| The same request-option key from Guzzle 7.11 onward                                                                  | integer or float milliseconds | S/V/I    |
| `Middleware::retry()` delay callback result                                                                          | integer milliseconds          | S/V/I    |
| Open request-option key `expect`                                                                                     | `bool` or integer bytes       | S/V/I    |
| Open request-option key `progress` callback parameters                                                               | four integer byte counts      | S/V/I    |
| `TransferStats::__construct($transferTime)` and `getTransferTime()`                                                  | nullable float seconds        | S/V/I    |

The request shape deliberately ends in `...`; unrelated Guzzle options are not sealed out. Guzzle 7 and 8 retain their
different retry callback signatures.

## getID3

Evidence: the 1.x [`getID3` analyzer](https://github.com/JamesHeinrich/getID3/blob/1.9.25/getid3/getid3.php) and
[`structure reference`](https://github.com/JamesHeinrich/getID3/blob/1.9.25/structure.txt), and the 2.x
[`GetID3` analyzer](https://github.com/JamesHeinrich/getID3/blob/2.0.0-beta6/src/GetID3.php) and
[`structure reference`](https://github.com/JamesHeinrich/getID3/blob/2.0.0-beta6/docs/Structure.md), together with
format modules that calculate the published fields.

| Boundaries                                                   | Promoted type                                                            | Coverage     |
| ------------------------------------------------------------ | ------------------------------------------------------------------------ | ------------ |
| `openfile($filesize)` and `analyze($filesize)`               | nullable integer bytes                                                   | S/V/I        |
| Open result keys `filesize`, `avdataoffset`, `avdataend`     | optional integer bytes                                                   | S/V/I-family |
| Open result key `playtime_seconds`                           | optional integer or float seconds                                        | S/V/I        |
| Open result keys `bitrate`, `audio.bitrate`, `video.bitrate` | optional integer or float bits per second; audio also preserves `'free'` | S/V/I-family |
| Open result keys `audio.sample_rate`, `video.frame_rate`     | optional integer or float hertz                                          | S/V/I-family |
| Open result keys `video.resolution_x`, `video.resolution_y`  | optional integer nominal raster pixels                                   | S/V/I-family |

The top-level, audio, and video shapes remain open because format modules add keys dynamically. All branded keys remain
optional. The upstream structure reference defines the standardized video dimensions as integers measured in pixels;
Yumemi's `pixel` is the nominal raster-sample dimension rather than the physical-length `css_pixel` unit. Other
format-specific image dimensions remain unbranded.

## Illuminate Auth

Evidence: Laravel's
[`SessionGuard`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Auth/SessionGuard.php),
[`PasswordBroker`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Auth/Passwords/PasswordBroker.php),
[`RequirePassword`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Auth/Middleware/RequirePassword.php),
and the
[`database`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Auth/Passwords/DatabaseTokenRepository.php)
and [`cache`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Auth/Passwords/CacheTokenRepository.php)
token repositories. Adjacent tags were checked at CacheTokenRepository's Laravel 11.31.0 introduction, the timebox
additions in 11.45.0 and 12.14.0, the cache-prefix removal in 12.20.0, and SessionGuard's 12.45.0 hash-key addition.

| Boundaries                                                       | Promoted type                                               | Coverage |
| ---------------------------------------------------------------- | ----------------------------------------------------------- | -------- |
| `SessionGuard::setRememberDuration()`                            | integer minutes                                             | S/V/I    |
| `SessionGuard` and `PasswordBroker` constructor timeboxes        | integer microseconds                                        | S/V/I    |
| `RequirePassword` constructor, `using()`, and `handle()` timeout | integer seconds, preserving published string and null arms  | S/V/I    |
| `DatabaseTokenRepository` expiry                                 | integer minutes in Laravel 11; integer seconds in 12 and 13 | S/V/I    |
| Database and cache token-repository throttle                     | integer seconds                                             | S/V/I    |
| `CacheTokenRepository` expiry                                    | integer seconds                                             | S/V/I    |

Laravel 11 multiplies the database repository's expiry by 60 before storing it; Laravel 12 and 13 store the supplied
seconds directly. Cache expiry and both throttle parameters are already second-valued. Apocrypha brands these native
boundaries but performs no conversion. Profile reflection verifies the constructor changes and the Laravel 11 runtime
multiplication. Plain consumers exercise the retained upstream signatures at each cutover; Larastan consumers for every
supported major disable the complete Auth stub set and reproduce only these unit boundaries through metadata.

## Illuminate Bus

Evidence: Laravel's [`Queueable`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Bus/Queueable.php)
trait and [`Batch`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Bus/Batch.php), checked at the
current releases of Laravel 11–13 and immediately before and after Laravel 12.52.0.

| Boundaries                         | Promoted type                                                                | Coverage |
| ---------------------------------- | ---------------------------------------------------------------------------- | -------- |
| `Queueable::$delay` and `delay()`  | integer seconds; date-time, interval, array, and null alternatives preserved | S/V/I    |
| `Batch::progress()` before 12.52.0 | integer percent                                                              | S/V/I    |
| `Batch::progress()` from 12.52.0   | `int<0, 100>` percent                                                        | S/V/I    |

`Queueable::delay()` assigns its argument directly to the public property; the scalar integer is documented as seconds.
The array alternative remains open because upstream publishes no element shape. `Batch::progress()` calculates a
percentage by multiplying the processed fraction by 100. Laravel 12.52 casts that result and publishes its integer
range. Earlier versions declare an integer but return `round()`'s float for nonzero totals; Apocrypha preserves the
published scalar type and records the runtime mismatch rather than silently widening the upstream signature.

Bus always uses the metadata adapter because a trait stub does not propagate its annotations to arbitrary consuming job
classes. The adapter explicitly matches trait use and intersects the progress unit with Laravel's selected return type.
Retained stubs cover both sides of the 12.52 cutover and remain in exact metadata parity. Total, pending, failed, and
processed job counts remain unbranded dimensionless counts.

## Illuminate Cache and Cookie

Evidence: Laravel's
[`Repository`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Cache/Repository.php),
[`RateLimiter`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Cache/RateLimiter.php),
[`Lock`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Cache/Lock.php),
[`Limit`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Cache/RateLimiting/Limit.php), and
[`CookieJar`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Cookie/CookieJar.php), checked at the
initial and current release of Laravel 11–13.

| Boundaries                                                                                                                           | Promoted type                                                                             | Coverage     |
| ------------------------------------------------------------------------------------------------------------------------------------ | ----------------------------------------------------------------------------------------- | ------------ |
| Cache contract/repository `put`, `add`, `remember`, `set`, `putMany`, `setMultiple`; store `put`, `putMany`                          | integer seconds with each upstream date, interval, closure, or null alternative preserved | S/V/I-family |
| Cache lock provider/contract/concrete constructor and `block`                                                                        | integer seconds                                                                           | S/V/I-family |
| `Lock::betweenBlockedAttemptsSleepFor()`                                                                                             | integer milliseconds                                                                      | S/V/I        |
| `Repository::getDefaultCacheTime()`, `setDefaultCacheTime()`; `RateLimiter::attempt`, `hit`, `increment`, `decrement`, `availableIn` | integer seconds with upstream alternatives preserved                                      | S/V/I-family |
| `Limit::$decaySeconds`, constructor, and `perSecond`; `perMinute(s)`, `perHour`, `perDay` inputs                                     | stored seconds; factory inputs use the unit named by the method                           | S/V/I-family |
| Cookie factory contract and `CookieJar::make($minutes)`                                                                              | integer minutes                                                                           | S/V/I        |

Cookie expiration timestamps and the variadic queue API remain unbranded.

## Illuminate Console

Evidence: Laravel's scheduler
[`ManagesAttributes`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Console/Scheduling/ManagesAttributes.php)
and
[`ManagesFrequencies`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Console/Scheduling/ManagesFrequencies.php)
traits, checked at the current releases of Laravel 11–13.

| Boundaries                                  | Promoted type            | Coverage |
| ------------------------------------------- | ------------------------ | -------- |
| `Event::$repeatSeconds`                     | nullable integer seconds | S/V/I    |
| `Event::$expiresAt`, `withoutOverlapping()` | integer minutes          | S/V/I    |

The named sub-minute frequency methods store their second interval in `$repeatSeconds`; the protected numeric helper is
not a public input boundary. `withoutOverlapping()` stores its argument in `$expiresAt`, which Laravel documents and
uses as the cache-lock lifetime in minutes. Laravel 13.2 adds an unrelated termination-signal flag to that method, so
the standalone reference stubs preserve the signatures immediately before and after that cutover. Larastan profiles
suppress those stubs and reproduce only the same unit boundaries through metadata. Cron segments, exit codes, and
attempt counts remain unbranded.

## Illuminate Database

Evidence: Laravel's
[`Connection`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Database/Connection.php),
[`QueryExecuted`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Database/Events/QueryExecuted.php),
and query [`Builder`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Database/Query/Builder.php). The
timing surface was checked at the initial and current releases of Laravel 11–13. The query-timeout cutover was checked
on both sides at Laravel 12.50.0 and 12.51.0.

| Boundaries                                 | Promoted type                                                        | Coverage     |
| ------------------------------------------ | -------------------------------------------------------------------- | ------------ |
| `Connection::logQuery()`                   | nullable input milliseconds                                          | S/V/I-family |
| `Connection::whenQueryingForLongerThan()`  | integer or float milliseconds, preserving date/interval alternatives | S/V/I-family |
| `Connection::totalQueryDuration()`         | float milliseconds                                                   | S/V/I-family |
| `QueryExecuted::__construct()` and `$time` | nullable input or stored milliseconds                                | S/V/I        |
| Query `Builder::timeout()` and `$timeout`  | nullable integer seconds from Laravel 12.51.0                        | S/V/I-family |

The framework calculates elapsed query time by multiplying seconds from `microtime(true)` by 1000. Query Builder's
timeout is documented and forwarded as seconds. The active metadata adapter leaves the installed Laravel declarations
authoritative: callback and event-constructor PHPDoc changed within the supported majors, so even a unit-only partial
stub could erase more precise upstream types. Retained stubs exist only for metadata parity and are never loaded during
consumer analysis. Query-log array entries remain unbranded because upstream publishes an open array rather than a fixed
measurement-bearing result shape.

## Illuminate Filesystem and HTTP

Evidence: Laravel's
[`Filesystem`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Filesystem/Filesystem.php),
[`PendingRequest`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Http/Client/PendingRequest.php),
and fake-upload [`File`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Http/Testing/File.php) and
[`FileFactory`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Http/Testing/FileFactory.php).

| Boundaries                                                                  | Promoted type                                                                    | Coverage     |
| --------------------------------------------------------------------------- | -------------------------------------------------------------------------------- | ------------ |
| Filesystem contract, local filesystem, adapter, and `LockableFile` `size()` | integer bytes                                                                    | S/V/I-family |
| Local filesystem `put`, `prepend`, `append`                                 | integer bytes, with `put` preserving `false`                                     | S/V/I-family |
| `PendingRequest::timeout()` and `connectTimeout()` through Laravel 11.35.0  | integer seconds                                                                  | S/V/I        |
| The same timeout methods from Laravel 11.35.1 and in 12–13                  | integer or float seconds                                                         | S/V/I        |
| `PendingRequest::retry()` schedule elements and scalar/callback sleep       | integer milliseconds; retry count remains dimensionless                          | S/V/I        |
| `FileFactory::create`, `File::create`, and `File::size` numeric input       | integer `1024 * byte`, preserving the string-content alternative where published | S/V/I        |
| `File::getSize()`                                                           | integer bytes                                                                    | S/V/I        |

The fake-upload input scale follows Laravel's multiplication by 1024, not decimal `kilobyte`. Filesystem modification
times remain unbranded timestamps.

## Intervention Image

Evidence: Intervention Image's `ImageManager`, `Image`, and `ImageInterface` at
[`3.0.0`](https://github.com/Intervention/image/tree/3.0.0/src),
[`3.11.8`](https://github.com/Intervention/image/tree/3.11.8/src),
[`4.0.0`](https://github.com/Intervention/image/tree/4.0.0/src), and
[`4.2.1`](https://github.com/Intervention/image/tree/4.2.1/src). Runtime checks create and resize a GD image at the
minimum and current release of each supported major.

| Boundaries                                                                  | Promoted type                                                  | Coverage     |
| --------------------------------------------------------------------------- | -------------------------------------------------------------- | ------------ |
| Manager `create()` in 3.x and `createImage()` in 4.x                        | integer pixels for width and height                            | S/V/I        |
| `ImageInterface::width()` and `height()`                                    | integer pixels                                                 | S/V/I        |
| Pixelation, resize, scale, cover, canvas, contain, and crop dimensions      | integer pixels; 4.x preserves each published `Fraction` option | S/V/I-family |
| Text, crop, placement/insertion, fill, pixel, and 3.x primitive coordinates | signed integer pixels                                          | S/V/I-family |
| `ImageInterface::rotate()`                                                  | float degrees                                                  | S/V/I        |

The active metadata adapter preserves the complete upstream interface and concrete receiver precision. Version-specific
reference stubs exist only for metadata parity and are never loaded during consumer analysis. Upstream plain integers
remain unbounded; implementation checks do not justify publishing a narrower type. Pixel values use Yumemi's nominal
raster-sample unit rather than `css_pixel`. Resolution units depend on object state, and opacity, transparency, quality,
and lower-level geometry objects remain unbranded.

## Illuminate Process, Queue, Redis, and Support

Evidence: Laravel's
[`PendingProcess`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Process/PendingProcess.php),
[`WorkerOptions`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Queue/WorkerOptions.php), Redis's
[`DurationLimiterBuilder`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Redis/Limiters/DurationLimiterBuilder.php),
[`ConcurrencyLimiterBuilder`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Redis/Limiters/ConcurrencyLimiterBuilder.php),
concrete limiters, and
[`CommandExecuted`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Redis/Events/CommandExecuted.php),
[`Benchmark`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Support/Benchmark.php),
[`Sleep`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Support/Sleep.php), and
[`Timebox`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Support/Timebox.php).

| Boundaries                                                                     | Promoted type                                                                     | Coverage     |
| ------------------------------------------------------------------------------ | --------------------------------------------------------------------------------- | ------------ |
| Process `timeout` and `idleTimeout`                                            | integer seconds; Laravel 13 also preserves `CarbonInterval`                       | S/V/I        |
| Queue contract `later`, `laterOn`; job contract/concrete/trait `release`       | integer seconds with date and interval alternatives where upstream publishes them | S/V/I-family |
| `WorkerOptions::$backoff` and constructor input                                | integer seconds or array of integer seconds                                       | S/V/I-family |
| Worker `timeout`, `sleep`, `rest`, `maxTime` properties and constructor inputs | integer seconds                                                                   | S/V/I-family |
| Worker `memory` property and constructor input                                 | integer `1048576 * byte`                                                          | S/V/I        |
| Worker `stopWhenEmptyFor` property and constructor input                       | integer seconds from 11.53.0, 12.60.0, and 13.10.0 only                           | S/V/I-family |
| Redis duration decay, concurrency release, and blocking timeouts               | integer seconds                                                                   | S/V/I-family |
| Redis limiter polling sleeps                                                   | integer milliseconds                                                              | S/V/I-family |
| Redis `CommandExecuted::$time` and constructor latency                         | float milliseconds; constructor preserves upstream `null`                         | S/V/I        |
| `Benchmark::measure()` and `value()` elapsed results                           | float milliseconds, preserving array keys and tuple value type                    | S/V/I-family |
| `Sleep::sleep()`; `Sleep::usleep()`                                            | integer or float seconds; integer microseconds                                    | S/V/I        |
| `Timebox::call($microseconds)`                                                 | integer microseconds                                                              | S/V/I        |

Worker memory is compared after division by 1024 twice, which establishes the binary-megabyte scale. Redis's absolute
limiter-expiry timestamp remains unbranded. Attempt counts, job counts, lock counts, and retry counts remain unbranded.

## Illuminate Routing

Evidence: Laravel's [`Route`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Routing/Route.php),
[`ThrottleRequests`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Routing/Middleware/ThrottleRequests.php),
[`UrlGenerator`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Routing/UrlGenerator.php), and
[`Redirector`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Routing/Redirector.php), checked at the
initial and current snapshots of Laravel 11 through 13.

| Boundaries                                         | Promoted type                   | Coverage     |
| -------------------------------------------------- | ------------------------------- | ------------ |
| `ThrottleRequests::with()` decay                   | integer minutes                 | S/V/I        |
| `ThrottleRequests::handle()` decay                 | integer or float minutes        | S/V/I        |
| `Route::block()` lock and wait                     | nullable integer seconds        | S/V/I        |
| `Route::locksFor()` and `waitsFor()`               | nullable integer seconds        | S/V/I        |
| Generator, contract, redirector, and facade expiry | interval, date-time, or seconds | S/V/I-family |

Throttle middleware multiplies the decay value by 60 before passing seconds to the rate limiter. Route lock fields are
documented and consumed as seconds. Signed-URL generation passes integer expirations through `InteractsWithTime`, where
integers are relative seconds; `DateTimeInterface` remains the absolute-time alternative. Routing always uses the
metadata adapter so its partial reference stub cannot replace the complete upstream `Route` surface, and so Larastan's
additional `Route` PHPDoc remains authoritative when installed. Attempt counts, HTTP status codes, and absolute Unix
timestamps remain unbranded.

## Illuminate Session

Evidence: Laravel's
[`ArraySessionHandler`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Session/ArraySessionHandler.php),
[`CacheBasedSessionHandler`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Session/CacheBasedSessionHandler.php),
[`CookieSessionHandler`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Session/CookieSessionHandler.php),
[`DatabaseSessionHandler`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Session/DatabaseSessionHandler.php),
[`FileSessionHandler`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Session/FileSessionHandler.php),
[`SessionManager`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Session/SessionManager.php), and
[`SymfonySessionDecorator`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Session/SymfonySessionDecorator.php),
checked at the initial and current snapshots of Laravel 11 through 13.

| Boundaries                                                                 | Promoted type            | Coverage     |
| -------------------------------------------------------------------------- | ------------------------ | ------------ |
| Array, cache, cookie, database, and file handler constructor lifetime      | integer minutes          | S/V/I        |
| `gc()` on all six concrete session handlers                                | integer seconds          | S/V/I-family |
| `SessionManager` route-block lock and wait defaults                        | integer seconds          | S/V/I        |
| `SymfonySessionDecorator::invalidate()` and `migrate()` lifetime arguments | nullable integer seconds | S/V/I        |

Every stateful handler stores its constructor lifetime as minutes; Array and Cookie multiply it by 60, Database and File
use minute-aware date arithmetic, and Cache multiplies it by 60 before writing. Laravel's session middleware converts
the configured lifetime to seconds before calling the concrete handler's `gc()` method. The two manager defaults are
documented and configured as seconds. The decorator preserves the nullable lifetime signature of Symfony's
`SessionInterface`, even though Laravel's underlying store currently ignores the argument. Absolute session activity and
expiration timestamps, lock-owner identifiers, attempt counts, and garbage-collection lottery odds remain unbranded.

## Illuminate Validation

Evidence: Laravel's
[`Dimensions`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Validation/Rules/Dimensions.php),
[`File`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Validation/Rules/File.php), and
[`ImageFile`](https://github.com/laravel/framework/blob/v13.25.0/src/Illuminate/Validation/Rules/ImageFile.php), checked
at the initial and current snapshots of Laravel 11 through 13. The overlap audit also checked Larastan 3's validation
stub and `jbboehr/phpstan-laravel-validation` at development commit `93a4d0d`.

| Boundaries                                                        | Promoted type                                   | Coverage     |
| ----------------------------------------------------------------- | ----------------------------------------------- | ------------ |
| `Dimensions` width, height, and minimum/maximum dimension methods | integer nominal raster pixels                   | S/V/I-family |
| `File::size`, `between`, `min`, and `max` integer alternatives    | integer `1024 * byte`, preserving string inputs | S/V/I        |

Laravel's file validator compares the uploaded byte count after division by 1024, establishing the integer input's
binary scale. String size expressions remain an upstream-parsed alternative and are not branded. Ratios, general
constraint arrays, and rule-object inputs remain unbranded. `ImageFile` inherits the annotated `File` methods.

Larastan's validation stub currently owns `Validator::safe()`, while `jbboehr/phpstan-laravel-validation` owns
validation-result inference and `Validator::setRules()`. Neither declares the fluent rule methods above. The isolated
consumer installs all three extensions together; Apocrypha uses its whole-integration metadata adapter whenever Larastan
is present, leaving both existing extensions' declarations authoritative.

## Measurements

Evidence: Measurements [`Length`](https://github.com/marfurt/measurements/blob/v1.4.0/src/Quantities/Length.php),
[`Duration`](https://github.com/marfurt/measurements/blob/v1.4.0/src/Quantities/Duration.php), and
[`Measurement::__callStatic()`](https://github.com/marfurt/measurements/blob/v1.4.0/src/Measurement.php), together with
the corresponding `UnitLength` and `UnitDuration` factory implementations.

| Boundaries                                                           | Promoted type                   | Coverage |
| -------------------------------------------------------------------- | ------------------------------- | -------- |
| All 21 `Length` magic factories, from `megameters` through `parsecs` | float unit named by the factory | S/V/I    |
| `Duration::seconds`, `minutes`, and `hours` magic factories          | float second, minute, or hour   | S/V/I    |

The active metadata adapter leaves the upstream class PHPDoc authoritative, preventing a partial stub from hiding
unrelated or newly added magic factories. A retained reference stub reproduces every verified `@method` declaration on
the two selected classes for review. Constructors, `value()`, scalar arithmetic, and conversion remain unbranded because
their unit depends on object state. Other quantity classes remain untouched. Absolute-temperature factories are excluded
because their inputs are coordinate points and Yumemi's native scalar brands currently represent only multiplicative
quantities.

## phpgeo

Evidence: phpgeo's distance, bearing, geometry, factory, utility, and polyline processor sources at
[`6.0.4`](https://github.com/mjaschen/phpgeo/tree/6.0.4/src), checked against the equivalent declarations in majors 4
and 5. Runtime consumer checks independently confirm an equatorial degree is about 111.2 km and east is 90 degrees.

| Boundaries                                                                                                                    | Promoted type                  | Coverage     |
| ----------------------------------------------------------------------------------------------------------------------------- | ------------------------------ | ------------ |
| Distance interface, `Haversine`, `Vincenty`, and `Coordinate::getDistance()` returns                                          | float meters                   | S/V/I-family |
| `Coordinate::hasSameLocation`, polyline uniqueness/containment, point-to-line epsilon, simplifier tolerance, bounds expansion | float meters                   | S/V/I-family |
| Line/polyline length, polygon perimeter, perpendicular and point-to-line distance returns                                     | float meters                   | S/V/I-family |
| Polygon area                                                                                                                  | float square meters            | S/V/I        |
| Line and bearing calculator bearing/final-bearing returns                                                                     | float degrees                  | S/V/I-family |
| Spherical/ellipsoidal destination bearing and distance inputs; ellipsoidal final-bearing return                               | float degrees and float meters | S/V/I        |
| Cardinal north/east/south/west setters and getters                                                                            | float meters                   | S/V/I-family |

Latitude and longitude remain unbranded because origin and range are part of their meaning. Bearing ranges remain
unexpressed until branded float ranges exist.

## Symfony HttpFoundation and Stopwatch

Evidence: HttpFoundation's [`Response`](https://github.com/symfony/http-foundation/blob/v8.1.4/Response.php),
[`Cookie`](https://github.com/symfony/http-foundation/blob/v8.1.4/Cookie.php),
[`UploadedFile`](https://github.com/symfony/http-foundation/blob/v8.1.4/File/UploadedFile.php), SSE classes, session
classes, and [`IpUtils`](https://github.com/symfony/http-foundation/blob/v8.1.4/IpUtils.php); Stopwatch's
[`StopwatchEvent`](https://github.com/symfony/stopwatch/blob/v8.1.0/StopwatchEvent.php) and
[`StopwatchPeriod`](https://github.com/symfony/stopwatch/blob/v8.1.0/StopwatchPeriod.php).

| Boundaries                                                                                                   | Promoted type                                                             | Coverage     |
| ------------------------------------------------------------------------------------------------------------ | ------------------------------------------------------------------------- | ------------ |
| Response age/max-age/TTL getters; max-age, shared-max-age, stale, TTL setters; open `setCache` duration keys | integer seconds, preserving nullable returns                              | S/V/I        |
| `Cookie::getMaxAge()`                                                                                        | integer seconds                                                           | S/V/I        |
| Session invalidate/migrate/regenerate lifetime and metadata lifetime                                         | nullable integer seconds on inputs; integer seconds on return             | S/V/I-family |
| `UploadedFile::getMaxFilesize()`                                                                             | integer or float bytes                                                    | S/V/I        |
| Symfony 7.3+ `EventStreamResponse` and `ServerEvent` retry constructor/getter/setter                         | nullable integer milliseconds, preserving each method's exact nullability | S/V/I        |
| Symfony 8+ `IpUtils::anonymize($v4Bytes, $v6Bytes)`                                                          | integer bytes intersected with `int<0, 4>` and `int<0, 16>`               | S/V/I        |
| Stopwatch event/period `getDuration()`; `getMemory()`                                                        | integer or float milliseconds; integer bytes                              | S/V/I        |

The HttpFoundation cache option shape remains open. Cookie expiration inputs and `getExpiresTime()` remain Unix
timestamps and are intentionally unbranded. Symfony 7's commented future IP parameters are not treated as public
reflection parameters before Symfony 8.

## Re-audit Triggers

Repeat the affected section when adding a boundary, admitting a new package major, changing a minimum version, or when
upstream changes a native signature inside a supported major. Adjacent releases around a discovered cutover belong in
both the focused consumer matrix and this ledger; checking only the newest release is insufficient for a structural
change.
