# Getting Started

Install Apocrypha as a development dependency, select the upstream package whose unit-bearing boundaries PHPStan should
check, and confirm that an incorrectly scaled value is rejected.

## Installation

Yumemi and PHPStan are required by Apocrypha. While neither project has a tagged release, install both development
branches explicitly:

```shell
composer require --dev jbboehr/yumemi:dev-master jbboehr/yumemi-apocrypha:dev-master \
    phpstan/extension-installer
```

This is appropriate when an application uses Yumemi only during analysis. If application code calls Yumemi runtime
functions or classes, install `jbboehr/yumemi:dev-master` as a normal dependency first, then install Apocrypha and the
extension installer with `--dev`.

## Automatic Registration

With `phpstan/extension-installer`, Composer registers both PHPStan extensions automatically. Apocrypha remains inert
until at least one integration is selected or autodetection is enabled.

## Select Integrations

Explicit selection is the default and most predictable mode:

```neon
parameters:
    yumemiApocrypha:
        integrations:
            - illuminate/cache
            - illuminate/http
```

An explicitly selected package must be installed, either directly or as an exact Composer replacement, and within its
verified major-version matrix. Otherwise PHPStan stops with a configuration error rather than applying a missing or
stale declaration. A Laravel application that installs `laravel/framework` may select the corresponding `illuminate/*`
integrations without installing those component packages separately.

## Autodetect Integrations

Autodetection activates every directly installed or exactly replaced package known to Apocrypha:

```neon
parameters:
    yumemiApocrypha:
        autoDetect: true
```

Autodetection is strict by default. If a known package is installed at an unverified or unparseable version, analysis
stops. Applications that prefer to lose that integration silently may opt out:

```neon
parameters:
    yumemiApocrypha:
        autoDetect: true
        strictAutoDetect: false
```

Explicit selections remain strict even in this mode. Explicit and autodetected integrations form one deduplicated set.

## Manual Registration

Without `phpstan/extension-installer`, include both entry points:

```neon
includes:
    - vendor/jbboehr/yumemi/extension.neon
    - vendor/jbboehr/yumemi-apocrypha/extension.neon

parameters:
    yumemiApocrypha:
        integrations:
            - illuminate/cache
```

Apocrypha's entry point enables Yumemi's opt-in `@yumemi-*` tag promotion. That integration replaces internal PHPStan
parser services and may conflict with another extension replacing the same services.

## Verify Analysis

Add a deliberate mismatch to code using an annotated upstream API:

<!-- yumemi-example: getting-started-invalid -->

```php
<?php

use Illuminate\Contracts\Cache\Store;

use function jbboehr\Yumemi\unit;

function verifyApocryphaInstallation(Store $cache): void
{
    $cache->put('report', 'ready', unit(30, 'second'));
    $cache->put('report', 'stale', unit(1, 'minute')); // PHPStan rejects this seconds boundary.
}
```

Run the project's normal PHPStan command. Once the mismatch is reported, remove the rejected call and consult the
[integration reference](integrations.md) for the complete annotated surface.

[Documentation index](./) · [Repository README](https://github.com/jbboehr/yumemi-apocrypha.php)
