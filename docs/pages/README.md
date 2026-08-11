{{#title Yumemi Apocrypha - Curated PHPStan unit integrations}}

<img src="images/yumemi-banner.png" alt="Iudex Mensurarum Mysticarum Yumemi Apocrypha" width="2172" height="724" loading="eager" fetchpriority="high">

# Yumemi Apocrypha

Yumemi Apocrypha extends [Yumemi](https://github.com/jbboehr/yumemi.php)'s PHPStan unit checking to verified third-party
APIs. It supplies analysis metadata only: upstream packages retain their runtime behavior, and branded native values
remain ordinary PHP scalars. Third-party dependencies and integration release policy remain outside Yumemi's core
package.

## Start Here

- [Getting Started](getting-started.md) installs the package, activates an integration, and verifies a deliberate unit
  mistake.
- [Integrations](integrations.md) lists the exact annotated APIs, units, versions, and limitations.
- [Maintaining Integrations](contributing/maintaining-integrations.md) describes the upstream verification workflow.
