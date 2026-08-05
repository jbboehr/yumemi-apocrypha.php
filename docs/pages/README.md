{{#title Yumemi Apocrypha - Curated PHPStan unit integrations}}

# Yumemi Apocrypha

Yumemi Apocrypha supplies curated PHPStan unit annotations for third-party PHP packages. It extends
[Yumemi](https://github.com/jbboehr/yumemi.php) at framework and library boundaries without adding those dependencies to
Yumemi's core package.

The current integrations cover selected getID3 1.x and 2.x media measurements, Guzzle request and transfer boundaries,
unit-bearing Illuminate APIs for verified Laravel releases, phpgeo distance and bearing calculations, and duration and
memory results from verified Symfony Stopwatch releases.

## Start Here

- [Getting Started](getting-started.md) installs the package, activates an integration, and verifies a deliberate unit
  mistake.
- [Integrations](integrations.md) lists the exact annotated APIs, units, versions, and limitations.
- [Maintaining Integrations](contributing/maintaining-integrations.md) describes the upstream verification workflow.

Apocrypha changes PHPStan's understanding of third-party calls only. It does not wrap those calls or convert their
arguments at runtime.
