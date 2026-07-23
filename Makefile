## v2.0.0
PLUGIN ?= $(notdir $(CURDIR))
VERSION ?= $(shell perl -lne 'm{Stable tag: .*?(.+)} and print $$1' readme.txt)
WPENV_BIN ?= $(shell test -x node_modules/.bin/wp-env && printf 'node_modules/.bin/wp-env' || printf 'npx @wordpress/env')
WPENV ?= $(WPENV_BIN) run cli --env-cwd=wp-content/plugins/$(PLUGIN)

.PHONY: analyse
analyse: env-check ## Run the phpstan analyser (inside wp-env)
	$(WPENV) env XDEBUG_MODE=off vendor/bin/phpstan analyse --memory-limit 2G

.PHONY: build
build: ## Build the languages and assets
	make build:i18n
	make build:assets

.PHONY: build\:all
build\:all: ## Build everything: languages, assets, blocks, and Divi elements
	make build
	make build:blocks
	make build:divi

.PHONY: build\:assets
build\:assets: ## Build the frontend assets with Rollup
	npm run rollup

.PHONY: build\:blocks
build\:blocks: ## Check the node/npm engines and build the Gutenberg blocks
	npm run check
	npm run blocks

.PHONY: build\:divi
build\:divi: ## Build the Divi elements
	npm run divi

.PHONY: build\:i18n
build\:i18n: ## Build the language files: text-domain check, then pot -> po -> mo
	npx gulp

.PHONY: bump
bump: ## Bump to the next patch version
	npx gulp bump

# Both halves always run — a phpcs finding must not hide a wp-since one — and
# the exit code reports whether either failed.
.PHONY: compat
compat: ## Check PHP compatibility (8.1, phpcs) and WP compatibility (the declared floor, wp-since)
	@test -f '+/tools/phpcs/vendor/bin/phpcs' || composer --working-dir='+/tools/phpcs' update
	@test -f '+/tools/wp-since/vendor/bin/wp-since' || composer --working-dir='+/tools/wp-since' update
	@status=0; \
	XDEBUG_MODE=off '+/tools/phpcs/vendor/bin/phpcs' --standard=phpcs.xml || status=1; \
	XDEBUG_MODE=off php -d memory_limit=2G '+/tools/wp-since/vendor/bin/wp-since' check || status=1; \
	exit $$status

.PHONY: coverage
coverage: env-check ## Run the suite with coverage of the PLUGIN, gated at 80% (restarts wp-env with Xdebug)
	$(WPENV_BIN) start --xdebug=coverage
	$(WPENV) env XDEBUG_MODE=coverage composer test:coverage

.PHONY: coverage\:all
coverage\:all: ## The full coverage picture: main suite, multisite suite, then the merged table
	make coverage
	make coverage:multisite
	make coverage:merge

# A clover from before a plugin/ change carries the old line numbering, and the
# merge then invents phantom uncovered lines — so a snapshot older than plugin/
# gets a warning. mtime is an approximation (a git checkout touches files), so
# it warns rather than refuses.
.PHONY: coverage\:merge
coverage\:merge: ## Merge the main and multisite clovers into tests/coverage/merged.xml, and print the merged table
	@test -f tests/coverage/clover.xml || { printf '\nNo tests/coverage/clover.xml — run `make coverage` first.\n\n'; exit 1; }
	@test -f tests/coverage/multisite.xml || { printf '\nNo tests/coverage/multisite.xml — run `make coverage:multisite` first.\n\n'; exit 1; }
	@find plugin -name '*.php' -newer tests/coverage/clover.xml | head -1 | grep -q . && \
		printf '\n\033[33mWarning:\033[0m plugin/ has changed since tests/coverage/clover.xml was generated —\nthe merged table may show phantom uncovered lines. Rerun `make coverage`.\n' || true
	@find plugin -name '*.php' -newer tests/coverage/multisite.xml | head -1 | grep -q . && \
		printf '\n\033[33mWarning:\033[0m plugin/ has changed since tests/coverage/multisite.xml was generated —\nthe merged table may show phantom uncovered lines. Rerun `make coverage:multisite`.\n' || true
	XDEBUG_MODE=off php tests/merge-clover.php tests/coverage/clover.xml tests/coverage/multisite.xml tests/coverage/merged.xml

.PHONY: coverage\:multisite
coverage\:multisite: multisite-env ## Run the multisite suite with coverage (its clover feeds coverage:merge)
	cd tests/multisite && npx @wordpress/env run cli --env-cwd=wp-content/plugins/site-reviews \
		env XDEBUG_MODE=coverage php -d memory_limit=-1 vendor/bin/pest \
		--test-directory=tests/multisite -c tests/multisite/phpunit.xml --colors=always \
		--coverage-clover=tests/coverage/multisite.xml

