# Integrations

<figure class="logion" data-logion="SFA 15:50">
<div class="logion-text">
<blockquote>
<p>Three moons drew contrary tides from one sea, and the islanders feared that the waters would tear the shore apart.
Yet each tide carried a different gift—salt, silver weed, and warm rain—and withdrew before the next arrived. Give
praise for the hidden proportion that restraineth abundance; for when the moons aligned, the sea stood upright, and a
green island rose within it.</p>
</blockquote>
<p class="logion-citation">— <cite>Scholia of the Fifth Archive 15:50</cite></p>
</div>
<img src="images/logia/SFA-15_50.webp" alt="Three moons and ordered gift-bearing tides surrounding a green island at rose-gold dawn" width="960" height="540" loading="eager" fetchpriority="high">
</figure>

Apocrypha deliberately covers a small set of stable, useful, and unambiguous unit-bearing boundaries. The table below is
a record of verified behavior, not a promise to support every future or historical package major indefinitely.

## Version Policy

On each CI run, the consumer matrix resolves the latest compatible release of every verified major and any
package-specific minimum shown below. An unknown future major, or a release below a stated minimum, is rejected until
its signatures and semantics have been reviewed.

| Package family         | Integration key                                                                                                                                                            | Verified versions                   | Verification snapshots                                                           | Checked    |
| ---------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------------------- | -------------------------------------------------------------------------------- | ---------- |
| Carbon                 | `nesbot/carbon`                                                                                                                                                            | 2.62.1+ in 2.x; 3.x                 | `2.62.1`, `2.73.0`, `3.0.0`, `3.1.1`, `3.2.0`, `3.13.2`                          | 2026-08-09 |
| Guzzle                 | `guzzlehttp/guzzle`                                                                                                                                                        | 7, 8                                | `7.0.0`, `7.10.0`, `7.11.0`, `7.15.3`, `8.0.0`, `8.0.2`                          | 2026-08-09 |
| getID3                 | `james-heinrich/getid3`                                                                                                                                                    | 1.9.22+ in 1.x; 2.0.0-beta6+ in 2.x | `1.9.22`, `1.9.25`, `2.0.0-beta6`                                                | 2026-08-11 |
| Illuminate packages    | `illuminate/cache`, `illuminate/cookie`, `illuminate/filesystem`, `illuminate/http`, `illuminate/process`, `illuminate/queue`, `illuminate/redis`, or `illuminate/support` | 11, 12, 13                          | `v11.55.0`, `v12.65.0`, `v13.24.0`; HTTP, Queue, and Redis details below         | 2026-08-11 |
| Intervention Image     | `intervention/image`                                                                                                                                                       | 3, 4                                | `3.0.0`, `3.11.8`, `4.0.0`, `4.2.1`                                              | 2026-08-11 |
| Laravel framework      | Provider only; select the applicable component keys above                                                                                                                  | 11, 12, 13                          | `v11.55.0`, `v12.65.0`, `v13.24.0`; HTTP and Queue cutovers below                | 2026-08-09 |
| Measurements           | `nmarfurt/measurements`                                                                                                                                                    | 1.4+ in 1.x                         | `v1.4.0`                                                                         | 2026-08-10 |
| phpgeo                 | `mjaschen/phpgeo`                                                                                                                                                          | 4, 5, 6                             | `4.0.0`, `4.2.1`, `5.0.0`, `6.0.0`, `6.0.4`                                      | 2026-08-09 |
| Symfony HttpFoundation | `symfony/http-foundation`                                                                                                                                                  | 6.4+ in 6.x; 7.x; 8.x               | `v6.4.0`, `v6.4.43`, `v7.0.0`, `v7.2.9`, `v7.3.0`, `v7.4.16`, `v8.0.0`, `v8.1.4` | 2026-08-09 |
| Symfony Stopwatch      | `symfony/stopwatch`                                                                                                                                                        | 6, 7, 8                             | `v6.0.0`, `v6.4.24`, `v7.0.0`, `v7.4.8`, `v8.0.0`, `v8.1.0`                      | 2026-08-09 |

