#!/usr/bin/env bash
# usage: travis.sh before|after
# v1.0.0

if [ $1 == 'before' ]; then
	composer self-update
	# install php-coveralls to send coverage info
	composer init --require=satooshi/php-coveralls:0.7.0 -n
	composer install --no-interaction --ignore-platform-reqs

elif [ $1 == 'after' ]; then
	# send coverage data to coveralls
	php vendor/bin/coveralls --verbose --exclude-no-stmt
	# get scrutinizer ocular and run it
	wget https://scrutinizer-ci.com/ocular.phar
	ocular.phar code-coverage:upload --format=php-clover ./tmp/coverage.xml
fi
