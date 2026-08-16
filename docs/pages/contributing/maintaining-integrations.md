# Maintaining Integrations

<figure class="logion" data-logion="AWC 12:17">
<div class="logion-text">
<blockquote>
<p>The mariners returned the shattered lens unto the daughters of its maker, who received it without reproach and
preserved the star last witnessed therein.</p>
</blockquote>
<p class="logion-citation">— <cite>Acts of the Western Court 12:17</cite></p>
</div>
<img src="../images/logia/AWC-12_17.webp" alt="Mariners returning a shattered celestial lens to two custodians at a midnight observatory harbor" width="960" height="540" loading="eager" fetchpriority="high">
</figure>

Verify every integration against the real upstream package with isolated PHPStan consumer tests. A synthetic stub test
alone does not establish compatibility.

## Before Changing An Integration

Identify the upstream package, every major claimed by the loader, and the exact unit represented by each affected
parameter, return, or property. Prefer APIs whose physical interpretation is stable and unambiguous. Do not expand
coverage solely because a parameter name resembles a duration or size.

## Verify Upstream Signatures

The consumer fixture installs the release recorded in its committed profile lock. A weekly CI audit refreshes those
locks, temporarily applies any reported consumer-cache hash, runs the complete matrix, and reports dependency changes
for review. A separate lock check rejects stale install metadata for Apocrypha, Yumemi's development head, and Laravel
Validation's development head. The fixture's `verify.php` script then reflects every annotated class, method, parameter,
and property before PHPStan runs. Add reflection assertions whenever a stub gains coverage; a missing or renamed
upstream declaration must fail before semantic assertions run.

Laravel 11 has no tagged framework release outside its known advisory ranges. Its temporary consumer projects disable
Composer's security blocking because they install source only for reflection and static analysis; Composer still reports
the advisories, and this is not a recommendation to run an affected release. Exact historical cutover profiles use the
same policy while pinned to the reviewed tag. The lock check rejects a development-branch resolution so Composer cannot
silently substitute a development branch for a reviewed release; only Apocrypha's local package under test and packages
used by explicit development-head profiles are whitelisted.

Preserve the upstream type structure exactly and change only the unit-bearing scalar. In particular, retain integer
ranges and literal types when upstream publishes them, but do not infer a narrower type merely because the
implementation normally produces or accepts values in that range:

| Upstream type      | Unit-bearing replacement            |
| ------------------ | ----------------------------------- |
| `int`              | `unit_int<'second'>`                |
| `int<0, max>`      | `unit_int<'byte'>&int<0, max>`      |
| `non-negative-int` | `unit_int<'byte'>&non-negative-int` |
| `3`                | `3&unit_int<'meter'>`               |

Apply the same replacement within arrays, callbacks, unions, and nullable types. Preserve every ordinary fallback,
sentinel, and non-unit alternative so erasing the Yumemi brand reproduces the upstream PHPDoc.

The Laravel framework fixture separately verifies every Illuminate integration when Composer represents its component
package as an exact replacement rather than a directly installed package. Each Illuminate fixture also runs with
Larastan 3. In that mode Larastan owns the upstream declarations, while Apocrypha reproduces only its unit semantics
through rules and type extensions.

Every integration has a `test-consumer-*` Make target. Version variables are listed at the top of `Makefile`. Examples:

```shell
GETID3_VERSION=2 make test-consumer-getid3
GUZZLE_MAJOR=8 make test-consumer-guzzle
ILLUMINATE_AUTH_MAJOR=12 make test-consumer-illuminate-auth
ILLUMINATE_BUS_MAJOR=12 make test-consumer-illuminate-bus
ILLUMINATE_CACHE_MAJOR=12 make test-consumer-illuminate-cache
ILLUMINATE_QUEUE_MAJOR=12 make test-consumer-illuminate-queue
ILLUMINATE_ROUTING_MAJOR=12 make test-consumer-illuminate-routing
INTERVENTION_IMAGE_VERSION=3 make test-consumer-intervention-image
LARAVEL_FRAMEWORK_MAJOR=12 make test-consumer-laravel-framework
MEASUREMENTS_VERSION=1 make test-consumer-measurements
PHPGEO_MAJOR=6 make test-consumer-phpgeo
SYMFONY_STOPWATCH_MAJOR=7 make test-consumer-symfony-stopwatch
```