Laravel applications may install these APIs through `laravel/framework` instead of separate `illuminate/*` component
packages. Continue to select the precise component integration names, such as `illuminate/cache`; Composer's exact
replacement version supplies the verified major for explicit selection and autodetection. Broad or ambiguous replacement
constraints are not treated as verified versions.

Larastan 3 (verified at `v3.10.0`) is tested with every Illuminate integration and the complete Laravel framework across
Laravel 11 through 13. When Larastan is present, Apocrypha preserves its framework declarations and adds unit semantics
through PHPStan rules and type extensions. This automatic switch covers the complete integration; Apocrypha never loads
a partial Illuminate stub over Larastan. An installed Larastan major other than 3 is rejected while an Illuminate
integration is selected.

## Compatibility Before 1.0

The first releases treat the following surfaces as deliberate but provisional. A change during the 0.x series remains
compatibility-relevant and should be documented, even when Semantic Versioning permits it before 1.0.

| Surface                 | Provisional contract                                                                                                                                                                                                                   |
| ----------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Configuration           | `yumemiApocrypha.integrations` defaults to an empty list, `autoDetect` defaults to `false`, and `strictAutoDetect` defaults to `true`.                                                                                                 |
| Integration identifiers | The package names in the `Integration key` column are exact configuration keys. The Laravel framework row is provider-only: `laravel/framework` supplies applicable `illuminate/*` packages but is not itself an integration key.      |
| Selection               | Explicit selections always reject missing, unknown, or unverified packages. Autodetection forms a deduplicated union with explicit selections; `strictAutoDetect: false` permits only unsupported autodetected packages to be skipped. |
| Diagnostics             | Metadata-adapter boundary violations use `apocrypha.unit`; exact message text is not a compatibility promise. Stub-backed mismatches use PHPStan's own identifiers.                                                                    |
| PHP types               | `jbboehr\Yumemi\Apocrypha\Exception\ExceptionInterface` and `jbboehr\Yumemi\Apocrypha\Exception\InvalidConfigurationException` are public. Concrete internal-invariant exception classes are not public API.                           |
| Boundary expansion      | Adding a verified boundary or an autodetected integration can reveal a unit error in previously clean analysis. Supported-version reductions and diagnostic-producing coverage changes require an explicit compatibility decision.     |

## Carbon

Enable `nesbot/carbon` to brand fixed-duration calculations and waits. A branded native value remains an ordinary PHP
integer or float at runtime; Apocrypha describes Carbon's existing boundaries to PHPStan and does not change how Carbon
calculates dates.

| API concern                                           | Unit                          |
| ----------------------------------------------------- | ----------------------------- |
| Microsecond differences and adjustments               | `microsecond`                 |
| Millisecond differences and adjustments               | `millisecond`                 |
| Second, minute, and hour differences/adjustments      | `second`, `minute`, or `hour` |
| `secondsSinceMidnight()` and `secondsUntilEndOfDay()` | `second`                      |
| Carbon 3 `sleep()` methods                            | `second`                      |

<!-- yumemi-example: carbon-invalid -->

```php
<?php

use Carbon\CarbonImmutable;

/** @param unit_float<'minute'> $elapsed */
function recordElapsedMinutes(float $elapsed): void {}

$started = CarbonImmutable::parse('2026-08-05 09:00:00');
$finished = CarbonImmutable::parse('2026-08-05 09:00:30');

//! recordElapsedMinutes expects unit_float<'minute'>, unit_float<'second'> given
recordElapsedMinutes($started->diffInSeconds($finished));
```

Here `//!` marks the expected PHPStan diagnostic exercised by the documentation test; it is not Yumemi syntax.

