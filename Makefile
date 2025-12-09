## v1.0.0
PLUGIN ?= $(notdir $(CURDIR))
VERSION ?= $(shell perl -lne 'm{Stable tag: .*?(.+)} and print $$1' readme.txt)

.PHONY: analyse
analyse: ## Run phpstan analyser
	XDEBUG_MODE=off ./vendor/bin/phpstan analyse --memory-limit 2G

.PHONY: blocks
blocks: ## Build all blocks
	npm run check
	npm run blocks

.PHONY: build
build: ## Build all assets and languages
	npx gulp
	make mix

.PHONY: bump
bump: ## Bump to the next minor version
	npx gulp bump

.PHONY: check
check: ## Check WP compatibility for declared version
	XDEBUG_MODE=off php -d memory_limit=2G ./vendor/bin/wp-since check

.PHONY: compat
compat: ## Run PHP CodeSniffer to check PHP 8.1- Compatibility
	XDEBUG_MODE=off ./vendor/bin/phpcs --standard=phpcs.xml

.PHONY: db
db: ## Open the database in TablePlus
	@open mysql://dev:dev@127.0.0.1/site-reviews?enviroment=local&name=Localhost&safeModeLevel=0&advancedSafeModeLevel=0

.PHONY: divi
divi: ## Build all Divi elements
	npm run divi

.PHONY: help
help:  ## Display help
	@awk -F ':|##' '/^[^\t].+?:.*?##/ {printf "\033[36m%-30s\033[0m %s\n", $$1, $$NF}' $(MAKEFILE_LIST) | sort

.PHONY: i18n
i18n: ## Generate a pot file with the wp-cli
	npm run i18n-pot

.PHONY: mix
mix: ## Build all assets
	npm run production
	npx rollup -c
	make blocks
	make divi

.PHONY: mixsync
mixsync: ## Build all assets and sync
	make mix
	make sync

.PHONY: open
open: ## Open the development site in the default browser
	@open http://site-reviews.test/wp/wp-admin/edit.php?post_type=site-review

.PHONY: release
release: ## Release a new version
	sh ./release.sh

.PHONY: sync
sync: ## Sync plugin files to development site
	@rsync --archive --recursive --delete \
	--exclude={'/+','/*.md','/*.js','/*.json','/*.sh','/*.lock','/*.neon','/Makefile','/phpunit.xml','/tests'} \
	--filter=":- .gitignore" \
	. ~/Sites/site-reviews/public/app/plugins/site-reviews/

.PHONY: test
test: ## Run all phpunit tests
	XDEBUG_MODE=off ./vendor/bin/phpunit

.PHONY: testall
testall: ## Run phpstan analyser and all phpunit tests
	make analyse
	make test
	make check
	make compat

.PHONY: update
update: ## Update Composer and NPM
	composer update
	npm-check -u

.PHONY: watch
watch: ## Build all plugin assets and run Browsersync
	npm run watch

.PHONY: zip
zip: ## Create a zip archive of Site Reviews
	git archive -o ./$(PLUGIN)-v$(VERSION).zip --prefix=$(PLUGIN)/ HEAD
	open .
