.DEFAULT: all
.PHONY: all coverage-branch docs docs-check docs-serve test-consumer-guzzle test-consumer-guzzle-archive \
	test-consumer-illuminate-cache \
	test-consumer-illuminate-cache-archive test-consumer-illuminate-http test-consumer-illuminate-http-archive \
	test-consumer-illuminate-cookie test-consumer-illuminate-filesystem test-consumer-illuminate-process \
	test-consumer-illuminate-queue \
	test-consumer-illuminate-support test-consumer-phpgeo test-consumer-phpgeo-archive \
	test-consumer-symfony-stopwatch test-consumer-symfony-stopwatch-archive \
	test-consumer-manual

BRANCH_COVERAGE_OUTPUT ?= coverage/branch
BRANCH_COVERAGE_SOURCE ?= src
BRANCH_COVERAGE_TESTS ?=
BRANCH_COVERAGE_XDEBUG_ERROR := Xdebug is not loaded; enter nix develop .\#xdebug.
ILLUMINATE_CACHE_MAJOR ?= 12
GUZZLE_MAJOR ?= 8
ILLUMINATE_COOKIE_MAJOR ?= 12
ILLUMINATE_FILESYSTEM_MAJOR ?= 12
ILLUMINATE_HTTP_MAJOR ?= 12
ILLUMINATE_PROCESS_MAJOR ?= 12
ILLUMINATE_QUEUE_MAJOR ?= 12
ILLUMINATE_SUPPORT_MAJOR ?= 12
PHPGEO_MAJOR ?= 6
SYMFONY_STOPWATCH_MAJOR ?= 7

all:

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

test-consumer-guzzle:
	tests/Consumer/run source guzzle $(GUZZLE_MAJOR)

test-consumer-guzzle-archive:
	tests/Consumer/run archive guzzle $(GUZZLE_MAJOR)

test-consumer-illuminate-cache:
	tests/Consumer/run source illuminate-cache $(ILLUMINATE_CACHE_MAJOR)

test-consumer-illuminate-cache-archive:
	tests/Consumer/run archive illuminate-cache $(ILLUMINATE_CACHE_MAJOR)

test-consumer-illuminate-cookie:
	tests/Consumer/run source illuminate-cookie $(ILLUMINATE_COOKIE_MAJOR)

test-consumer-illuminate-filesystem:
	tests/Consumer/run source illuminate-filesystem $(ILLUMINATE_FILESYSTEM_MAJOR)

test-consumer-illuminate-http:
	tests/Consumer/run source illuminate-http $(ILLUMINATE_HTTP_MAJOR)

test-consumer-illuminate-http-archive:
	tests/Consumer/run archive illuminate-http $(ILLUMINATE_HTTP_MAJOR)

test-consumer-illuminate-process:
	tests/Consumer/run source illuminate-process $(ILLUMINATE_PROCESS_MAJOR)

test-consumer-illuminate-queue:
	tests/Consumer/run source illuminate-queue $(ILLUMINATE_QUEUE_MAJOR)

test-consumer-illuminate-support:
	tests/Consumer/run source illuminate-support $(ILLUMINATE_SUPPORT_MAJOR)

test-consumer-phpgeo:
	tests/Consumer/run source phpgeo $(PHPGEO_MAJOR)

test-consumer-phpgeo-archive:
	tests/Consumer/run archive phpgeo $(PHPGEO_MAJOR)

test-consumer-symfony-stopwatch:
	tests/Consumer/run source symfony-stopwatch $(SYMFONY_STOPWATCH_MAJOR)

test-consumer-symfony-stopwatch-archive:
	tests/Consumer/run archive symfony-stopwatch $(SYMFONY_STOPWATCH_MAJOR)

test-consumer-manual:
	tests/Consumer/run source manual 12