Carbon 2.62.1 through 2.x uses integer-returning `diffInReal*()` methods, float-returning `floatDiffInReal*()` methods,
and integer `addReal*()`/`subReal*()` adjustments. Carbon 3 uses float-returning `diffIn*()` methods and accepts either
integer or float branded values for fixed-time adjustments. Carbon 3.0 and 3.1 expose the compatibility aliases
`addReal*()` and `subReal*()`; Carbon 3.2 and later use `addUTC*()`, `subUTC*()`, and `diffInUTC*()`. Apocrypha selects
the matching version profile from the installed release and applies it through PHPStan argument and return extensions.
Carbon's own declarations remain authoritative, so branded calls retain the concrete receiver type and unrelated Carbon
methods remain available.

The Carbon 2 integration starts at 2.62.1, the first release in the supported signature line that runs on Apocrypha's
PHP 8.2 baseline. Earlier Carbon 2 releases may advertise PHP 8 compatibility but fail against PHP 8.2's `DateTime`
behavior. Carbon 3 support starts at 3.0.0.

Calendar-relative days, weeks, months, and years remain unbranded. Their elapsed length depends on the starting date,
timezone transitions, and calendar rules, so they are not fixed multiplicative durations. Timestamps and timezone
offsets also remain outside this integration.

## Guzzle

Enable `guzzlehttp/guzzle` to brand selected request options, retry delays, progress byte counts, and transfer times.
The same open request-option shape applies to `Client` construction, concrete convenience methods such as `get()`, and
the synchronous and asynchronous methods shared with `ClientInterface`. Request delay values remain branded integers
through Guzzle 7.10 and accept branded integers or floats from Guzzle 7.11 onward.

| API concern                                        | Unit          |
| -------------------------------------------------- | ------------- |
| Total, connection, and stream read timeouts        | `second`      |
| Request delay and retry-delay callback result      | `millisecond` |
| Expect/Continue threshold and progress byte counts | `byte`        |
| `TransferStats` transfer time                      | `second`      |

<!-- yumemi-example: guzzle-invalid -->

```php
<?php

use GuzzleHttp\Client;

use function jbboehr\Yumemi\unit;

function fetchRemoteReport(Client $client): void
{
    $client->request('GET', '/reports', ['timeout' => unit(2, 'second')]);
    //! array{timeout: 250&unit_int<'1/1000 * second'>} given
    $client->request('GET', '/reports', [
        'timeout' => unit(250, 'millisecond'),
    ]);
}
```

The request-option shape is open: headers, authentication, handlers, and other options that Guzzle accepts remain
available without being restated by Apocrypha. The `expect` option retains Guzzle's `bool` alternative. Progress
callbacks receive four `byte`-branded integer parameters; annotate a named callback's parameters when its body needs to
retain those brands. Major-specific stubs preserve the different retry-delay callback signatures in Guzzle 7 and 8.

Handler-stat arrays and generic header access remain unbranded because their units depend on a runtime or literal key.
Pool-only option callbacks, cookie ages, and internal handler configuration are outside this bounded integration.

## getID3

Enable `james-heinrich/getid3` to brand the stable measurements in the open array returned by `getID3::analyze()` and
the optional file-size override accepted by `analyze()` and `openfile()`.

| Result or argument                 | Unit           |
| ---------------------------------- | -------------- |
| File size and audio/video offsets  | `byte`         |
| Playback duration                  | `second`       |
| Overall, audio, and video bitrates | `bit / second` |
| Audio sample and video frame rates | `hertz`        |
| Video width and height             | `pixel`        |

<!-- yumemi-example: getid3-invalid -->

```php
<?php

use JamesHeinrich\GetID3\GetID3;

/** @param unit_int<'second'>|unit_float<'second'> $duration */
function recordMediaDuration(int|float $duration): void {}

$metadata = (new GetID3())->analyze('/srv/media/interview.wav');

if (isset($metadata['playtime_seconds'])) {
    recordMediaDuration($metadata['playtime_seconds']);
}

if (isset($metadata['bitrate'])) {
    //! recordMediaDuration expects unit_float<'second'>|unit_int<'second'>, unit_float<'bit / second'>|unit_int<'bit / second'> given
    recordMediaDuration($metadata['bitrate']);
}
```

