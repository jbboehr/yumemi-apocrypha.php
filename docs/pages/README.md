{{#title Yumemi Apocrypha - Curated PHPStan unit integrations}}

<img src="images/yumemi-banner.png" alt="Iudex Mensurarum Mysticarum Yumemi Apocrypha" width="2172" height="724" loading="eager" fetchpriority="high">

# Yumemi Apocrypha

[Yumemi](https://github.com/jbboehr/yumemi.php) defines branded native values; Yumemi Apocrypha maps them onto verified
third-party APIs. Apocrypha supplies PHPStan metadata only: upstream packages keep their runtime behavior, and branded
native values remain ordinary PHP scalars. Apocrypha owns integration selection and version policy; Yumemi owns unit
semantics and optional `@yumemi-*` tag promotion.

## Start Here

- [Getting Started](getting-started.md) installs the package, activates an integration, and verifies a deliberate unit
  mistake.
- [Integrations](integrations.md) lists the covered API areas, exact units, versions, and limitations.
- [Maintaining Integrations](contributing/maintaining-integrations.md) describes the upstream verification workflow.
