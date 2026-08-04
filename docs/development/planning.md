# Planning

Yumemi Apocrypha is the separately versioned home for curated unit-aware integrations with third-party PHP packages. Its
primary maintenance obligation is to keep every enabled stub structurally compatible with the upstream versions it
claims to support and semantically accurate about the represented units.

## Current Scope

- Explicit integration selection and optional Composer-package autodetection are implemented.
- Explicit selections and strict autodetection reject installed package majors that have not been verified.
- `illuminate/cache` and `illuminate/http` cover their shared unit-bearing APIs across Laravel 11 through 13.
- Isolated Composer consumers verify every supported major, automatic and manual PHPStan registration, source installs,
  and a representative Composer archive.
- Yumemi's generic `@yumemi-*` annotation mechanism remains in the core package; Apocrypha owns only package-specific
  stubs and their selection policy.

## Maintenance Policy

The supported-version table is descriptive rather than a promise to support every future or historical framework major.
New majors should normally be added after their reflected signatures and PHPStan behavior pass the complete consumer
suite. Integration scope remains curated: add APIs only when their physical unit is stable, useful, and unambiguous.

## Future Candidates

Evaluate new integrations from observed application value rather than package breadth. Current candidates include
duration boundaries in `illuminate/support`, `illuminate/process`, and `illuminate/queue`; each requires a version and
semantic review before implementation.