getID3 2.x uses the namespaced `JamesHeinrich\GetID3\GetID3` class shown above. Applications on getID3 1.x use the
global `getID3` class; Apocrypha selects the corresponding stub from the installed package version.

The result remains open because getID3 adds format-specific metadata dynamically. Its branded keys are optional so
callers must check that analysis produced them; guarded, unlisted keys remain available as `mixed`. The audio bitrate
also retains getID3's special `'free'` value. The standardized `video.resolution_x` and `video.resolution_y` keys are
integer counts of addressable raster samples, so they use Yumemi's nominal `pixel` unit. They are not physical-length
`css_pixel` values. Format-specific nested image dimensions remain outside the bounded result shape.

The 1.x integration starts at 1.9.22, the first verified release in that signature line that runs cleanly on Apocrypha's
PHP 8.2 baseline. The namespaced 2.x integration starts at the current `2.0.0-beta6` prerelease. Yumemi currently treats
`bit / second` and `hertz` as definitionally equivalent because `bit` is dimensionless, so it cannot yet detect a
bitrate passed to a sample-rate or frame-rate boundary.

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
    //! RateLimiter::hit() expects DateInterval|DateTimeInterface|unit_int<'second'>, 2&unit_int<'minute'> given
    $limiter->hit('report', unit(2, 'minute'));
}
```

Plain integers are rejected at annotated boundaries. Existing `DateTimeInterface`, `DateInterval`, closure, and nullable
alternatives remain valid where Laravel accepts them, as do calls that omit an optional duration.

With Larastan, the same boundaries are enforced through direct repository calls, the `Cache` facade, and method calls on
the repository returned by the zero-argument `cache()` helper.

## Illuminate Cookie

Enable `illuminate/cookie` to require a `minute` value for the lifetime accepted by
`Illuminate\Contracts\Cookie\Factory::make()` and `CookieJar::make()`.

The variadic cookie queue API is not annotated because the same call may receive either a prebuilt cookie or the
positional arguments forwarded to `make()`. Absolute expiration timestamps also remain unbranded.

## Illuminate Filesystem

Enable `illuminate/filesystem` to brand file-size results and the byte counts returned by local `put()`, `prepend()`,
and `append()` operations as `byte` values. This covers the filesystem contract, local filesystem, filesystem adapter,
and `LockableFile` size APIs.

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
Laravel 11.35.1 widened `PendingRequest::timeout()` and `connectTimeout()` from `int` to `int|float`. Apocrypha selects
an integer-only profile through 11.35.0 and the integer-or-float profile for 11.35.1 and later.

<!-- yumemi-example: illuminate-http-invalid -->

```php
<?php

use Illuminate\Http\Client\PendingRequest;

use function jbboehr\Yumemi\unit;

