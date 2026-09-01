<?php

/*
 * The same per-test isolation as the main suites — a DB transaction that rolls
 * back, hook and request resets, the commit and WP_IMPORTING tripwires — from
 * the file the main suite's Pest.php shares: tests/pest/Support/isolation.php.
 *
 * Helpers.php, beside this file, is loaded by Pest itself.
 */

$isolation = require __DIR__.'/../pest/Support/isolation.php';

uses()
    ->beforeEach($isolation['beforeEach'])
    ->afterEach($isolation['afterEach'])
    ->in(__DIR__);
