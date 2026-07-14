## v1.1.0
PLUGIN ?= $(notdir $(CURDIR))
VERSION ?= $(shell perl -lne 'm{Stable tag: .*?(.+)} and print $$1' readme.txt)

# wp-env, and deliberately NOT `npx @wordpress/env`.
#
# npx resolves the package against the npm REGISTRY before it runs anything, even when the package
# is already in its cache. So every target in this file reached out to the network -- and with no
# network it did not fail, it HUNG, which is the worst of the three things it could have done.
#
# The locally installed binary is used when it is there, and npx is the fallback for somebody who
# has not run `npm install` yet. Once installed, nothing in this Makefile touches the network except
# `make env:update`, which is supposed to.
WPENV_BIN ?= $(shell test -x node_modules/.bin/wp-env && printf 'node_modules/.bin/wp-env' || printf 'npx @wordpress/env')
WPENV ?= $(WPENV_BIN) run cli --env-cwd=wp-content/plugins/$(PLUGIN)

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
		$(WPENV_BIN) start; \
	}

# WordPress is PINNED in .wp-env.json, and this is the only thing that moves the pin.
#
# `"core": null` means "the latest release", which sounds like what anybody would want — and it
# makes `wp-env start` phone api.wordpress.org EVERY TIME, to ask what that is. So the suite could
# not start on a train, and two people running it on the same commit on the same day could be
# testing against different WordPresses without either of them knowing.
#
# Pinned, `wp-env start` is offline (the zip is cached in ~/.wp-env after the first fetch) and
# reproducible. The version moves when somebody DECIDES it should, which is what this does: ask
# wordpress.org what the current release is, write it into .wp-env.json, and rebuild.
#
# Nothing else in the suite touches the network. See tests/pest/bootstrap.php.
.PHONY: env\:update
env\:update: docker-check ## Move WordPress to the latest production release (the ONLY thing that needs the internet)
	@latest="$$(curl -fsS https://api.wordpress.org/core/version-check/1.7/ | perl -lne 'm{"current":"([^"]+)"} and print $$1 and exit')"; \
	test -n "$$latest" || { printf '\nCould not reach wordpress.org to ask what the latest release is.\n\n'; exit 1; }; \
	printf '\nPinning WordPress to %s\n\n' "$$latest"; \
	perl -i -pe "s{\"core\": \".*\"}{\"core\": \"https://wordpress.org/wordpress-$$latest.zip\"}" .wp-env.json
	$(WPENV_BIN) start --update
	@$(MAKE) --no-print-directory env:info
	@printf 'Commit .wp-env.json to pin this for everybody.\n\n'

.PHONY: env\:info
env\:info: env-check ## What is the suite actually running against?
	@printf '\nWordPress  '
	@$(WPENV) wp core version 2>/dev/null | tr -d '\r'
	@printf 'PHP        '
	@$(WPENV) php -r 'echo PHP_VERSION, PHP_EOL;' 2>/dev/null | tr -d '\r'
	@printf 'Tested to  %s (readme.txt)\n\n' "$$(perl -lne 'm{Tested up to: *(.+)} and print $$1' readme.txt)"

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

# XDEBUG_MODE=off is not decoration, and the reason is not the one you would guess.
#
# Xdebug IS off by default in wp-env — but `make test:coverage` restarts the container with
# `wp-env start --xdebug=coverage`, and LEAVES IT THAT WAY. So the first coverage run of the
# day quietly makes every subsequent `make test` 60% slower, for profiling nobody asked for,
# until somebody restarts wp-env without the flag. Measured, on the same code:
#
#   composer test                       56.5s   (after a coverage run: Xdebug still loaded)
#   env XDEBUG_MODE=off composer test   35.4s
#
# So the test targets say what they want rather than trusting the container's state. The
# four separate pest processes `composer test` runs (one per testsuite, each booting
# WordPress again) cost about a second between them — not worth merging. Xdebug was all of it.
.PHONY: test
test: env-check ## Run the Pest suites inside wp-env (see tests/pest/README.md)
	$(WPENV) env XDEBUG_MODE=off composer test

.PHONY: test\:all
test\:all: ## Run the analyser, the full suite with coverage, and the compat checks
	make analyse
	make test\:coverage
	make check
	make compat

.PHONY: test\:coverage
test\:coverage: env-check ## Run the suite with coverage of the PLUGIN, gated at 80% (restarts wp-env with Xdebug)
	$(WPENV_BIN) start --xdebug=coverage
	$(WPENV) env XDEBUG_MODE=coverage composer test:coverage

# Coverage of ONE part of the plugin, measured against the WHOLE suite — which is the only
# number that means anything: a file is covered by whatever tests happen to reach it, and most
# are reached from more than one place.
#
# DIR, not FILE. PHPUnit's --coverage-filter takes directories only: every value becomes a
# `new FilterDirectory($value, '', '.php')` (TextUI/Configuration/Merger.php), and a path to a
# single .php file matches nothing at all — it does not error, it silently reports zero files.
# So scope it to the directory and read the line for the file you care about.
#
#   make test:coverage:only DIR=plugin/Widgets
#   make test:coverage:only DIR=plugin/Database/Tables
.PHONY: test\:coverage\:only
test\:coverage\:only: env-check ## Coverage of one DIRECTORY, from the whole suite: make test:coverage:only DIR=plugin/Widgets
	@test -n "$(DIR)" || { echo "Usage: make test:coverage:only DIR=plugin/Widgets   (a directory — see the Makefile)"; exit 1; }
	@test -d "$(DIR)" || { echo "Not a directory: $(DIR)   (--coverage-filter takes directories, not files)"; exit 1; }
	$(WPENV_BIN) start --xdebug=coverage
	$(WPENV) env XDEBUG_MODE=coverage php -d memory_limit=-1 vendor/bin/pest \
		--test-directory=tests/pest --coverage --coverage-filter=$(DIR)

.PHONY: test\:coverage\:integrations
test\:coverage\:integrations: env-check ## Coverage of the third-party integrations only — reported, never gated
	$(WPENV_BIN) start --xdebug=coverage
	$(WPENV) env XDEBUG_MODE=coverage composer test:coverage:integrations

.PHONY: test\:install
test\:install: docker-check ## Start wp-env and install the composer dev dependencies inside it
	rm -rf vendor
	$(WPENV_BIN) start
	$(WPENV) composer update

# The edit loop, not the thing to trust before a commit. A file that passes on its own
# can still be poisoning — or poisoned by — the ones that run around it: the Import
# suite's DDL implicitly COMMITs the per-test transaction, and the term AUTO_INCREMENT
# does not roll back at all. Both were invisible until the whole suite ran, in order.
# Iterate with this; run `make test` before committing.
#
#   make test:file FILE=tests/pest/Integration/EmailTest.php
#   make test:file FILE=tests/pest/Integration/EmailTest.php NAME='plain text'
#
.PHONY: test\:file
test\:file: env-check ## Run one test file (FILE=…), or the tests in it matching NAME=…
	@test -n '$(FILE)' || { printf '\nUsage: make test:file FILE=tests/pest/Integration/EmailTest.php [NAME="part of the test name"]\n\n'; exit 1; }
	$(WPENV) env XDEBUG_MODE=off vendor/bin/pest --test-directory=tests/pest --colors=always '$(FILE)' $(if $(NAME),--filter='$(NAME)',)

# For bisecting a leak. A test that passes alone and fails in the suite is leaning on — or being
# poisoned by — something in another file, and the only way to find out which file is to run them
# together, two at a time, in the order the suite would.
#
#   make test:files FILES="tests/pest/Integration/ApplicationTest.php tests/pest/Integration/ReviewManagerTest.php"
#
# FILES is deliberately UNQUOTED in the recipe, which is the whole difference from test:file.
.PHONY: test\:files
test\:files: env-check ## Run several test files together (FILES="a.php b.php") — for bisecting a leak
	@test -n '$(FILES)' || { printf '\nUsage: make test:files FILES="tests/pest/Integration/A.php tests/pest/Integration/B.php"\n\n'; exit 1; }
	$(WPENV) env XDEBUG_MODE=off vendor/bin/pest --test-directory=tests/pest --colors=always $(FILES)

# The ten slowest tests. Run this when the suite gets slower and you want to know WHY,
# rather than reasoning about which of the new tests looks expensive — the answer has
# twice now been something nobody would have picked (eight thousand migration runs; six
# foreign constraints rebuilt on every boot).
.PHONY: test\:profile
test\:profile: env-check ## Show the slowest tests in the suite
	$(WPENV) env XDEBUG_MODE=off vendor/bin/pest --test-directory=tests/pest --colors=always --profile

# Shuffles the tests WITHIN each suite, so that anything leaning on state a previous test
# happened to leave behind falls over.
#
# It is not hypothetical. A block test asserted that the review form renders, and passed —
# because the test before it had logged somebody in. It only failed when the order changed.
# A test that inherits the current user, the plugin settings, a transient or a container
# binding is not testing what its name says.
#
# EACH SUITE IS A SEPARATE PROCESS, and that is not a detail. Shuffling all four suites
# together in one process moves Import out of last place — and Import defines WP_IMPORTING,
# which cannot be unset and which suppresses avatars, verification emails, geolocation,
# notifications and cache flushes for the remainder of the process. The first version of this
# target did exactly that and reported forty-one "leaking" tests, almost none of which were
# leaking anything: ThirdParty/CacheTest said so on line 47, in as many words.
#
# So: shuffle inside a suite, never across them. The seed is shared, so a failure is
# reproducible in full with:
#
#   make test:random SEED=1234
.PHONY: test\:random
test\:random: env-check ## Run each suite in a random order, to find tests that leak state
	@seed="$(if $(SEED),$(SEED),$$(od -An -N2 -tu2 < /dev/urandom | tr -d ' '))"; \
	printf '\nRandom order seed: %s   (reproduce with: make test:random SEED=%s)\n' "$$seed" "$$seed"; \
	for suite in Unit Integration ThirdParty Import; do \
		printf '\n─── %s ───\n' "$$suite"; \
		$(WPENV) env XDEBUG_MODE=off vendor/bin/pest --test-directory=tests/pest --colors=always \
			--testsuite="$$suite" --order-by=random --random-order-seed="$$seed" || exit 1; \
	done

.PHONY: test\:import
test\:import: env-check ## Run only the Import suite inside wp-env (runs last: it defines WP_IMPORTING)
	$(WPENV) env XDEBUG_MODE=off composer test:import

.PHONY: test\:integration
test\:integration: env-check ## Run only the Integration suite inside wp-env
	$(WPENV) env XDEBUG_MODE=off composer test:integration

.PHONY: test\:thirdparty
test\:thirdparty: env-check ## Run only the ThirdParty suite inside wp-env (the integrations)
	$(WPENV) env XDEBUG_MODE=off composer test:thirdparty

.PHONY: test\:unit
test\:unit: env-check ## Run only the Unit suite inside wp-env (fast feedback loop)
	$(WPENV) env XDEBUG_MODE=off composer test:unit

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