function configureRemoteArchive(PendingRequest $request): void
{
    $request->timeout(unit(30, 'second'));
    //! ::timeout() expects unit_float<'second'>|unit_int<'second'>, 250&unit_int<'1/1000 * second'> given
    $request->timeout(unit(250, 'millisecond'));
}
```

## Illuminate Support

Enable `illuminate/support` to brand framework timing helpers and benchmark results.

| API concern                        | Unit          |
| ---------------------------------- | ------------- |
| `Benchmark` measurements           | `millisecond` |
| `Sleep::sleep()` duration          | `second`      |
| `Sleep::usleep()` duration         | `microsecond` |
| `Timebox::call()` minimum duration | `microsecond` |

The fluent `Sleep::for($value)->seconds()` form is not annotated because the unit is selected after the numeric value
crosses the first method boundary. The direct `sleep()` and `usleep()` entry points have unambiguous units.
`Sleep::sleep()` preserves Laravel's integer-or-float input, while `Sleep::usleep()` preserves its integer-only input.

## Illuminate Process

Enable `illuminate/process` to require `second` values for `PendingProcess::timeout()` and `idleTimeout()`.

Laravel 13 also accepts `CarbonInterval` at these boundaries. Apocrypha selects a major-specific stub so this
alternative remains valid on Laravel 13 without incorrectly allowing it on Laravel 11 or 12.

## Illuminate Queue

Enable `illuminate/queue` to brand delayed dispatches, job release delays, and physical `WorkerOptions` values.

| API concern                                        | Unit             |
| -------------------------------------------------- | ---------------- |
| Delayed dispatch and job release                   | `second`         |
| Worker backoff, timeout, sleep, rest, and lifetime | `second`         |
| Worker memory limit                                | `1048576 * byte` |

The memory limit is measured in binary megabytes by Laravel's worker implementation, so Apocrypha uses the exact scale
`1048576 * byte`. Attempt counts, maximum jobs, and other dimensionless controls remain ordinary integers.
`stopWhenEmptyFor` was added independently to Laravel 11.53.0, 12.60.0, and 13.10.0. Apocrypha selects the complete
worker profile at each release boundary so earlier releases in every supported major retain their original constructor
and property surface.

## Illuminate Redis

Enable `illuminate/redis` to distinguish limiter lifetimes and blocking timeouts in seconds from polling sleeps and
reported command latency in milliseconds.

| API concern                                      | Unit          |
| ------------------------------------------------ | ------------- |
| Duration-window decay and blocking timeout       | `second`      |
| Concurrency-lock release and blocking timeout    | `second`      |
| Limiter polling sleep                            | `millisecond` |
| `CommandExecuted::$time` and constructor latency | `millisecond` |

<!-- yumemi-example: illuminate-redis-invalid -->

```php
<?php

use Illuminate\Redis\Limiters\DurationLimiterBuilder;

use function jbboehr\Yumemi\unit;

function configureRedisPolling(DurationLimiterBuilder $limiter): void
{
    $limiter->block(unit(5, 'second'));
    $limiter->sleep(unit(250, 'millisecond'));
    //! DurationLimiterBuilder::sleep() expects unit_int<'1/1000 * second'>, 1&unit_int<'second'> given
    $limiter->sleep(unit(1, 'second'));
}
```

The fluent duration builder also accepts Laravel's published `DateTimeInterface` and `DateInterval` alternatives for its
decay window. The concrete limiter constructors and `block()` methods carry the same units as their builders. Absolute
limiter-expiry timestamps remain unbranded. The shared profile was checked at the initial and current releases of each
supported major: `v11.0.0`, `v11.51.0`, `v12.0.0`, `v12.66.0`, `v13.0.0`, and `v13.25.0`.

## Intervention Image

Enable `intervention/image` to distinguish raster dimensions and coordinates from rotation angles. These values remain
ordinary PHP integers and floats at runtime; Apocrypha adds units only to PHPStan's view of Intervention Image's public
manager and image APIs.

| API concern                                                  | Unit     |
| ------------------------------------------------------------ | -------- |
| Image creation, resizing, canvas, crop, and pixelation sizes | `pixel`  |
| Text, crop, insertion, fill, and drawing coordinates         | `pixel`  |
| `width()` and `height()` results                             | `pixel`  |
| `rotate()` angle                                             | `degree` |

<!-- yumemi-example: intervention-image-invalid -->

```php
<?php

use Intervention\Image\ImageManager;

use function jbboehr\Yumemi\unit;

$manager = new ImageManager(new Intervention\Image\Drivers\Gd\Driver());
$image = $manager->createImage(unit(1200, 'pixel'), unit(800, 'pixel'));
$image->rotate(unit(45.0, 'degree'));

