# Planning

Yumemi Apocrypha is the separately versioned home for curated unit-aware integrations with third-party PHP packages. Its
primary maintenance obligation is to keep every enabled stub structurally compatible with the upstream versions it
claims to support and semantically accurate about the represented units.

## Current Scope

- Explicit integration selection and optional Composer-package autodetection are implemented for directly installed
  packages and exact Composer replacements such as the Illuminate components supplied by `laravel/framework`.
- Explicit selections and strict autodetection reject installed package versions that have not been verified, including
  releases below a package-specific minimum.
- `nesbot/carbon` covers fixed-duration differences, adjustments, and waits from Carbon 2.62.1 through Carbon 3. Its
  three version profiles preserve Carbon 2's integer APIs, Carbon 3.0 through 3.1's `Real` compatibility aliases, and
  Carbon 3.2 and later's `UTC` aliases without presenting calendar-relative operations as fixed durations.
- `james-heinrich/getid3` covers a bounded open result shape for byte counts, durations, bitrates, sample rates, and
  frame rates from getID3 1.9.22 and later 1.x releases and 2.0.0-beta6 and later 2.x releases.
- `guzzlehttp/guzzle` covers selected request timeouts, delays, byte thresholds and callbacks, retry delays, and
  transfer times across Guzzle 7 and 8, including the request-delay native-type change at Guzzle 7.11.
- `illuminate/cache`, `illuminate/cookie`, `illuminate/filesystem`, `illuminate/http`, `illuminate/support`,
  `illuminate/process`, and `illuminate/queue` cover stable unit-bearing APIs across Laravel 11 through 13.
- Every Illuminate integration coexists automatically with Larastan 3. Standalone analysis uses the package stubs;
  Larastan analysis keeps Larastan's declarations and reproduces Apocrypha's unit boundaries through a metadata-driven
  rule and type-extension adapter, with parity enforced against the canonical stubs.
- `mjaschen/phpgeo` covers selected meter distances and tolerances, square-meter areas, and degree bearings across
  phpgeo 4 through 6 while leaving coordinate origins unbranded.
- `symfony/http-foundation` covers response cache durations, cookie max-age and session lifetimes, configured upload
  byte limits, Symfony 7.3 and later SSE retry delays, and Symfony 8 IP anonymization byte counts. Absolute expiration
  timestamps remain unbranded.
- `symfony/stopwatch` covers event and period durations and memory results across Symfony Stopwatch 6 through 8.
- The loader can enforce a minimum release within a major and select major- or minor-version-specific stub files when a
  supported upstream signature differs. Carbon uses three release profiles; Illuminate HTTP distinguishes Laravel 11
  before and after 11.35.1; Illuminate Queue uses cutovers at Laravel 11.53.0, 12.60.0, and 13.10.0; Guzzle
  distinguishes request delays before and after 7.11.0; getID3 uses minimum and major selection for its global 1.x and
  namespaced 2.x APIs; Illuminate Process and Guzzle also use major-specific files.
- Isolated Composer consumers verify every supported major, automatic and manual PHPStan registration, source installs,
  a representative Composer archive, and Laravel 11 through 13 replacement metadata for every Illuminate integration.
  Every Illuminate subpackage and the combined framework fixture run both with and without Larastan. HttpFoundation and
  Stopwatch run both with and without `phpstan/phpstan-symfony` 2 across every supported Symfony major.
- Akashi inventories every fenced PHP example in the README and public documentation. An explicit manifest assigns each
  current example to its real isolated consumer, and coverage tests reject unlisted documents, unmarked or unclassified
  examples, stale manifest entries, and routes that are not wired into the consumer harness. Akashi preserves each
  authored fixture exactly while leaving package installation and PHPStan verification in that consumer.
- Yumemi's generic `@yumemi-*` annotation mechanism remains in the core package; Apocrypha owns only package-specific
  stubs and their selection policy.

## Maintenance Policy

The supported-version table is descriptive rather than a promise to support every future or historical framework major.
New majors should normally be added after their reflected signatures and PHPStan behavior pass the complete consumer
suite. Integration scope remains curated: add APIs only when their physical unit is stable, useful, and unambiguous.
Larastan compatibility is selected for an entire Illuminate integration rather than as a partial stub overlay, and an
unverified Larastan major is rejected until its combined behavior passes the same matrix. `phpstan/phpstan-symfony` 2
currently coexists directly with the Symfony stubs because its active declarations do not overlap them. If an extension
release begins owning any declaration in a selected integration, disable that integration's complete stub set and use a
metadata adapter rather than loading both.

