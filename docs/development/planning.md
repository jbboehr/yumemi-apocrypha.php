# Planning

Yumemi Apocrypha is the separately versioned home for curated unit-aware integrations with third-party PHP packages. Its
primary maintenance obligation is to keep every enabled stub structurally compatible with the upstream versions it
claims to support and semantically accurate about the represented units.

## Current Scope

- Explicit integration selection and optional Composer-package autodetection are implemented.
- Explicit selections and strict autodetection reject installed package majors that have not been verified.
- `guzzlehttp/guzzle` covers selected request timeouts, delays, byte thresholds and callbacks, retry delays, and
  transfer times across Guzzle 7 and 8.
- `illuminate/cache`, `illuminate/cookie`, `illuminate/filesystem`, `illuminate/http`, `illuminate/support`,
  `illuminate/process`, and `illuminate/queue` cover stable unit-bearing APIs across Laravel 11 through 13.
- `mjaschen/phpgeo` covers selected meter distances and tolerances, square-meter areas, and degree bearings across
  phpgeo 4 through 6 while leaving coordinate origins unbranded.
- `symfony/stopwatch` covers event and period durations and memory results across Symfony Stopwatch 6 through 8.
- The loader can select major-specific stub files when a supported upstream signature differs; Illuminate Process uses
  this for Laravel 13's `CarbonInterval|int` timeout boundary, and Guzzle uses it for retry callback signatures.
- Isolated Composer consumers verify every supported major, automatic and manual PHPStan registration, source installs,
  and a representative Composer archive.
- Yumemi's generic `@yumemi-*` annotation mechanism remains in the core package; Apocrypha owns only package-specific
  stubs and their selection policy.

## Maintenance Policy

The supported-version table is descriptive rather than a promise to support every future or historical framework major.
New majors should normally be added after their reflected signatures and PHPStan behavior pass the complete consumer
suite. Integration scope remains curated: add APIs only when their physical unit is stable, useful, and unambiguous.

## Future Candidates

Evaluate further integrations from observed application value rather than package breadth. Preserve version-specific
upstream alternatives when a signature differs between supported majors, and avoid timestamps or count-like integers
whose physical semantics are not represented by Yumemi's native duration and quantity brands.

### Evaluation Gates

Unit-rich packages are candidates, not automatically good stub targets. Before ranking an integration by ecosystem reach
or the number of measurements it contains, establish that its useful boundaries can be represented soundly by Yumemi's
current type model:

1. The unit must be concrete, stable, and documented by the upstream package.
2. The required unit must exist in the effective Yumemi registry, or the integration must have a deliberate and
   documented registry strategy.
3. The unit must not depend on a sibling argument, object state, runtime metadata, or an arbitrary user-provided string
   unless Yumemi already supports that dependency.
4. A timestamp, coordinate origin, or other point must not be mislabeled as an ordinary duration or displacement.
5. The stub must preserve every valid upstream alternative and must not replace a broad options array with a sealed,
   incomplete shape.
6. Supported majors must have enough stable, high-confidence boundaries to justify their ongoing consumer matrix.

After those gates pass, prefer ecosystem reach, collision potential between adjacent units, density of useful public
boundaries, and maintenance stability. A package that uses several independent dimensions normally has more value than
one that repeats the same duration unit throughout, but a small stable API may still dominate a theoretically rich API
whose useful data is dynamic or context-dependent.

### Candidate Order

The leading remaining candidate that can provide value without first extending Yumemi's type system is:

1. `james-heinrich/getid3`: file sizes, durations, bitrates, sample rates, frame rates, and media dimensions provide
   high measurement density, but its large format-dependent result arrays require a deliberately bounded and
   maintainable shape rather than an attempted transcription of all metadata.

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
- `nesbot/carbon`: annotate only APIs with explicit fixed-duration semantics. Calendar-relative days, months, and years
  must not be presented as fixed multiplicative durations.
- `league/flysystem`: nonnegative file sizes are clear but provide less incremental value than the mixed-unit
  candidates.

OpenTelemetry remains an interesting future experiment, but its measurement unit is attached to an instrument and may be
an arbitrary user-provided string. Useful coverage therefore needs dependent unit propagation rather than a broad fixed
stub. Stripe, AWS SDK generation, and state-dependent PDF coordinate APIs are not current roadmap priorities.

### Core Capability Effects

Future Yumemi types improve these integrations in different ways:

- **Branded integer ranges** have the broadest immediate value: nonnegative byte counts, sample rates, delays,
  durations, and image dimensions; percentages from zero through one hundred; and exact sentinel unions where upstream
  uses a special negative value. PHPStan's existing integer-range model makes this the preferred first core enhancement.
- **Branded float ranges** are most valuable to phpgeo's latitude and longitude bounds, then fractional timeouts,
  durations, rates, and image parameters. They require more custom PHPStan machinery than integer ranges.
- **Constant-valued unit types** primarily improve PHPWord and PHPPresentation conversion-ratio constants. They also
  preserve useful literal information through ordinary Yumemi arithmetic, but no candidate integration should depend on
  them merely to brand a nonconstant parameter or return.
- **`unit_numeric_string<'...'>`** provides little immediate value to this candidate set. getID3 exposes its principal
  measurements as integers or floats, while PHPWord CSS values such as `10px` and formatted media durations such as
  `3:45` are not PHP numeric strings. Guzzle's string-valued `Content-Length` is accessible through a generic header API
  and would need literal-key-dependent inference rather than a fixed stub. Numeric-string brands remain most relevant to
  request, configuration, environment, and serialized-scalar boundaries.
- **Nominal document and image units** need an explicit semantic decision before stubbing. A pixel may be a sample count
  or a physical length under an assumed resolution; twips and EMUs are fixed document units; typographic point names
  must match the effective registry. Do not silently approximate these distinctions for the sake of broader coverage.

With the bounded Guzzle and phpgeo integrations in place, implement branded integer ranges, then decide the pixel and
OOXML unit model before expanding into the document and image package group. Float ranges can follow when coordinate
coverage supplies the concrete acceptance tests.