//! ImageInterface::rotate() expects unit_float<'arc_degree'>, 1.0&unit_float<'radian'> given
$image->rotate(unit(1.0, 'radian'));
```

Intervention Image 3 names the manager method `create()`; version 4 uses `createImage()` and also accepts its `Fraction`
enum wherever the upstream API permits relative dimensions. Apocrypha preserves those alternatives. The integration is
metadata-driven so it does not replace the package's large central interfaces or hide unrelated methods and concrete
return precision.

Resolution values remain unbranded because their scale depends on whether the image resolution is expressed per inch or
per centimeter. Quality, opacity, and transparency are also outside this bounded first surface. The `pixel` brand means
a nominal raster sample, not the physical-length `css_pixel` unit. Plain upstream integers are not narrowed from runtime
validation or convention alone.

## Measurements

Enable `nmarfurt/measurements` to require the unit named by each `Length` and `Duration` magic factory. The scalar
passed to a factory remains an ordinary PHP float at runtime; Apocrypha adds the unit requirement only to PHPStan's view
of the existing call.

| Factory family | Covered units                                                                                 |
| -------------- | --------------------------------------------------------------------------------------------- |
| Metric length  | Megameter through picometer                                                                   |
| Other length   | Inch, foot, yard, mile, light-year, nautical mile, fathom, furlong, astronomical unit, parsec |
| Duration       | Second, minute, hour                                                                          |

<!-- yumemi-example: measurements-invalid -->

```php
<?php

use Measurements\Quantities\Length;

use function jbboehr\Yumemi\unit;

$aisle = Length::meters(unit(4.48, 'meter'));

//! Measurements\Quantities\Length::meters() expects unit_float<'meter'>, 14.7&unit_float<'international_foot'> given at a Yumemi Apocrypha unit boundary.
Length::meters(unit(14.7, 'foot'));
```

The active integration is metadata-driven, so the returned `Length` or `Duration` object retains the upstream package's
complete magic-factory, conversion, and arithmetic API. Apocrypha does not infer a native scalar unit from an object's
state, so constructors, `value()`, `addValue()`, `convertTo()`, and similar state-dependent methods remain unchanged.
Other quantity classes are also left untouched.

Absolute-temperature factories remain plain floats. Celsius and Fahrenheit values are coordinate points rather than
multiplicative temperature differences, and Yumemi's current branded native scalar types cannot preserve that
distinction. Support starts at Measurements 1.4.0, the first release whose Composer constraint includes PHP 8.

## phpgeo

Enable `mjaschen/phpgeo` to brand phpgeo's selected distance, area, bearing, and tolerance boundaries.

| API concern                                    | Unit        |
| ---------------------------------------------- | ----------- |
| Distances, lengths, perimeters, and tolerances | `meter`     |
| Polygon area                                   | `meter ^ 2` |
| Bearings and destination-bearing inputs        | `degree`    |

This covers the distance calculators and their coordinate convenience method; line, polyline, and polygon measurements;
cardinal-direction components; destination calculations; bounds expansion; perpendicular-distance utilities; polyline
simplification tolerance.

<!-- yumemi-example: phpgeo-invalid -->

```php
<?php

use Location\Bearing\BearingSpherical;
use Location\Coordinate;

use function jbboehr\Yumemi\unit;

function projectSurveyPoint(BearingSpherical $bearing, Coordinate $origin): void
{
    $bearing->calculateDestination($origin, unit(45.0, 'degree'), unit(500.0, 'meter'));
    //! calculateDestination() expects unit_float<'arc_degree'>, 0.5&unit_float<'radian'> given
    $bearing->calculateDestination(
        $origin,
        unit(0.5, 'radian'),
        unit(500.0, 'meter'),
    );
}
```

Native boundaries do not convert values. A `kilometer` value is dimensionally compatible with a meter distance but is
not definitionally equivalent, so convert it before calling phpgeo.

Latitude and longitude remain ordinary floats. They are angular coordinates with distinct origins and bounded domains,
not interchangeable angle magnitudes. Dimensionless fractions, inverse flattening, iteration controls, and geometry
counts also remain unbranded. Bearing brands express degrees but do not encode phpgeo's `0` through `360` range because
branded float ranges are not yet available.

## Symfony HttpFoundation

Enable `symfony/http-foundation` to catch cache durations, session lifetimes, and SSE reconnect delays that use adjacent
time units. Apocrypha describes the existing HttpFoundation calls to PHPStan; the values remain ordinary PHP integers at
runtime and no cache value is converted automatically.

| API concern                                 | Unit          | Availability |
| ------------------------------------------- | ------------- | ------------ |
| Response age, freshness, staleness, and TTL | `second`      | 6.4+         |
| Cookie max-age and session cookie lifetime  | `second`      | 6.4+         |
| Maximum configured upload size              | `byte`        | 6.4+         |
| Server-sent event reconnect retry           | `millisecond` | 7.3+         |
| IPv4 and IPv6 anonymization suffix lengths  | `byte`        | 8.x          |

<!-- yumemi-example: symfony-http-foundation-invalid -->

```php
<?php

