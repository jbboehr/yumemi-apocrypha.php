# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to
[Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.1.0] - 2026-08-14

### Added

- Initial PHPStan extension for PHP `^8.2` and PHPStan `^2.2.5`, using Yumemi `^0.1` branded native types to describe
  third-party unit-bearing boundaries without runtime wrappers or conversion.
- Unit-aware integrations for Carbon, getID3, Guzzle, Intervention Image, phpgeo, Measurements, Symfony HttpFoundation
  and Stopwatch, and Laravel 11–13 Cache, Cookie, Database, Filesystem, HTTP, Process, Queue, Redis, Routing, Session,
  Support, and Validation APIs.
- Explicit integration selection and optional strict Composer-package autodetection, including verified-version
  enforcement and Illuminate component discovery through `laravel/framework` replacements.
- Version-aware stubs and metadata adapters that preserve upstream alternatives, ranges, nullability, and unrelated
  declarations while adding package-specific unit diagnostics.
- Automatic coexistence with Larastan 3, plus verified compatibility profiles for `phpstan-laravel-validation` and
  `phpstan/phpstan-symfony` 2.
- Automatic registration through `phpstan/extension-installer`, manual `extension.neon` registration, and public
  documentation examples verified against the real upstream packages.

[Unreleased]: https://github.com/jbboehr/yumemi-apocrypha.php/compare/v0.1.0...HEAD
[0.1.0]: https://github.com/jbboehr/yumemi-apocrypha.php/releases/tag/v0.1.0