.PHONY: env
env: env-check ## What the suite is actually running against
	@printf '\nWordPress  '
	@$(WPENV) wp core version 2>/dev/null | tr -d '\r'
	@printf 'PHP        '
	@$(WPENV) php -r 'echo PHP_VERSION, PHP_EOL;' 2>/dev/null | tr -d '\r'
	@printf 'Tested to  %s (readme.txt)\n\n' "$$(perl -lne 'm{Tested up to: *(.+)} and print $$1' readme.txt)"

.PHONY: env\:update
env\:update: docker-check ## Update the WordPress version in .wp-env.json to the latest production release
	@latest="$$(curl -fsS https://api.wordpress.org/core/version-check/1.7/ | perl -lne 'm{"current":"([^"]+)"} and print $$1 and exit')"; \
	test -n "$$latest" || { printf '\nCould not reach wordpress.org to ask what the latest release is.\n\n'; exit 1; }; \
	printf '\nPinning WordPress to %s\n\n' "$$latest"; \
	perl -i -pe "s{\"core\": \".*\"}{\"core\": \"https://wordpress.org/wordpress-$$latest.zip\"}" .wp-env.json
	$(WPENV_BIN) start --update
	@$(MAKE) --no-print-directory env
	@printf 'Commit .wp-env.json to pin this for everybody.\n\n'

.PHONY: help
help: ## Display this help
	@awk '/^[^\t#].*?:.*?##/ { \
		n = index($$0, "##"); \
		t = substr($$0, 1, n - 1); \
		gsub(/\\:/, SUBSEP, t); \
		sub(/:.*/, "", t); \
		gsub(SUBSEP, ":", t); \
		d = substr($$0, n + 2); \
		sub(/^[ \t]+/, "", d); \
		printf "\033[36m%-30s\033[0m %s\n", t, d \
	}' $(MAKEFILE_LIST) | sort

.PHONY: release
release: ## Release a new version
	sh ./release.sh

.PHONY: stubs
stubs: ## List the stub manifest: what is generated from where, and what is missing
	@XDEBUG_MODE=off php tests/bin/generate-stubs.php --list

# Regenerates tests/stubs from the latest upstream releases. Premium sources are
# local zips dropped into tests/bin/zips (gitignored) — absent ones are skipped.
#   make stubs:update                          all entries with an available source
#   make stubs:update SLUGS='woocommerce elementor'
.PHONY: stubs\:update
stubs\:update: ## Regenerate the third-party stubs from tests/bin/stubs-manifest.php (SLUGS=… for a subset)
	@test -f vendor/php-stubs/generator/src/StubsGenerator.php || { \
		printf '\nphp-stubs/generator is not installed. Run:\n\n    make test:install\n\n'; \
		exit 1; \
	}
	XDEBUG_MODE=off php -d memory_limit=4G tests/bin/generate-stubs.php $(SLUGS)

.PHONY: test
test: env-check ## Run the four main Pest suites inside wp-env (see tests/pest/README.md)
	$(WPENV) env XDEBUG_MODE=off composer test

.PHONY: test\:all
test\:all: ## Run the main and multisite suites
	make test
	make test:multisite

# Run one test file.  Make sure to run `make test` before committing.
#   make test:file FILE=tests/pest/Integration/EmailTest.php [NAME='plain text']
.PHONY: test\:file
test\:file: env-check ## Run one test file (FILE=…), or the tests in it matching NAME=…
	@test -n '$(FILE)' || { printf '\nUsage: make test:file FILE=tests/pest/Integration/EmailTest.php [NAME="part of the test name"]\n\n'; exit 1; }
	$(WPENV) env XDEBUG_MODE=off vendor/bin/pest --test-directory=tests/pest --colors=always '$(FILE)' $(if $(NAME),--filter='$(NAME)',)

# Run several files together to bisect a leak. Make sure to run `make test` before committing.
#   make test:files FILES="tests/pest/Integration/A.php tests/pest/Integration/B.php"
.PHONY: test\:files
test\:files: env-check ## Run several test files together (FILES="a.php b.php") — for bisecting a leak
	@test -n '$(FILES)' || { printf '\nUsage: make test:files FILES="tests/pest/Integration/A.php tests/pest/Integration/B.php"\n\n'; exit 1; }
	$(WPENV) env XDEBUG_MODE=off vendor/bin/pest --test-directory=tests/pest --colors=always $(FILES)

.PHONY: test\:import
test\:import: env-check ## Run only the Import suite inside wp-env (runs last: it defines WP_IMPORTING)
	$(WPENV) env XDEBUG_MODE=off composer test:import

.PHONY: test\:install
test\:install: docker-check ## Start wp-env and install the composer dev dependencies inside it
	rm -rf vendor
	$(WPENV_BIN) start
	$(WPENV) composer update

.PHONY: test\:integration
test\:integration: env-check ## Run only the Integration suite inside wp-env
	$(WPENV) env XDEBUG_MODE=off composer test:integration

.PHONY: test\:multisite
test\:multisite: multisite-env ## Run the multisite suite in its own wp-env instance
	cd tests/multisite && npx @wordpress/env run cli --env-cwd=wp-content/plugins/site-reviews \
		env XDEBUG_MODE=off php -d memory_limit=-1 vendor/bin/pest \
		--test-directory=tests/multisite -c tests/multisite/phpunit.xml --colors=always

.PHONY: test\:profile
test\:profile: env-check ## Show the slowest tests in the suite
	$(WPENV) env XDEBUG_MODE=off vendor/bin/pest --test-directory=tests/pest --colors=always --profile

# Shuffle tests WITHIN each suite. The seed is shared and reproducible.
#   make test:random SEED=1234
.PHONY: test\:random
test\:random: env-check ## Run each suite with its tests shuffled, to find tests that leak state
	@seed="$(if $(SEED),$(SEED),$$(od -An -N2 -tu2 < /dev/urandom | tr -d ' '))"; \
	printf '\nRandom order seed: %s   (reproduce with: make test:random SEED=%s)\n' "$$seed" "$$seed"; \
	for suite in Unit Integration ThirdParty Import; do \
		printf '\n─── %s ───\n' "$$suite"; \
		$(WPENV) env XDEBUG_MODE=off vendor/bin/pest --test-directory=tests/pest --colors=always \
			--testsuite="$$suite" --order-by=random --random-order-seed="$$seed" || exit 1; \
	done

.PHONY: test\:thirdparty
test\:thirdparty: env-check ## Run only the ThirdParty suite inside wp-env (the integrations)
	$(WPENV) env XDEBUG_MODE=off composer test:thirdparty

.PHONY: test\:unit
test\:unit: env-check ## Run only the Unit suite inside wp-env (fast feedback loop)
	$(WPENV) env XDEBUG_MODE=off composer test:unit

.PHONY: update
update: env-check ## Update the Composer dependencies (inside wp-env) and check the NPM ones
	$(WPENV) composer update
	npm-check -u

.PHONY: watch
watch: ## Watch and rebuild the frontend assets with Rollup
	npm run rollup:watch

.PHONY: zip
zip: ## Create a zip archive of Site Reviews from HEAD
	git archive -o ./$(PLUGIN)-v$(VERSION).zip --prefix=$(PLUGIN)/ HEAD
	open .

# ─── Private targets ─────────────────────────────────────────────────────────
# No help entry; used only as prerequisites by the recipes above.

# wp-env needs a running Docker engine; fail early with instructions.
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

# Ensure the test env is installed and wp-env is running (start is idempotent).
# The grep pins THIS instance's name shape (wp-env-<plugin>-<hash>-cli): a loose
# '$(PLUGIN).*cli' also matches a sibling checkout's containers (site-reviews
# matches site-reviews-premium-…-cli) and skips the start while this env is down.
.PHONY: env-check
env-check: docker-check
	@test -f vendor/bin/pest || { \
		printf '\nThe test environment has not been set up yet. Run:\n\n'; \
		printf '    make test:install\n\n'; \
		exit 1; \
	}
	@docker ps --format '{{.Names}}' | grep -Eq 'wp-env-$(PLUGIN)-[0-9a-f]+-cli' || { \
		printf '\nwp-env is not running — starting it…\n\n'; \
		$(WPENV_BIN) start; \
	}

# The multisite suite's OWN wp-env instance (tests/multisite/.wp-env.json:
# port 8892, EMPTY_TRASH_DAYS=0), converted to a network on first run. wp-env
# picks the instance from the CWD, hence the cd; npx resolves the local wp-env
# from any subdir. Started with Xdebug so coverage:multisite can measure;
# test:multisite runs with XDEBUG_MODE=off regardless.
# wp-env regenerates wp-config.php whenever start re-provisions, which erases
# the constants multisite-convert appended — and a converted database with a
# single-site config strands the whole instance. The `wp config set` lines
# re-assert them idempotently on every run, whichever half survived.
# WP_ENV_PORT is pinned to 8892 here because the environment variable overrides
# .wp-env.json's own "port" — a WP_ENV_PORT exported for the MAIN instance (the
# usual workaround when 8888 is taken) would otherwise redirect this one onto
# the main instance's port and fail the bind.
.PHONY: multisite-env
multisite-env: docker-check
	cd tests/multisite && WP_ENV_PORT=8892 npx @wordpress/env start --xdebug=coverage
	cd tests/multisite && (npx @wordpress/env run cli wp core is-installed --network 2>/dev/null \
		|| npx @wordpress/env run cli wp core multisite-convert --title='Site Reviews Network')
	cd tests/multisite && npx @wordpress/env run cli bash -c "\
		wp config set MULTISITE true --raw --type=constant && \
		wp config set SUBDOMAIN_INSTALL false --raw --type=constant && \
		wp config set DOMAIN_CURRENT_SITE localhost:8892 --type=constant && \
		wp config set PATH_CURRENT_SITE / --type=constant && \
		wp config set SITE_ID_CURRENT_SITE 1 --raw --type=constant && \
		wp config set BLOG_ID_CURRENT_SITE 1 --raw --type=constant"
