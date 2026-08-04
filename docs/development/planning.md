# Planning

Yumemi Apocrypha is the separately versioned home for curated unit-aware integrations with third-party PHP packages. Its
primary maintenance obligation is to keep every enabled stub structurally compatible with the upstream versions it
claims to support and semantically accurate about the represented units.

## Initial Milestone

- Provide explicit and optional automatic integration selection.
- Reject stale integrations by default when an installed package major has not been verified.
- Migrate the existing `illuminate/cache` and `illuminate/http` integrations for Laravel 11 through 13.
- Verify every supported major through isolated Composer consumer projects.
- Keep Yumemi's generic `@yumemi-*` annotation mechanism in the core package.

## Maintenance Policy

The supported-version table is descriptive rather than a promise to support every future or historical framework major.
New majors should normally be added after their reflected signatures and PHPStan behavior pass the complete consumer
suite. Integration scope remains curated: add APIs only when their physical unit is stable, useful, and unambiguous.

## Future Candidates

Evaluate new integrations from observed application value rather than package breadth. Current candidates include
duration boundaries in `illuminate/support`, `illuminate/process`, and `illuminate/queue`; each requires a version and
semantic review before implementation.
