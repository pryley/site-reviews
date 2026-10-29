## v3.0.0
# The shared targets come from pryley/wp-plugin-tooling's make/plugin.mk, included below;
# `make help` lists them with the targets below. PHP tooling that loads vendor/ runs inside
# the wp-env cli container, where `make test:install` installs the composer dependencies.

PLUGIN := site-reviews
TEST_DIR := tests/pest
WP_ENV_PORT := 8890
MULTISITE_PORT := 8892
MULTISITE_TITLE := Site Reviews Network
WOOCOMMERCE_PORT := 8894
COVERAGE_CLOVERS := tests/coverage/clover.xml tests/coverage/multisite.xml tests/coverage/woocommerce.xml
WPENV_CONFIGS := .wp-env.json tests/multisite/.wp-env.json tests/woocommerce/.wp-env.json
I18N_EXCLUDE := +,node_modules,tests,vendor,vendors
I18N_HEADERS := {"Last-Translator":"Paul Ryley <paul@geminilabs.io>","Language-Team":"Gemini Labs <support@geminilabs.io>"}
MULTILINGUALPRESS_SOURCE ?= $(HOME)/Local Sites/test-multilingualpress/app/public/wp-content/plugins/multilingualpress
TEST_JS = npm run test:js
TEST_ALL_EXTRA = test:woocommerce
COVERAGE_ALL_EXTRA = coverage:woocommerce
ENV_UPDATE_AFTER = (cd tests/woocommerce && env WP_ENV_PORT=$(WOOCOMMERCE_PORT) npx @wordpress/env start --update) && $(MAKE) --no-print-directory woocommerce-env
# MultilingualPress is proprietary: the multisite instance maps a local copy of it from tests/bin/plugins.
MULTISITE_PREPARE = mkdir -p tests/bin/plugins/multilingualpress; \
	if [ -f "$(MULTILINGUALPRESS_SOURCE)/multilingualpress.php" ]; then \
		rsync -a --delete "$(MULTILINGUALPRESS_SOURCE)/" tests/bin/plugins/multilingualpress/; \
	fi
MULTISITE_SETUP = quietly npx @wordpress/env run cli bash wp-content/plugins/site-reviews/tests/multisite/setup-multilingualpress.sh

TOOLING_MK := vendor/pryley/wp-plugin-tooling/make/plugin.mk
ifneq ($(wildcard $(TOOLING_MK)),)
include $(TOOLING_MK)
else
.DEFAULT:
	@printf '\nRun `make test:install` first.\n\n'
endif

.PHONY: build
build: ## Build the languages and assets
	@$(MAKE) --no-print-directory build:i18n
	@$(MAKE) --no-print-directory build:assets

.PHONY: build\:all
build\:all: ## Build everything: languages, assets, blocks, and Divi elements
	@$(MAKE) --no-print-directory build:i18n
	@$(MAKE) --no-print-directory build:blocks
	@$(MAKE) --no-print-directory build:divi
	@$(MAKE) --no-print-directory build:assets

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

.PHONY: coverage\:woocommerce
coverage\:woocommerce: woocommerce-env ## Run the WooCommerce suite with coverage (its clover feeds coverage:merge)
	@cd tests/woocommerce && npx @wordpress/env run cli --env-cwd=wp-content/plugins/site-reviews \
		env XDEBUG_MODE=coverage php -d memory_limit=-1 vendor/bin/pest \
		--test-directory=tests/woocommerce -c tests/woocommerce/phpunit.xml --colors=always \
		--coverage-clover=tests/coverage/woocommerce.xml

.PHONY: release
release: ## Release a new version
	sh ./release.sh

.PHONY: test\:install
test\:install: docker-check ## Start wp-env and install the composer dev dependencies inside it
	@test ! -d vendor/pryley/wp-plugin-tooling/.git || test -z "$$(git -C vendor/pryley/wp-plugin-tooling status --porcelain)" || { \
		printf '\nvendor/pryley/wp-plugin-tooling has uncommitted changes. Commit or discard them first.\n\n'; \
		exit 1; \
	}
	rm -rf vendor
	WP_ENV_PORT=$(WP_ENV_PORT) $(or $(WPENV_BIN),npx @wordpress/env) start
	$(or $(WPENV),npx @wordpress/env run cli --env-cwd=wp-content/plugins/$(PLUGIN)) composer install

.PHONY: test\:woocommerce
test\:woocommerce: woocommerce-env ## Run the WooCommerce suite in its own wp-env instance (a real WooCommerce)
	@cd tests/woocommerce && npx @wordpress/env run cli --env-cwd=wp-content/plugins/site-reviews \
		env XDEBUG_MODE=off php -d memory_limit=-1 vendor/bin/pest \
		--test-directory=tests/woocommerce -c tests/woocommerce/phpunit.xml --colors=always

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

.PHONY: woocommerce-env
woocommerce-env: docker-check
	@printf 'Preparing the WooCommerce wp-env instance (port %s)…\n' '$(WOOCOMMERCE_PORT)'
	@log=$$(mktemp); trap 'rm -f "$$log"' EXIT; \
	quietly() { "$$@" >"$$log" 2>&1 || { cat "$$log"; exit 1; }; }; \
	cd tests/woocommerce; \
	quietly env WP_ENV_PORT=$(WOOCOMMERCE_PORT) npx @wordpress/env start --xdebug=coverage; \
	quietly npx @wordpress/env run cli --env-cwd=wp-content/plugins/site-reviews wp eval \
		'glsr(GeminiLabs\SiteReviews\Database\OptionManager::class)->set("settings.integrations.woocommerce.enabled", "yes");'; \
	quietly npx @wordpress/env run cli wp option update woocommerce_enable_reviews yes
