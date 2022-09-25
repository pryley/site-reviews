VERSION ?= $(shell perl -lne 'm{Stable tag: .*?(.+)} and print $$1' readme.txt)

.PHONY: analyse
analyse: ## Run phpstan analyser
	./vendor/bin/phpstan analyse --memory-limit 1G

.PHONY: build
build: ## Build all assets and languages
	npx gulp
	make mix

.PHONY: db
db: ## Open the database in TablePlus
	@open mysql://dev:dev@127.0.0.1/site-reviews?enviroment=local&name=Localhost&safeModeLevel=0&advancedSafeModeLevel=0

.PHONY: help
help:  ## Display help
	@awk -F ':|##' '/^[^\t].+?:.*?##/ {printf "\033[36m%-30s\033[0m %s\n", $$1, $$NF}' $(MAKEFILE_LIST) | sort

.PHONY: mix
mix: ## Build all assets
	npx mix --production
	npx rollup -c

.PHONY: mixsync
mixsync: ## Build all assets and sync
	make mix
	make sync

.PHONY: open
open: ## Open the development site in the default browser
	@open http://site-reviews.test/wp/wp-admin/edit.php?post_type=site-review

.PHONY: release
release: ## Release a new version of Site Reviews
	sh ./release.sh

.PHONY: sync
sync: ## Sync plugin files to development site
	@rsync --archive --recursive --delete \
	--exclude={'/+','/*.md','/*.js','/*.json','/*.sh','/*.lock','/*.neon','/Makefile','/phpunit.xml','/tests'} \
	--filter=":- .gitignore" \
	. ~/Sites/site-reviews/public/app/plugins/site-reviews/

.PHONY: test
test: ## Run all phpunit tests
	./vendor/bin/phpunit

.PHONY: testall
testall: ## Run phpstan analyser and all phpunit tests
	./vendor/bin/phpstan analyse --memory-limit 1G
	./vendor/bin/phpunit

.PHONY: update
update: ## Update Composer and NPM
	valet composer update
	npm-check -u

.PHONY: watch
watch: ## Build all plugin assets and run Browsersync
	npm run watch

.PHONY: zip
zip: ## Create a zip archive of Site Reviews
	git archive -o ./site-reviews-v$(VERSION).zip --prefix=site-reviews/ HEAD
