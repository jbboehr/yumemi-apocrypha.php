# Getting Started

<figure class="logion" data-logion="OSD 69:22">
<div class="logion-text">
<blockquote>
<p>At the feast of first thunder, leave the highest stair bare; the rain shall write there the name that pride omitted.
Read it kneeling.</p>
</blockquote>
<p class="logion-citation">— <cite>Ordinances of the Synthetic Dawn 69:22</cite></p>
</div>
<img src="images/logia/OSD-69_22.webp" alt="A kneeling witness below a rain-lit ceremonial stair beneath ordered lightning" width="960" height="540" loading="eager" fetchpriority="high">
</figure>

Install Apocrypha as a development dependency, select the upstream package whose unit-bearing boundaries PHPStan should
check, and confirm that an incorrectly scaled value is rejected.

## Installation

Yumemi has a tagged `0.1` release, while Apocrypha has not yet published its first release. Install Apocrypha's
development branch with PHPStan's extension installer; Composer installs the compatible Yumemi release transitively:

```shell
composer require --dev jbboehr/yumemi-apocrypha:dev-master phpstan/extension-installer
```

Use this command when the application references Yumemi only during analysis. If runtime code calls Yumemi functions or
classes, install `jbboehr/yumemi:^0.1` as a normal dependency first. Then install Apocrypha and the extension installer
with `--dev`.

## Automatic Registration

With `phpstan/extension-installer`, Composer registers both PHPStan extensions automatically. Apocrypha remains inert
until at least one integration is selected or autodetection is enabled.

For a non-interactive installation, whitelist the extension-installer plugin before installing:

```shell
composer config --no-plugins allow-plugins.phpstan/extension-installer true
```

`phpstan/extension-installer` discovers every participating extension. Remove any manual include for an extension it
discovers, or that extension loads twice. To keep existing manual includes, skip the installer and follow
[Manual Registration](#manual-registration).

With Larastan 3 installed, selected Illuminate integrations need no additional configuration. Apocrypha preserves
Larastan's declarations and adds unit-bearing boundaries through PHPStan rules and type extensions.

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

If Composer's `vendor-dir` setting uses another directory, replace `vendor` in both include paths with that directory.

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
    //! Store::put() expects unit_int<'second'>, 1&unit_int<'minute'> given
    $cache->put('report', 'stale', unit(1, 'minute'));
}
```

Here `//!` marks the expected PHPStan diagnostic exercised by the documentation test; it is not Yumemi syntax. Run the
project's normal PHPStan command. Once the mismatch is reported, remove the rejected call and consult the
[integration reference](integrations.md) for the covered boundaries and their exact units.

## Troubleshooting

| Symptom                                       | What to check                                                                                                                                                                                  |
| --------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Composer refuses the extension installer      | Run the plugin-whitelist command under [Automatic Registration](#automatic-registration) before the non-interactive install.                                                                   |
| An extension or service appears to load twice | Use automatic or manual registration, not both. Remove manual includes for extensions discovered by `phpstan/extension-installer`.                                                             |
| A selected integration rejects its package    | Confirm the precise integration key is installed directly or supplied by an exact Composer replacement, and that its version appears in the [verified matrix](integrations.md#version-policy). |
| A wrapper loses a branded option type         | Type the wrapper parameter with the required Yumemi native type, or construct the branded value before placing it in a generic options array.                                                  |

[Documentation index](./) · [Repository README](https://github.com/jbboehr/yumemi-apocrypha.php)
