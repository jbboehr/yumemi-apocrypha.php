<p align="center">
  <img src="docs/pages/images/yumemi-banner.png" alt="Yumemi" width="960">
</p>

# Yumemi Apocrypha

Curated PHPStan unit annotations for third-party PHP packages, built on [Yumemi](https://github.com/jbboehr/yumemi.php).

The package is under active development and has no tagged release. It currently covers selected unit-bearing APIs in
Guzzle 7 and 8; Illuminate Cache, Cookie, Filesystem, HTTP, Process, Queue, and Support across Laravel 11, 12, and 13;
phpgeo 4, 5, and 6; and Symfony Stopwatch across majors 6, 7, and 8.

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
bundled integration declarations describe Guzzle, Laravel, phpgeo, and Symfony APIs; see the
[Guzzle MIT notice](docs/GUZZLE-COPYRIGHT), [Laravel MIT notice](docs/LARAVEL-COPYRIGHT),
[phpgeo MIT notice](docs/PHPGEO-COPYRIGHT), and [Symfony MIT notice](docs/SYMFONY-COPYRIGHT).
