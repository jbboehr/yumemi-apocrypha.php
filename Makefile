.DEFAULT: all
.PHONY: all coverage-branch docs docs-check docs-serve test-consumer-carbon test-consumer-carbon-archive \
	test-consumer-getid3 test-consumer-getid3-archive \
	test-consumer-guzzle test-consumer-guzzle-archive \
	test-consumer-intervention-image test-consumer-intervention-image-archive \
	test-consumer-illuminate-auth test-consumer-illuminate-auth-archive test-consumer-illuminate-cache \
	test-consumer-illuminate-cache-archive test-consumer-illuminate-console test-consumer-illuminate-console-archive \
	test-consumer-illuminate-cookie test-consumer-illuminate-database test-consumer-illuminate-filesystem \
	test-consumer-illuminate-http test-consumer-illuminate-http-archive \
	test-consumer-illuminate-process \
	test-consumer-illuminate-queue \
	test-consumer-illuminate-redis \
	test-consumer-illuminate-routing \
	test-consumer-illuminate-session \
	test-consumer-illuminate-support test-consumer-illuminate-validation test-consumer-laravel-framework \
	test-consumer-laravel-framework-archive test-consumer-phpgeo test-consumer-phpgeo-archive \
	test-consumer-measurements test-consumer-measurements-archive \
	test-consumer-symfony-http-foundation test-consumer-symfony-http-foundation-archive \
	test-consumer-symfony-stopwatch test-consumer-symfony-stopwatch-archive \
	test-consumer-manual

BRANCH_COVERAGE_OUTPUT ?= coverage/branch
BRANCH_COVERAGE_SOURCE ?= src
BRANCH_COVERAGE_TESTS ?=
BRANCH_COVERAGE_XDEBUG_ERROR := Xdebug is not loaded; enter nix develop .\#xdebug.
CARBON_VERSION ?= 3
ILLUMINATE_AUTH_MAJOR ?= 12
ILLUMINATE_CACHE_MAJOR ?= 12
ILLUMINATE_CONSOLE_MAJOR ?= 12
ILLUMINATE_COMPATIBILITY_MODE ?= plain
GETID3_VERSION ?= 2
GUZZLE_MAJOR ?= 8
INTERVENTION_IMAGE_VERSION ?= 4
ILLUMINATE_COOKIE_MAJOR ?= 12
ILLUMINATE_DATABASE_MAJOR ?= 12
ILLUMINATE_FILESYSTEM_MAJOR ?= 12
ILLUMINATE_HTTP_MAJOR ?= 12
ILLUMINATE_PROCESS_MAJOR ?= 12
ILLUMINATE_QUEUE_MAJOR ?= 12
ILLUMINATE_REDIS_MAJOR ?= 12
ILLUMINATE_ROUTING_MAJOR ?= 12
ILLUMINATE_SESSION_MAJOR ?= 12
ILLUMINATE_SUPPORT_MAJOR ?= 12
ILLUMINATE_VALIDATION_MAJOR ?= 12
LARAVEL_FRAMEWORK_MAJOR ?= 12
MEASUREMENTS_VERSION ?= 1
PHPGEO_MAJOR ?= 6
SYMFONY_HTTP_FOUNDATION_VERSION ?= 7
SYMFONY_COMPATIBILITY_MODE ?= plain
SYMFONY_STOPWATCH_MAJOR ?= 7

all:

test-consumer-carbon:
	tests/Consumer/run source carbon $(CARBON_VERSION)

test-consumer-carbon-archive:
	tests/Consumer/run archive carbon $(CARBON_VERSION)

coverage-branch:
	@php -r 'if (!extension_loaded("xdebug")) { fwrite(STDERR, "$(BRANCH_COVERAGE_XDEBUG_ERROR)\n"); exit(1); }'
	@mkdir -p "$(BRANCH_COVERAGE_OUTPUT)"
	php -d xdebug.mode=coverage vendor/bin/phpunit \
		--configuration phpunit.branch.xml.dist \
		--path-coverage \
		--coverage-filter "$(BRANCH_COVERAGE_SOURCE)" \
		--coverage-html "$(BRANCH_COVERAGE_OUTPUT)/html" \
		--coverage-text="$(BRANCH_COVERAGE_OUTPUT)/coverage.txt" \
		$(BRANCH_COVERAGE_TESTS)

docs:
	mdbook build docs

docs-check: docs
	php tests/Documentation/check-generated-links.php build/docs

docs-serve:
	mdbook serve docs --hostname 127.0.0.1

test-consumer-getid3:
	tests/Consumer/run source getid3 $(GETID3_VERSION)

test-consumer-getid3-archive:
	tests/Consumer/run archive getid3 $(GETID3_VERSION)

test-consumer-guzzle:
	tests/Consumer/run source guzzle $(GUZZLE_MAJOR)

test-consumer-guzzle-archive:
	tests/Consumer/run archive guzzle $(GUZZLE_MAJOR)

test-consumer-intervention-image:
	tests/Consumer/run source intervention-image $(INTERVENTION_IMAGE_VERSION)

test-consumer-intervention-image-archive:
	tests/Consumer/run archive intervention-image $(INTERVENTION_IMAGE_VERSION)

test-consumer-illuminate-auth:
	tests/Consumer/run source illuminate-auth $(ILLUMINATE_AUTH_MAJOR) $(ILLUMINATE_COMPATIBILITY_MODE)