## Maintenance Backlog

- Resolve the release-blocking Carbon API-preservation failure found by the
  [BookStack application smoke test](bookstack-application-smoke-test-2026-08-09.md). A partial `CarbonInterface` stub
  can hide unrelated valid methods after a branded call; replace it with metadata-driven extensions or another design
  that preserves the complete upstream surface, then add a chained unrelated-method regression case.
- Make the package entry point safe for Composer's default symlinked path repositories and add a symlink-mode consumer
  test. The existing mirror and archive cases do not expose the current parent-relative Yumemi include failure.
- Document the migration from a manually included Larastan extension to automatic Composer extension discovery. Both
  registration mechanisms work, but enabling the installer without removing the manual Larastan include loads it twice.
- Extend the Larastan compatibility adapter to cover supported boundaries reached through facades and global helpers.
  The BookStack probe found that direct cache repository calls are enforced while `Cache::put()` and `cache()->put()`
  are not.
- Add scheduled CI that resolves and tests the latest compatible upstream releases. The consumer matrix currently
  exercises those releases when CI runs, but push- and pull-request-only triggers cannot detect upstream drift while the
  repository is otherwise idle.
- Keep the [integration semantic audit](integration-semantic-audit.md) current when an integration or verified version
  profile changes. Re-run its source, structure, valid-case, and invalid-case checks before the first public release.

## Future Candidates

Evaluate further integrations from observed application value rather than package breadth. Preserve version-specific
upstream alternatives when a signature differs between supported majors, and avoid timestamps or count-like integers
whose physical semantics are not represented by Yumemi's native duration and quantity brands.

The [August 2026 Packagist survey](stub-candidate-survey-2026-08-05.md) records the initial bounded candidate scan, and
the [science-focused follow-up](stub-candidate-survey-science-2026-08-05.md) adds 225 repositories selected through
math, science, geospatial, measurement, and deliberately noisier tags. Treat both sets of automated rankings as
discovery leads; every candidate must still pass the gates below against the real upstream API.

### Evaluation Gates

Unit-rich packages are candidates, not automatically good stub targets. Before ranking an integration by ecosystem reach
or the number of measurements it contains, establish that its useful boundaries can be represented soundly by Yumemi's
current type model:

1. The unit must be concrete, stable, and documented by the upstream package.
2. The required unit must exist in the effective Yumemi registry, or the integration must have a deliberate and
   documented registry strategy.
3. The unit must not depend on a sibling argument, object state, runtime metadata, or an arbitrary user-provided string
   unless Yumemi or a bounded integration extension can represent that dependency soundly.
4. A timestamp, coordinate origin, or other point must not be mislabeled as an ordinary duration or displacement.
5. The stub must preserve every valid upstream alternative and must not replace a broad options array with a sealed,
   incomplete shape.
6. Supported majors must have enough stable, high-confidence boundaries to justify their ongoing consumer matrix.

After those gates pass, prefer ecosystem reach, collision potential between adjacent units, density of useful public
boundaries, and maintenance stability. A package that uses several independent dimensions normally has more value than
one that repeats the same duration unit throughout, but a small stable API may still dominate a theoretically rich API
whose useful data is dynamic or context-dependent.

### Survey Follow-ups

Do not reject a measurement library merely because it represents quantities with objects. Its object API may already
prevent one quantity class from being passed as another, but native scalar values can still cross constructors,
factories, accessors, conversion helpers, and serialization boundaries. Reconsider such a package when the concrete
receiver class or fixed method name determines the scalar unit. Continue to deprioritize libraries that expose only one
native unit, even when that unit appears frequently.

The object-backed candidates divide into four practical groups:

- `nmarfurt/measurements` is the strongest renewed candidate. Magic factories such as `Length::meters()`,
  `Length::feet()`, `Temperature::celsius()`, `Temperature::fahrenheit()`, `Duration::seconds()`, and
  `Duration::hours()` give fixed, adjacent-unit scalar inputs. Its existing `@method` declarations may permit a bounded
  stub without general object-state propagation. Constructors, `value()`, and scalar arithmetic remain dependent on the
  selected `Unit` object and must not receive fixed annotations.