use Symfony\Component\HttpFoundation\Response;

use function jbboehr\Yumemi\unit;

function cacheHttpFoundationReport(Response $response): void
{
    $response->setMaxAge(unit(30, 'second'));
    //! Response::setMaxAge() expects unit_int<'second'>, 250&unit_int<'1/1000 * second'> given
    $response->setMaxAge(unit(250, 'millisecond'));
}
```

The response integration also covers the open options array passed to `Response::setCache()`. Its duration keys are
branded without sealing the array or restating unrelated upstream options. `Cookie::getMaxAge()` is a relative duration,
but cookie expiration inputs, `Cookie::getExpiresTime()`, and URI-signing expirations remain unbranded Unix timestamps.

Symfony 7.3 introduced `EventStreamResponse` and `ServerEvent`; their constructor, getter, and setter retry values use
milliseconds. Symfony 8 made the IPv4 and IPv6 byte-count parameters of `IpUtils::anonymize()` formal API parameters.
Those parameters preserve Symfony's `0` through `4` and `0` through `16` integer ranges in addition to their byte brand.
Earlier Symfony releases expose only the IP string in reflection, so Apocrypha does not manufacture the later arguments
there.

## Symfony Stopwatch

Enable `symfony/stopwatch` to brand elapsed durations and memory results from `StopwatchEvent` and `StopwatchPeriod`.

| API concern     | Unit          |
| --------------- | ------------- |
| `getDuration()` | `millisecond` |
| `getMemory()`   | `byte`        |

Duration results retain Symfony's `int|float` alternative: ordinary precision returns integers, while the
`morePrecision` mode may return floats. Both alternatives carry the same millisecond unit.

<!-- yumemi-example: symfony-stopwatch-invalid -->

```php
<?php

use Symfony\Component\Stopwatch\Stopwatch;

/** @param unit_int<'second'>|unit_float<'second'> $duration */
function recordProfileDurationInSeconds(int|float $duration): void {}

$event = (new Stopwatch())->start('render-report');

//! recordProfileDurationInSeconds expects unit_float<'second'>|unit_int<'second'>, unit_float<'1/1000 * second'>|unit_int<'1/1000 * second'> given
recordProfileDurationInSeconds($event->getDuration());
```

Origins and relative start and end times remain unbranded. They are clock coordinates, not elapsed durations, and
Yumemi's native brands do not represent coordinate origins.

## Limitations

- Most stubs cover signatures shared by all verified majors. A release-specific stub or metadata profile is selected
  where a supported signature differs, as with Carbon's three profiles and Illuminate Process on Laravel 13.
- Native branded arguments are not converted. Dimensionally compatible but differently scaled units remain invalid.
- Dynamic or unsupported unit expressions lose Yumemi's precise brand according to the core extension's normal rules.
- Application wrappers can erase a third-party option's key-specific type when they merge generic arrays before the
  package boundary. Give the wrapper parameter the appropriate Yumemi native type, or construct a branded value before
  placing it in the options array; Apocrypha cannot soundly recover the original scalar after that information is lost.
- Metadata-adapter argument and property violations use the PHPStan diagnostic identifier `apocrypha.unit`. An unpacked
  argument is checked when PHPStan can resolve it to a finite constant array; Apocrypha does not guess positions after
  an unknown unpack.
- Enabling Apocrypha enables Yumemi's parser-based optional-tag promotion, with the associated PHPStan upgrade and
  extension-conflict risk.

[Documentation index](./) · [Repository README](https://github.com/jbboehr/yumemi-apocrypha.php)
