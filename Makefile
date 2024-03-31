# Variables
VERSION ?= $(shell perl -lne 'm{Stable tag: .*?(.+)} and print $$1' readme.txt)

# Targets
analyse: ## Run phpstan analyser
	XDEBUG_MODE=off ./vendor/bin/phpstan analyse --memory-limit 2G

build: ## Build all assets and languages
	npx gulp
	make mix

bump: ## Bump to the next minor version
	npx gulp bump

compat: ## Run PHP CodeSniffer to check PHP 7.4+ Compatibility
	XDEBUG_MODE=off ./vendor/bin/phpcs --standard=phpcs.xml

db: ## Open the database in TablePlus
	@open mysql://dev:dev@127.0.0.1/site-reviews?enviroment=local&name=Localhost&safeModeLevel=0&advancedSafeModeLevel=0

help:  ## Display help
	@awk -F ':|##' '/^[^\t].+?:.*?##/ {printf "\033[36m%-30s\033[0m %s\n", $$1, $$NF}' $(MAKEFILE_LIST) | sort

mix: ## Build all assets
	npx mix --production
	npx rollup -c

mixsync: ## Build all assets and sync
	make mix
	make sync

open: ## Open the development site in the default browser
	@open http://site-reviews.test/wp/wp-admin/edit.php?post_type=site-review

release: ## Release a new version of Site Reviews
	make build
	@git diff --quiet || (echo "\n❌ \033[0;31mYou forgot to commit changes.\033[0m\n"; exit 1;)
	sh ./release.sh

sync: ## Sync plugin files to development site
	@rsync --archive --recursive --delete \
	--exclude={'/+','/*.md','/*.js','/*.json','/*.sh','/*.lock','/*.neon','/Makefile','/phpunit.xml','/tests'} \
	--filter=":- .gitignore" \
	. ~/Sites/site-reviews/public/app/plugins/site-reviews/

test: ## Run all phpunit tests
	XDEBUG_MODE=coverage ./vendor/bin/phpunit

testall: ## Run phpstan analyser and all phpunit tests
	make analyse
	make test
	make compat

update: ## Update Composer and NPM
	composer update
	npm-check -u

watch: ## Build all plugin assets and run Browsersync
	npm run watch

zip: ## Create a zip archive of Site Reviews
	git archive -o ./site-reviews-v$(VERSION).zip --prefix=site-reviews/ HEAD
	open .

.PHONY: analyse build bump compat db help mix mixsync open release sync test testall update watch zip