- `php-unit-conversion/php-unit-conversion` has meaningful ecosystem reach and concrete classes such as `Meter`, `Foot`,
  `Celsius`, `Fahrenheit`, `Second`, and `MilliSecond`. The inherited `getValue()` unit is determined by the receiver
  class and is suitable for a receiver-aware return extension. Constructor and `setValue()` inputs depend on the
  `convertFromBaseUnit` flag, so complete coverage requires conditional argument validation rather than an unconditional
  stub.
- `pdobrovolny/quantity` fixes the unit through concrete container classes whose inherited constructor and public
  `float $value` property form useful scalar boundaries. Evaluate only a common-unit subset: the package has a very
  large surface and requires PHP 8.5, so it needs a dedicated consumer profile and enough adoption to justify that
  matrix.
- `diversified-design/mesuraphp` is a clean semantic experiment. For example, `Foot::fromMeterValue()` consumes meters,
  `Foot::toMeterValue()` returns meters, while construction and `getValue()` use feet. Its pre-1.0 status and negligible
  current adoption keep it below the three candidates above.

Enum- or state-selected converters remain possible extension projects rather than ordinary stub work.
`jamal/unit-converter` could use a dynamic return extension and argument rule around its direct source- and target-enum
arguments. `kolaybi/unit-converter` would additionally need source and target units propagated through
`PendingConversion` and `ConversionResult`. Their negligible current adoption does not justify building that machinery
specifically for them. `andanteproject/measurement` carries values in `NumberInterface` objects rather than native
scalars; `jobmetric/laravel-unit` derives units from runtime records; and `khaledalam/unit`, `hiqdev/php-units`, and
`asika/better-units` predominantly select scalar meaning through runtime objects or strings. `gabrielelana/byte-units`
has useful byte boundaries but little adjacent-unit collision potential.

The science-focused survey also produced several non-measurement-library follow-ups:

- `seamapi/seam` is the strongest new fixed-boundary lead. Its generated thermostat SDK exposes separately named Celsius
  and Fahrenheit float fields and parameters; verify a deliberately bounded model and client subset across supported
  releases.
- `telnyx/telnyx-php` exposes seconds and milliseconds across generated models. Review a small stable subset before
  accepting the maintenance cost of its generated, fast-moving surface.
- `sobhanmohammadi/geometry` and `tecnickcom/tc-lib-pdf-graph` have fixed degree boundaries suitable for small source
  reviews, but their reach and adjacent-unit collision potential are weaker.
- GeoTools, Stadia Maps, Ricklab Location, and similar geographic packages generally select units through a sibling
  argument or object state. OPC UA packages generally attach units through runtime metadata. Keep both groups
  deprioritized until a reusable dependent-unit strategy exists.

The [framework-focused survey profile](../../tools/stub-candidate-survey-frameworks.json) inspected 32 curated framework
and component packages. `symfony/http-foundation`, its strongest result, graduated into the current integration set with
separate base, 7.3+ SSE, and Symfony 8 IP-anonymization profiles. Manual review reduced the remaining broad scanner
ranking to the following priorities:

- `cakephp/cakephp` has the smallest high-signal surface. In CakePHP 5.4, `LockInterface::acquireBlocking()` places the
  second-valued lock TTL and acquisition timeout beside a millisecond-valued retry interval. Start support at 5.4.0 and
  cover the facade, interface, and engine without pulling unrelated CakePHP APIs into the first stub.
- `symfony/messenger` combines millisecond retry delays, microsecond worker sleeps, and second worker time limits.
  Direct retry-strategy and delay-stamp boundaries are straightforward. `Worker::run()` needs either a genuinely open
  options shape or bounded extension logic so annotation does not seal the upstream options array.
- `yiisoft/yii2` has a direct seconds-versus-milliseconds collision in `MemCacheServer`: `timeout` is milliseconds while
  `retryInterval` is seconds. Its public untyped properties require careful stub verification, but the boundary is
  stable and useful. File rotation sizes measured in kilobytes provide a secondary fixed-unit surface.
