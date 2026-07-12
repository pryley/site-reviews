## v1.1.0
PLUGIN ?= $(notdir $(CURDIR))
VERSION ?= $(shell perl -lne 'm{Stable tag: .*?(.+)} and print $$1' readme.txt)

# PHP tooling that loads vendor/ runs INSIDE the wp-env cli container: the
# composer dependencies are installed there (PHP 8.3, see `make test:install`)
# and Pest needs a PHP the host may not have. The tests need a real WordPress
# anyway, which is what wp-env provides.
WPENV ?= npx @wordpress/env run cli --env-cwd=wp-content/plugins/$(PLUGIN)

.PHONY: analyse
analyse: env-check ## Run phpstan analyser (inside wp-env)
	$(WPENV) env XDEBUG_MODE=off vendor/bin/phpstan analyse --memory-limit 2G

.PHONY: blocks
blocks: ## Build all blocks
	npm run check
	npm run blocks

.PHONY: build
build: ## Build all assets and languages
	npx gulp
	make blocks
	make divi
	make rollup

.PHONY: bump
bump: ## Bump to the next minor version
	npx gulp bump

.PHONY: check
check: ## Check WP compatibility for declared version
	@test -f '+/tools/wp-since/vendor/bin/wp-since' || composer --working-dir='+/tools/wp-since' update
	XDEBUG_MODE=off php -d memory_limit=2G '+/tools/wp-since/vendor/bin/wp-since' check

.PHONY: compat
compat: ## Run PHP CodeSniffer to check PHP 8.1- Compatibility
	@test -f '+/tools/phpcs/vendor/bin/phpcs' || composer --working-dir='+/tools/phpcs' update
	XDEBUG_MODE=off '+/tools/phpcs/vendor/bin/phpcs' --standard=phpcs.xml

.PHONY: db
db: ## Open the database in TablePlus
	@open mysql://dev:dev@127.0.0.1/site-reviews?enviroment=local&name=Localhost&safeModeLevel=0&advancedSafeModeLevel=0

.PHONY: divi
divi: ## Build all Divi elements
	npm run divi

.PHONY: help
help:  ## Display help
	@awk '/^[^\t#].*?:.*?##/ { \
		n = index($$0, "##"); \
		t = substr($$0, 1, n - 1); \
		sub(/[: \t]+$$/, "", t); \
		gsub(/\\:/, ":", t); \
		d = substr($$0, n + 2); \
		sub(/^[ \t]+/, "", d); \
		printf "\033[36m%-30s\033[0m %s\n", t, d \
	}' $(MAKEFILE_LIST) | sort

.PHONY: i18n
i18n: ## Generate a pot file with the wp-cli
	npm run i18n-pot

.PHONY: open
open: ## Open the development site in the default browser
	@open http://site-reviews.test/wp/wp-admin/edit.php?post_type=site-review

# The test targets run inside wp-env, which needs a running Docker engine.
# Fail early with instructions instead of letting wp-env die on a socket error.
.PHONY: docker-check
docker-check:
	@command -v docker >/dev/null 2>&1 || { \
		printf '\nDocker CLI not found.\n'; \
		printf 'OrbStack installs it when the app is running:\n\n'; \
		printf '    open -a OrbStack\n\n'; \
		printf 'then run this command again.\n\n'; \
		exit 1; \
	}
	@docker info >/dev/null 2>&1 || { \
		printf '\nThe Docker engine is not running (wp-env needs it).\n'; \
		printf 'Start OrbStack (or your Docker provider) and retry:\n\n'; \
		printf '    open -a OrbStack\n\n'; \
		exit 1; \
	}

# Auto-start the wp-env containers when they exist but are stopped (e.g. after
# a reboot) — `wp-env start` is idempotent.
.PHONY: env-check
env-check: docker-check
	@test -f vendor/bin/pest || { \
		printf '\nThe test environment has not been set up yet. Run:\n\n'; \
		printf '    make test:install\n\n'; \
		exit 1; \
	}
	@docker ps --format '{{.Names}}' | grep -q '$(PLUGIN).*cli' || { \
		printf '\nwp-env is not running — starting it…\n\n'; \
		npx @wordpress/env start; \
	}

.PHONY: release
release: ## Release a new version
	sh ./release.sh

.PHONY: rollup
rollup: ## Build all assets
	npm run rollup

.PHONY: sync
sync: ## Sync plugin files to development site
	@rsync --archive --recursive --delete \
	--exclude={'/+','/*.md','/*.js','/*.json','/*.sh','/*.lock','/*.neon','/Makefile','/phpunit.xml','/tests'} \
	--filter=":- .gitignore" \
	. ~/Sites/site-reviews/public/app/plugins/site-reviews/

.PHONY: test
test: env-check ## Run the Pest suites inside wp-env (see tests/pest/README.md)
	$(WPENV) composer test

.PHONY: test\:all
test\:all: ## Run the analyser, the full suite with coverage, and the compat checks
	make analyse
	make test\:coverage
	make check
	make compat

.PHONY: test\:coverage
test\:coverage: env-check ## Run the suite with coverage (restarts wp-env with Xdebug in coverage mode)
	npx @wordpress/env start --xdebug=coverage
	$(WPENV) env XDEBUG_MODE=coverage composer test:coverage

.PHONY: test\:install
test\:install: docker-check ## Start wp-env and install the composer dev dependencies inside it
	rm -rf vendor
	npx @wordpress/env start
	$(WPENV) composer update

.PHONY: test\:integration
test\:integration: env-check ## Run only the Integration suite inside wp-env
	$(WPENV) composer test:integration

.PHONY: test\:unit
test\:unit: env-check ## Run only the Unit suite inside wp-env (fast feedback loop)
	$(WPENV) composer test:unit

.PHONY: update
update: env-check ## Update Composer (inside wp-env) and NPM
	$(WPENV) composer update
	npm-check -u

.PHONY: watch
watch: ## Build all plugin assets and run Browsersync
	npm run rollup:watch

.PHONY: zip
zip: ## Create a zip archive of Site Reviews
	git archive -o ./$(PLUGIN)-v$(VERSION).zip --prefix=$(PLUGIN)/ HEAD
	open .
