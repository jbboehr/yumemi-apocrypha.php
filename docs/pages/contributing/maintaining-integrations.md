# Maintaining Integrations

Every integration must be checked against the real upstream package and covered by isolated PHPStan consumer tests.
Editing a stub until a synthetic test passes does not establish compatibility.

## Before Changing A Stub

Identify the upstream package, every major claimed by the loader, and the exact unit represented by each affected
parameter, return, or property. Prefer APIs whose physical interpretation is stable and unambiguous. Do not expand
coverage merely because a parameter name resembles a duration or size.

## Verify Upstream Signatures

The consumer fixture installs the latest compatible release of each selected major. Its `verify.php` script reflects
every annotated class, method, parameter, and property before PHPStan runs. Add reflection assertions whenever a stub
gains coverage; a missing or renamed upstream declaration must fail before semantic assertions run.

Run one major locally with:

```shell
ILLUMINATE_CACHE_MAJOR=12 make test-consumer-illuminate-cache
ILLUMINATE_QUEUE_MAJOR=12 make test-consumer-illuminate-queue
SYMFONY_STOPWATCH_MAJOR=7 make test-consumer-symfony-stopwatch
```

Laravel 13 requires PHP 8.3 or later, and Symfony Stopwatch 8 requires PHP 8.4.1 or later. The CI matrix selects a
compatible PHP version automatically.

## Verify Package Behavior

Each fixture contains accepted calls and deliberately invalid plain, differently scaled, and dimensionally incompatible
values. Expected diagnostics should identify the exact branded boundary. Test explicit selection and autodetection
against the same cases.

At least one supported matrix entry uses a Composer archive. Archive tests must verify that runtime source, NEON entry
points, legal notices, and stubs are present while tests and development tooling are excluded.

## Add An Integration

1. Add the package and finite verified-major list to `SUPPORTED_INTEGRATIONS`; select major-specific files when the
   verified signatures differ.
2. Add its stub beneath `stubs/<vendor>/` with ordinary fallback PHPDoc and structurally matching `@yumemi-*` tags.
3. Add parser tests for promoted parameter, return, property, callback, collection, and union forms used by the stub.
4. Add an isolated Composer consumer with upstream reflection, accepted calls, rejected calls, and autodetection. Use
   the generic consumer path unless the integration needs documentation extraction or another package-specific step.
5. Add every verified major to CI with a PHP version supported by that upstream release.
6. Document exact units, alternatives, limitations, and verification snapshots.

## Compatibility Decisions

The matrix records what is currently verified; it does not promise automatic support for all future majors. Add a new
major only after the complete fixture passes. Removing a previously released major or materially changing an annotation
requires an explicit compatibility decision and, after the first tag, a changelog entry.

[Documentation index](../) · [Repository README](https://github.com/jbboehr/yumemi-apocrypha.php)