test-consumer-illuminate-auth-archive:
	tests/Consumer/run archive illuminate-auth $(ILLUMINATE_AUTH_MAJOR) $(ILLUMINATE_COMPATIBILITY_MODE)

test-consumer-illuminate-cache:
	tests/Consumer/run source illuminate-cache $(ILLUMINATE_CACHE_MAJOR) $(ILLUMINATE_COMPATIBILITY_MODE)

test-consumer-illuminate-cache-archive:
	tests/Consumer/run archive illuminate-cache $(ILLUMINATE_CACHE_MAJOR) $(ILLUMINATE_COMPATIBILITY_MODE)

test-consumer-illuminate-console:
	tests/Consumer/run source illuminate-console $(ILLUMINATE_CONSOLE_MAJOR) $(ILLUMINATE_COMPATIBILITY_MODE)

test-consumer-illuminate-console-archive:
	tests/Consumer/run archive illuminate-console $(ILLUMINATE_CONSOLE_MAJOR) $(ILLUMINATE_COMPATIBILITY_MODE)

test-consumer-illuminate-cookie:
	tests/Consumer/run source illuminate-cookie $(ILLUMINATE_COOKIE_MAJOR) $(ILLUMINATE_COMPATIBILITY_MODE)

test-consumer-illuminate-database:
	tests/Consumer/run source illuminate-database $(ILLUMINATE_DATABASE_MAJOR) $(ILLUMINATE_COMPATIBILITY_MODE)

test-consumer-illuminate-filesystem:
	tests/Consumer/run source illuminate-filesystem $(ILLUMINATE_FILESYSTEM_MAJOR) $(ILLUMINATE_COMPATIBILITY_MODE)

test-consumer-illuminate-http:
	tests/Consumer/run source illuminate-http $(ILLUMINATE_HTTP_MAJOR) $(ILLUMINATE_COMPATIBILITY_MODE)

test-consumer-illuminate-http-archive:
	tests/Consumer/run archive illuminate-http $(ILLUMINATE_HTTP_MAJOR) $(ILLUMINATE_COMPATIBILITY_MODE)

test-consumer-illuminate-process:
	tests/Consumer/run source illuminate-process $(ILLUMINATE_PROCESS_MAJOR) $(ILLUMINATE_COMPATIBILITY_MODE)

test-consumer-illuminate-queue:
	tests/Consumer/run source illuminate-queue $(ILLUMINATE_QUEUE_MAJOR) $(ILLUMINATE_COMPATIBILITY_MODE)

test-consumer-illuminate-redis:
	tests/Consumer/run source illuminate-redis $(ILLUMINATE_REDIS_MAJOR) $(ILLUMINATE_COMPATIBILITY_MODE)

test-consumer-illuminate-routing:
	tests/Consumer/run source illuminate-routing $(ILLUMINATE_ROUTING_MAJOR) $(ILLUMINATE_COMPATIBILITY_MODE)

test-consumer-illuminate-session:
	tests/Consumer/run source illuminate-session $(ILLUMINATE_SESSION_MAJOR) $(ILLUMINATE_COMPATIBILITY_MODE)

test-consumer-illuminate-support:
	tests/Consumer/run source illuminate-support $(ILLUMINATE_SUPPORT_MAJOR) $(ILLUMINATE_COMPATIBILITY_MODE)

test-consumer-illuminate-validation:
	tests/Consumer/run source illuminate-validation $(ILLUMINATE_VALIDATION_MAJOR) $(ILLUMINATE_COMPATIBILITY_MODE)

test-consumer-laravel-framework:
	tests/Consumer/run source laravel-framework $(LARAVEL_FRAMEWORK_MAJOR) $(ILLUMINATE_COMPATIBILITY_MODE)

test-consumer-laravel-framework-archive:
	tests/Consumer/run archive laravel-framework $(LARAVEL_FRAMEWORK_MAJOR) $(ILLUMINATE_COMPATIBILITY_MODE)

test-consumer-phpgeo:
	tests/Consumer/run source phpgeo $(PHPGEO_MAJOR)

test-consumer-phpgeo-archive:
	tests/Consumer/run archive phpgeo $(PHPGEO_MAJOR)

test-consumer-measurements:
	tests/Consumer/run source measurements $(MEASUREMENTS_VERSION)

test-consumer-measurements-archive:
	tests/Consumer/run archive measurements $(MEASUREMENTS_VERSION)

test-consumer-symfony-http-foundation:
	tests/Consumer/run source symfony-http-foundation $(SYMFONY_HTTP_FOUNDATION_VERSION) $(SYMFONY_COMPATIBILITY_MODE)

test-consumer-symfony-http-foundation-archive:
	tests/Consumer/run archive symfony-http-foundation $(SYMFONY_HTTP_FOUNDATION_VERSION) $(SYMFONY_COMPATIBILITY_MODE)

test-consumer-symfony-stopwatch:
	tests/Consumer/run source symfony-stopwatch $(SYMFONY_STOPWATCH_MAJOR) $(SYMFONY_COMPATIBILITY_MODE)

test-consumer-symfony-stopwatch-archive:
	tests/Consumer/run archive symfony-stopwatch $(SYMFONY_STOPWATCH_MAJOR) $(SYMFONY_COMPATIBILITY_MODE)

test-consumer-manual:
	tests/Consumer/run source manual 12
