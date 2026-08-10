# BookStack Application Smoke Test

This report records an application-scale installation and analysis of Yumemi Apocrypha against BookStack. It complements
the isolated consumer matrix by exercising package discovery, PHPStan extension registration, Larastan coexistence,
application wrappers, Laravel facades and helpers, and unrelated methods on classes affected by package stubs.

## Subject and Result

| Item                  | Value                                                                                                                                  |
| --------------------- | -------------------------------------------------------------------------------------------------------------------------------------- |
| Application           | [BookStack](https://codeberg.org/BookStack/bookstack)                                                                                  |
| Upstream revision     | [`c813c1b3628c0b6bd757c12cadaa56f50724117d`](https://codeberg.org/BookStack/bookstack/commit/c813c1b3628c0b6bd757c12cadaa56f50724117d) |
| Upstream branch       | `development`                                                                                                                          |
| Revision date         | 2026-07-30                                                                                                                             |
| PHP requirement       | `^8.2.0`                                                                                                                               |
| Framework             | Laravel `v12.64.0`                                                                                                                     |
| Static analysis       | Larastan `v3.10.0`; PHPStan updated from `2.2.6` to `2.2.8` during dependency resolution                                               |
| Other integrations    | Carbon `3.13.1`, Guzzle `7.15.2`, Symfony HttpFoundation `7.4.14`                                                                      |
| Baseline result       | BookStack passed PHPStan before Apocrypha was installed                                                                                |
| Final adoption result | BookStack passed PHPStan after four cache durations were branded and the unsafe Carbon integration was excluded                        |

BookStack was chosen because it is a large, maintained Laravel application with existing Larastan configuration and real
uses of Carbon, Guzzle, Illuminate components, and Symfony HttpFoundation. The test used an ephemeral clone and did not
commit or publish changes to BookStack.

## Method

### Establish the baseline

The checkout was installed without Composer scripts, then analyzed using BookStack's existing configuration:

```console
composer install --no-interaction --no-progress --no-scripts
vendor/bin/phpstan analyse --memory-limit=2G --no-progress --error-format=raw
```

The baseline analysis completed without diagnostics. BookStack's `phpstan.neon.dist` used level 4, analyzed `app/`,
bootstrapped `bootstrap/phpstan.php`, and manually included Larastan's `extension.neon`.

### Install the development packages

A Composer path repository pointed at the Apocrypha working tree. The first installation attempt used the minimum
package set:

```console
composer require --dev jbboehr/yumemi-apocrypha:dev-develop \
    phpstan/extension-installer:^1.4 --with-all-dependencies \
    --no-scripts --no-interaction --no-progress
```

Composer rejected the transitive `jbboehr/yumemi` development version under BookStack's stable minimum stability. The
successful retry explicitly made both development packages root requirements:

```console
composer require --dev jbboehr/yumemi:dev-master \
    jbboehr/yumemi-apocrypha:dev-develop phpstan/extension-installer:^1.4 \
    --with-all-dependencies --no-scripts --no-interaction --no-progress
```

This is consistent with the current [installation guide](../pages/getting-started.md): an untagged development install
must explicitly require Yumemi as well as Apocrypha.

Two setup problems then had to be resolved before application analysis could begin:

1. Composer's extension installer registered Larastan automatically, so BookStack's manual Larastan include loaded the
   same NEON file twice. Removing the manual include resolved the duplicate.
2. Composer installed the Apocrypha path repository as a symlink. Apocrypha's relative Yumemi include then resolved next
   to the source checkout instead of beneath BookStack's `vendor/` directory. Reinstalling the path package as a mirror
   reproduced the layout of a distribution install and allowed PHPStan to start:

   ```console
   COMPOSER_MIRROR_PATH_REPOS=1 composer reinstall jbboehr/yumemi-apocrypha \
       --prefer-dist --no-scripts --no-interaction --no-progress
   ```

With registration working and no integration selected, BookStack again passed PHPStan. This confirms that merely
installing Apocrypha remained inert and did not conflict with Larastan.

### Exercise selection and adoption

Enabling autodetection selected every applicable installed integration. A second run explicitly selected all applicable
integrations except Carbon:

```neon
parameters:
    yumemiApocrypha:
        packages:
            - guzzlehttp/guzzle
            - illuminate/cache
            - illuminate/cookie
            - illuminate/filesystem
            - illuminate/http
            - illuminate/process
            - illuminate/queue
            - illuminate/support
            - symfony/http-foundation
```

The explicit selection produced four intended diagnostics, all at Illuminate cache boundaries. The affected BookStack
values were already expressed in seconds at runtime; the findings were missing static brands, not evidence that
BookStack used the wrong scale.

The temporary adoption patch imported `jbboehr\Yumemi\unit` and branded these values:

- the login throttling decay time after conversion from minutes to seconds;
- the OIDC discovery cache duration after conversion from minutes to seconds;
- two one-day custom-head cache durations, each expressed as `unit(86400, 'second')`.

Because BookStack application code then called `unit()` at runtime, `jbboehr/yumemi` was moved from `require-dev` to
`require`. Apocrypha and the PHPStan extension installer remained development dependencies. Full PHPStan analysis passed
after these changes.

The final temporary checkout contained six modified files: the three application files named in the diagnostic
inventory, `composer.json`, `composer.lock`, and `phpstan.neon.dist`. No BookStack commit was created.

## Findings

### Carbon's partial interface stub hides valid methods

Severity: **release blocker for the Carbon integration**.

Autodetection produced this diagnostic in BookStack's trash cleanup logic:

```text
Call to an undefined method Carbon\CarbonInterface::subDays().
```

The valid upstream chain was equivalent to:

```php
Carbon::now()->addSeconds(10)->subDays($lifetime);
```

After `addSeconds()`, PHPStan saw the return as `CarbonInterface`. The selected Carbon 3 profile redeclares that
interface with only Apocrypha's fixed-duration methods, so the otherwise valid `subDays()` surface disappeared. The
isolated Carbon consumers verify selected unit-bearing methods and profile cutovers, but do not currently prove that
unrelated upstream methods remain available through a branded call chain.

This is not a BookStack defect. It shows that a partial class or interface stub is unsafe for Carbon's large, partly
magic API. Carbon should not be advertised as application-ready until Apocrypha either:

- replaces the partial stubs with metadata-driven argument and return extensions that preserve Carbon's declarations;
- or demonstrates another independently maintainable representation of the complete upstream surface.

The first direction matches the Larastan compatibility design already used for Illuminate packages and avoids copying
Carbon's complete API into this repository.

### Symlinked Composer path repositories break the Yumemi include

Severity: **high for contributors and local consumers; distribution installs are unaffected**.

The package entry point currently includes Yumemi with a package-relative path from
[`extension.neon`](../../extension.neon). When the path package is symlinked, NEON resolves the parent traversal from
the real source checkout and looks for `yumemi/yumemi-tags.neon` beside Apocrypha rather than in the consuming project's
`vendor/jbboehr/` directory.

The isolated consumer harness installs path repositories as mirrors, so it does not exercise Composer's default
symlinked layout. A fix should use a consumer-root-relative reference, if PHPStan's configuration expansion supports it,
and add an explicit symlinked path-repository test. The mirror workaround proves the rest of the integration but should
not be the documented development workflow.

### Extension Installer and manual Larastan registration collide

Severity: **documentation and onboarding issue**.

BookStack manually included Larastan before `phpstan/extension-installer` was installed. The installer then registered
Larastan a second time and PHPStan stopped with a duplicate-include error. Existing Larastan applications therefore need
one of two explicit migration paths:

- install the extension installer and remove manual includes for extensions it discovers; or
- keep manual registration and include Apocrypha manually instead of adding the installer.

The getting-started documentation explains automatic and manual Apocrypha registration independently, but does not yet
call out this common transition for an established PHPStan project.

### Cache facades and the global helper bypass the Laravel adapter

Severity: **medium coverage gap**.

A controlled temporary probe called the same cache method through three common surfaces:

```php
$repository->put('repository', 'value', 60);
Cache::put('facade', 'value', 60);
cache()->put('helper', 'value', 60);
```

Apocrypha diagnosed the repository-typed call only. The Larastan compatibility adapter examines the receiver visible in
the AST. A facade remains `Illuminate\Support\Facades\Cache`, while the helper's inferred union is not accepted by the
adapter's exact receiver match. Direct contract, repository, store, and lock coverage works as documented, but the two
idiomatic Laravel entry points are currently false negatives.

The adapter should gain explicit, tested facade resolution and a sound strategy for helper-return unions. Until then,
documentation should avoid implying that every Laravel spelling of a covered boundary is enforced.

### Generic application wrappers can erase option-key precision

Severity: **known propagation limitation**.

BookStack passes an integer timeout into an application wrapper that merges it into Guzzle options before constructing a
client. No diagnostic was produced at the wrapper parameter. The open Guzzle options shape correctly avoids sealing out
unrelated keys, but PHPStan loses the constant-key relationship through the generic `array_merge()` path.

This is not necessarily a defect in the Guzzle integration: the third-party boundary no longer has enough static
information to recover the originating scalar. Applications can preserve the guarantee by branding the wrapper parameter
or calling `unit()` before building the options array. A documentation recipe for unit-aware wrappers would make this
limitation easier to handle.

### The non-Carbon integration set worked at application scale

Severity: **positive evidence**.

With Carbon excluded, the entire applicable integration set coexisted with Larastan and introduced only four intended
cache diagnostics. Branding those durations made full BookStack analysis clean. No compatibility regression appeared
from Guzzle, Illuminate Cookie, Filesystem, HTTP, Process, Queue, or Support, or Symfony HttpFoundation.

This result does not prove every boundary in those integrations, but it demonstrates successful installation, selection,
analysis, and adoption in a substantial Laravel 12 application.

## Diagnostic Inventory

| BookStack location                                 | Boundary                               | Result                                   |
| -------------------------------------------------- | -------------------------------------- | ---------------------------------------- |
| `app/Access/Controllers/ThrottlesLogins.php:31`    | `RateLimiter::hit()` decay seconds     | Intended unbranded-value diagnostic      |
| `app/Access/Oidc/OidcProviderSettings.php:101`     | `Repository::remember()` cache seconds | Intended unbranded-value diagnostic      |
| `app/Theming/CustomHtmlHeadContentProvider.php:28` | `Repository::remember()` cache seconds | Intended unbranded-value diagnostic      |
| `app/Theming/CustomHtmlHeadContentProvider.php:45` | `Repository::remember()` cache seconds | Intended unbranded-value diagnostic      |
| `app/Entities/Tools/TrashCan.php:324`              | Unrelated `CarbonInterface::subDays()` | Incorrect regression from Carbon profile |

Explicitly selecting the nine non-Carbon integrations removed only the Carbon regression and retained the four intended
cache diagnostics.

## Validation Performed

| Check                                                             | Result                                                             |
| ----------------------------------------------------------------- | ------------------------------------------------------------------ |
| Dependency installation without Composer scripts                  | Passed                                                             |
| Baseline BookStack PHPStan analysis                               | Passed                                                             |
| Apocrypha installed but no integration selected                   | Passed after registration setup was corrected                      |
| Autodetection with all installed integrations                     | Failed with four intended cache findings and one Carbon regression |
| Explicit selection of every applicable integration except Carbon  | Produced only the four intended cache findings                     |
| Full PHPStan after branding all four cache durations              | Passed                                                             |
| PHP_CodeSniffer on the three modified BookStack application files | Passed                                                             |
| `git diff --check` in the temporary BookStack checkout            | Passed                                                             |
| Runtime BookStack PHPUnit suite                                   | Not run; this exercise targeted installation and static analysis   |
| Public Packagist installation                                     | Not run; the test used local development packages                  |

Composer reported seven security advisories affecting two packages after dependency resolution. They belonged to the
temporary BookStack dependency graph, were not introduced or investigated as part of this static-analysis exercise, and
do not alter the Apocrypha findings above.

## Recommended Actions

1. Replace or redesign the Carbon partial stubs so branded methods preserve the complete upstream method surface. Add a
   consumer regression that chains an annotated fixed-duration method into an unrelated real or magic Carbon method.
2. Make [`extension.neon`](../../extension.neon) safe under Composer's default symlinked path repositories and add a
   symlink-mode consumer case alongside the existing mirror and archive cases.
3. Document how automatic extension discovery interacts with an application's existing manual Larastan include.
4. Extend the Larastan adapter to cover facades and the global cache helper, with valid and invalid consumer cases for
   both surfaces.
5. Add a wrapper recipe showing how an application can retain a unit brand across generic option-array construction.
6. Repeat an application-scale smoke test after the first four items, and retain the exact upstream revision and
   dependency versions in the resulting report.

The Carbon fix is the only finding that invalidates an enabled integration's ordinary upstream API. It should be
resolved before the first public release, or Carbon should be removed from the advertised integration set until it is
safe.