Set `ILLUMINATE_COMPATIBILITY_MODE=larastan` on any Illuminate or Laravel framework command to exercise coexistence with
Larastan. This switches most integrations from standalone stubs to the adapter; Illuminate Bus, Database, and Routing
already use the adapter in plain mode.

Set `ILLUMINATE_COMPATIBILITY_MODE=phpstan-laravel-validation` on the Illuminate Validation command to install Larastan
and `jbboehr/phpstan-laravel-validation` together. This profile verifies Apocrypha's unit diagnostics and the validation
extension's inferred output type in the same analysis.

Set `SYMFONY_COMPATIBILITY_MODE=phpstan-symfony` on a Symfony HttpFoundation or Stopwatch command to exercise direct
coexistence with `phpstan/phpstan-symfony` 2. The compatibility profile installs and autodiscovers both extensions, then
runs the same reflection, valid-case, invalid-case, explicit-selection, and autodetection checks as plain mode.

Laravel 13 and Intervention Image 4 require PHP 8.3 or later, and Symfony Stopwatch 8 requires PHP 8.4.1 or later.
Guzzle 7 and 8 and Intervention Image 3 run on the repository's PHP 8.2 baseline. The CI matrix selects a compatible PHP
version automatically.

## Verify Package Behavior

Each fixture contains accepted calls and deliberately invalid plain, differently scaled, and dimensionally incompatible
values. Expected diagnostic fragments should identify the exact branded boundary. The consumer harness checks them in
both directions: every expected fragment must occur, every emitted file diagnostic must match a declared fragment, and
general PHPStan errors fail the fixture. One fragment may deliberately cover repeated equivalent diagnostics. Test
explicit selection and autodetection against the same cases.

Illuminate stubs are normally the canonical standalone representation. Keep the metadata catalog in exact parity with
their `@yumemi-param`, `@yumemi-return`, and `@yumemi-var` tags, including every major restriction. The parity test is a
guardrail, not a substitute for real consumer coverage: verify positional and named calls, finite unpacking, property
reads and writes, return precision, and both direct-package and `laravel/framework` installs. Unknown unpack positions
must not produce a speculative `apocrypha.unit` diagnostic.

Illuminate Database always uses the metadata adapter because Laravel changed callback and event-constructor PHPDoc
within supported majors. Keep its retained profile stubs as reviewable unit-semantic references, never enable them
alongside the adapter, and verify that modern upstream callback types remain enforced in plain mode.

Illuminate Bus always uses the metadata adapter because PHPStan does not propagate a trait stub's PHPDoc onto arbitrary
job classes. Keep both retained progress profiles as unit-semantic references, never enable them alongside the adapter,
and test method and property boundaries through direct use of `Queueable` and through an inherited Laravel wrapper
trait.

Illuminate Routing also always uses the metadata adapter because a partial `Route` declaration would hide most of its
upstream method surface, while Larastan supplies additional `Route` PHPDoc of its own. Keep its retained stub as a
reviewable unit-semantic reference, never enable it alongside the adapter, and exercise an unrelated upstream route
method in both compatibility modes.

Carbon always uses the metadata adapter because a partial `CarbonInterface` stub can replace unrelated upstream methods.
Keep its retained profile stubs as reviewable semantic references, but never enable them alongside the adapter. Every
profile must chain a branded fixed-duration call into an unrelated upstream method and assert the concrete receiver
type.

Measurements also always uses the metadata adapter because a partial `Length` or `Duration` stub would replace the
complete upstream magic-factory list. Keep `stubs/measurements/measurements.stub` as a reviewable semantic reference,
never enable it alongside the adapter, and keep `usesUnitBoundaryAdapter()` true for `nmarfurt/measurements`.

Intervention Image always uses the metadata adapter because partial declarations of its central manager and image types
would hide unrelated upstream methods. Keep the v3 and v4 reference stubs in exact parity with the metadata catalog,
never enable either alongside the adapter, and verify both interface-typed and concrete `Image` calls.

