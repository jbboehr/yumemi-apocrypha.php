<p align="center">
  <img src="docs/pages/images/yumemi-banner.png" alt="Yumemi" width="960">
</p>

# Yumemi Apocrypha

Curated PHPStan unit annotations for third-party PHP packages, built on [Yumemi](https://github.com/jbboehr/yumemi.php).

The package is under active development and has no tagged release. It currently covers selected unit-bearing APIs in:

| Composer package        | Supported versions                  |
| ----------------------- | ----------------------------------- |
| `james-heinrich/getid3` | 1.9.22+ in 1.x; 2.0.0-beta6+ in 2.x |
| `guzzlehttp/guzzle`     | 7, 8                                |
| `illuminate/cache`      | 11, 12, 13                          |
| `illuminate/cookie`     | 11, 12, 13                          |
| `illuminate/filesystem` | 11, 12, 13                          |
| `illuminate/http`       | 11, 12, 13                          |
| `illuminate/process`    | 11, 12, 13                          |
| `illuminate/queue`      | 11, 12, 13                          |
| `illuminate/support`    | 11, 12, 13                          |
| `mjaschen/phpgeo`       | 4, 5, 6                             |
| `symfony/stopwatch`     | 6, 7, 8                             |

```shell
composer require --dev jbboehr/yumemi:dev-master jbboehr/yumemi-apocrypha:dev-master \
    phpstan/extension-installer
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
    $cache->put('report', 'ready', unit(1, 'minute')); // PHPStan rejects minutes at this seconds boundary.
}
```

## Documentation

See the [documentation index](docs/pages/README.md) for installation, integration coverage, and maintenance policy.

## License

Yumemi Apocrypha is licensed under `AGPL-3.0-only WITH romic-exception`. See [LICENSE.md](LICENSE.md) and the
[Romic Exception](docs/LICENSE_EXCEPTION.md). Contributions follow the terms in [CONTRIBUTING.md](CONTRIBUTING.md). The
bundled integration declarations describe third-party APIs; see the [third-party notices](docs/THIRD_PARTY_NOTICES.md).
