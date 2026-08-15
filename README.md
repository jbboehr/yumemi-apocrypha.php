<p align="center">
  <img src="docs/pages/images/yumemi-banner.png" alt="Yumemi" width="960">
</p>

# Yumemi Apocrypha

[![Nix CI](https://github.com/jbboehr/yumemi-apocrypha.php/actions/workflows/nix.yml/badge.svg)](https://github.com/jbboehr/yumemi-apocrypha.php/actions/workflows/nix.yml)
[![Conventional CI](https://github.com/jbboehr/yumemi-apocrypha.php/actions/workflows/ci.yml/badge.svg)](https://github.com/jbboehr/yumemi-apocrypha.php/actions/workflows/ci.yml)
[![Built with Nix](https://img.shields.io/badge/built%20with-Nix-5277C3?logo=nixos&logoColor=white)](flake.nix)
[![License: AGPL-3.0-only WITH romic-exception](https://img.shields.io/badge/license-AGPL--3.0--only%20WITH%20romic--exception-blue.svg)](LICENSE.md)
[![AI burn](https://img.shields.io/endpoint?url=https%3A%2F%2Fgist.githubusercontent.com%2Fjbboehr%2F6cec1c1ceaaa57ec32e8488daa2b4499%2Fraw%2Fagent-badge.json&cacheSeconds=300)](https://github.com/arlegotin/agent-badge)

Curated PHPStan unit annotations for third-party PHP packages, built on [Yumemi](https://github.com/jbboehr/yumemi.php).

The public API remains provisional throughout the `0.x` release series.

```shell
composer require --dev jbboehr/yumemi-apocrypha:^0.1 phpstan/extension-installer
```

If Yumemi is used only during PHPStan analysis, the complete toolchain may remain in development dependencies. The
example below calls Yumemi's runtime `unit()` function; applications that do the same must install Yumemi as a normal
dependency:

```shell
composer require jbboehr/yumemi:^0.1
```

Select an integration in `phpstan.neon`:

```neon
parameters:
    yumemiApocrypha:
        integrations:
            - illuminate/cache
```

<!-- yumemi-example: readme-cache-invalid -->

```php
<?php

use Illuminate\Contracts\Cache\Store;

use function jbboehr\Yumemi\unit;

function cacheReportForOneMinute(Store $cache): void
{
    //! Store::put() expects unit_int<'second'>, 1&unit_int<'minute'> given
    $cache->put('report', 'ready', unit(1, 'minute'));
}
```

`//!` marks the expected PHPStan diagnostic used by documentation testing; it is not Yumemi syntax.

## Verified package compatibility

| Composer package                    | Supported versions                  |
| ----------------------------------- | ----------------------------------- |
| `nesbot/carbon`                     | 2.62.1+ in 2.x; 3.x                 |
| `james-heinrich/getid3`             | 1.9.22+ in 1.x; 2.0.0-beta6+ in 2.x |
| `guzzlehttp/guzzle`                 | 7, 8                                |
| `illuminate/auth`                   | 11, 12, 13                          |
| `illuminate/cache`                  | 11, 12, 13                          |
| `illuminate/cookie`                 | 11, 12, 13                          |
| `illuminate/database`               | 11, 12, 13                          |
| `illuminate/filesystem`             | 11, 12, 13                          |
| `illuminate/http`                   | 11, 12, 13                          |
| `illuminate/process`                | 11, 12, 13                          |
| `illuminate/queue`                  | 11, 12, 13                          |
| `illuminate/redis`                  | 11, 12, 13                          |
| `illuminate/routing`                | 11, 12, 13                          |
| `illuminate/session`                | 11, 12, 13                          |
| `illuminate/support`                | 11, 12, 13                          |
| `illuminate/validation`             | 11, 12, 13                          |
| `intervention/image`                | 3, 4                                |
| `laravel/framework` (provider only) | 11, 12, 13                          |
| `mjaschen/phpgeo`                   | 4, 5, 6                             |
| `nmarfurt/measurements`             | 1.4+ in 1.x                         |
| `symfony/http-foundation`           | 6.4+ in 6.x; 7.x; 8.x               |
| `symfony/stopwatch`                 | 6, 7, 8                             |

`laravel/framework` supplies the listed Illuminate packages through Composer replacements. Select the precise
`illuminate/*` integration name; no separate component installation is required.

## Documentation

Start with [Getting Started](docs/pages/getting-started.md), then consult the
[integration reference](docs/pages/integrations.md) for exact units, versions, and limitations. The
[documentation index](docs/pages/README.md) also links maintainer guidance.

## License

Yumemi Apocrypha is licensed under `AGPL-3.0-only WITH romic-exception`. See [LICENSE.md](LICENSE.md) and the
[Romic Exception](docs/LICENSE_EXCEPTION.md). Contributions follow the terms in [CONTRIBUTING.md](CONTRIBUTING.md). The
bundled integration declarations describe third-party APIs; see the [third-party notices](docs/THIRD_PARTY_NOTICES.md).
