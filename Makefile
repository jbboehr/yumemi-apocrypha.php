.DEFAULT: all
.PHONY: all coverage-branch docs docs-check docs-serve test-consumer-illuminate-cache \
	test-consumer-illuminate-cache-archive test-consumer-illuminate-http test-consumer-illuminate-http-archive

BRANCH_COVERAGE_OUTPUT ?= coverage/branch
BRANCH_COVERAGE_SOURCE ?= src
BRANCH_COVERAGE_TESTS ?=
BRANCH_COVERAGE_XDEBUG_ERROR := Xdebug is not loaded; enter nix develop .\#xdebug.
ILLUMINATE_CACHE_MAJOR ?= 12
ILLUMINATE_HTTP_MAJOR ?= 12

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

test-consumer-illuminate-cache:
	tests/Consumer/run source illuminate-cache $(ILLUMINATE_CACHE_MAJOR)

test-consumer-illuminate-cache-archive:
	tests/Consumer/run archive illuminate-cache $(ILLUMINATE_CACHE_MAJOR)

test-consumer-illuminate-http:
	tests/Consumer/run source illuminate-http $(ILLUMINATE_HTTP_MAJOR)

test-consumer-illuminate-http-archive:
	tests/Consumer/run archive illuminate-http $(ILLUMINATE_HTTP_MAJOR)