Symfony compatibility currently keeps Apocrypha's stubs enabled because `phpstan/phpstan-symfony` 2 does not register
the same declarations for supported HttpFoundation and Stopwatch releases. Exercise every supported Symfony major in
both modes so a change in its version-conditional stub loader cannot pass unnoticed.

Supported matrix entries exercise Composer archives, while conventional CI separately builds and verifies the Git
archive from a real checkout. Both payload checks must confirm that runtime source, NEON entry points, legal notices,
and stubs are present while tests, local state, and development tooling are excluded.

Before tagging a release, run `composer release:check`. The release gate assigns the built Composer archive a
prospective stable version, installs it into clean stable-minimum projects, and confirms that Composer selects a tagged
Yumemi `0.1` release. It exercises extension-installer with Symfony Stopwatch, manual PHPStan registration with
Illuminate Cache, and Laravel with Larastan. Set `APOCRYPHA_PACKAGE_VERSION` when preparing a version other than the
default prospective `0.1.0`.

Source-mode consumers use Composer's default symlinked path-repository layout; archive-mode consumers use a mirrored
path. Keep both modes because package-relative NEON includes and registered stub paths can behave differently after
symlink resolution. A source-mode consumer must confirm that returned stub paths remain beneath its own `vendor/` tree;
resolving them to the external checkout can make PHPStan validate a different package boundary or fail during stub
validation.

Every fenced PHP example in `README.md` and `docs/pages/**` must have a `yumemi-example` marker and an entry in the
public-documentation verification manifest. Examples that use third-party APIs belong to the corresponding isolated
consumer; the harness resolves their source document through that manifest before Akashi extracts the authored fence. Do
not copy an example into a fixture or declare a consumer route without wiring that marker into the consumer harness.
Route a future dependency-free example through a root runtime or PHPStan test instead of assigning it to an unrelated
consumer. Write `//!` expectations on their own line so Akashi can parse them when a verification path uses its PHPStan
adapter.

## Add An Integration

1. Add the package and finite verified-major list to `SUPPORTED_INTEGRATIONS`; record a package-specific minimum when
   only part of a major is compatible, and select major-specific files when verified signatures differ.
2. Add its stub beneath `stubs/<vendor>/` with ordinary fallback PHPDoc and structurally matching `@yumemi-*` tags.
3. Add parser tests for promoted parameter, return, property, callback, collection, and union forms used by the stub.
4. For an extension-backed integration, mirror every promoted tag in `PackageIntegrationUnitBoundaryMetadata`, register
   every class with a return boundary in `apocrypha.neon`, and exercise the consumer in both standalone and Larastan
   modes.
5. Add an isolated Composer consumer with upstream reflection, accepted calls, rejected calls, and autodetection. Use
   the generic consumer path unless the integration needs documentation extraction or another package-specific step.
6. Add every verified major to CI with a PHP version supported by that upstream release. If the integration adds runtime
   source, add that source to the Composer archive test's required-path whitelist.
7. Document exact units, alternatives, limitations, and verification snapshots.

## Compatibility Decisions

The matrix records what is currently verified; it does not promise automatic support for all future majors. Add a new
major only after the complete fixture passes. Removing a previously released major or materially changing an annotation
requires an explicit compatibility decision and, after the first tag, a changelog entry.

For Larastan, coexistence is an integration-wide choice rather than a declaration overlay. Loading even an apparently
nonoverlapping stub can collide with a class Larastan adds in a later minor release. When a selected Illuminate
integration and supported Larastan are both installed, disable all Apocrypha stubs for that integration and use the
metadata adapter. Illuminate Bus, Database, and Routing make the same whole-integration choice without Larastan so
upstream PHPDoc remains authoritative in both modes. Reject an unknown Larastan major until the complete matrix has been
verified.

For `phpstan/phpstan-symfony`, direct coexistence remains acceptable only while the combined consumer matrix proves that
its registered declarations do not overlap the selected Apocrypha integration. If an extension release begins owning any
declaration in that integration, disable the complete integration stub set and reproduce the unit boundaries with a
metadata adapter; do not retain a partial nonoverlapping stub subset.

[Documentation index](../) · [Repository README](https://github.com/jbboehr/yumemi-apocrypha.php)