- `codeigniter4/framework` exposes a microsecond session lock retry interval, second-valued throttling windows, degree
  image rotation, and image quality on a 0 through 100 percentage scale. Pixel dimensions remain out of scope until
  Yumemi has an explicit pixel model; do not let that block a smaller duration, angle, and percentage integration.
- `nette/utils` has strong ecosystem reach, but its most interesting image methods mix pixel integers with percentage
  strings and percentage opacity. Keep it behind the pixel semantic decision rather than branding only the easy opacity
  parameter.
- `slim/slim` produced no meaningful native unit boundary: its pixel match came from error-page CSS. `slim/psr7` has
  legitimate byte-valued stream and upload sizes, but bytes are its only useful native unit. Keep Slim in the surveyed
  set and deprioritize an integration unless another fixed unit appears.

The remaining reviewed Symfony, Laminas, Nette, Spiral, and Hyperf packages predominantly expose one native unit, select
units dynamically, wrap measurements in objects, or produced contextual scanner matches. Do not promote them from the
automated ranking without a new fixed-boundary finding.

### Candidates Requiring Core Work

The following packages remain valuable after a small amount of core semantic or type-system work:

- `phpoffice/phpspreadsheet`: begin with fixed conversion helpers. Methods whose value unit is selected by a companion
  argument, and Excel column widths whose interpretation depends on font context, cannot be modeled as fixed branded
  parameters.
- `phpoffice/phpword` and `phpoffice/phppresentation`: conversion helpers and selected OOXML boundaries are strong
  targets after deciding how Yumemi names and defines pixels, points, twips, and EMUs. Their literal conversion
  constants are the clearest third-party use case for constant-valued native unit types.
- `intervention/image` and `imagine/imagine`: pixel dimensions, signed coordinates, angles, and percentages are useful,
  but the integration first needs an explicit pixel model. Branded integer and float ranges would then express positive
  dimensions and bounded opacity more accurately.
- `league/flysystem`: nonnegative file sizes are clear but provide less incremental value than the mixed-unit
  candidates.

OpenTelemetry remains an interesting future experiment, but its measurement unit is attached to an instrument and may be
an arbitrary user-provided string. Useful coverage therefore needs dependent unit propagation rather than a broad fixed
stub. Stripe, AWS SDK generation, and state-dependent PDF coordinate APIs are not current roadmap priorities.

### Core Capability Effects

Yumemi's implemented and planned types affect these integrations in different ways:

- **Branded integer ranges and constants** are available through intersections such as `unit_int<'byte'>&int<0, max>`
  and `3&unit_int<'meter'>`. Apocrypha preserves these refinements when an upstream signature publishes them; it does
  not narrow a plain upstream `int` from implementation behavior or domain convention alone. Existing unbounded
  `unit_int` boundaries accept compatible branded constants and ranges.
- **Branded float ranges** are most valuable to phpgeo's latitude and longitude bounds, then fractional timeouts,
  durations, rates, and image parameters. They require more custom PHPStan machinery than integer ranges.
- **Constant-valued unit types** primarily improve PHPWord and PHPPresentation conversion-ratio constants. They preserve
  useful literal information through ordinary Yumemi arithmetic, but no candidate integration should depend on them
  merely to brand a nonconstant parameter or return.
- **`unit_numeric_string<'...'>`** provides little immediate value to this candidate set. getID3 exposes its principal
  measurements as integers or floats, while PHPWord CSS values such as `10px` and formatted media durations such as
  `3:45` are not PHP numeric strings. Guzzle's string-valued `Content-Length` is accessible through a generic header API
  and would need literal-key-dependent inference rather than a fixed stub. Numeric-string brands remain most relevant to
  request, configuration, environment, and serialized-scalar boundaries.
- **Nominal document and image units** need an explicit semantic decision before stubbing. A pixel may be a sample count
  or a physical length under an assumed resolution; twips and EMUs are fixed document units; typographic point names
  must match the effective registry. Do not silently approximate these distinctions for the sake of broader coverage.

With the bounded Guzzle, phpgeo, and getID3 integrations in place and branded integer precision available, decide the
pixel and OOXML unit model before expanding into the document and image package group. Float ranges can follow when
coordinate coverage supplies the concrete acceptance tests.
