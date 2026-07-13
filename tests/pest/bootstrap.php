<?php

/*
 * Boots a REAL WordPress — by default the wp-env instance the suite runs
 * inside (see .wp-env.json / the Makefile) — with Site Reviews active.
 *
 * Deliberately NOT WordPress core's test framework (WP_UnitTestCase,
 * WP_Ajax_UnitTestCase, factories): it is pinned to PHPUnit 9, which Pest
 * cannot run on. The pieces of it the suite actually needs are reimplemented
 * in Support/ — post/term/user factories and the admin-ajax harness — and
 * every test isolates itself with a DB transaction that rolls back instead
 * (see Pest.php).
 *
 * The constants are defined BEFORE wp-load so the plugin sees them as it
 * loads (Router, Tables, Queue, AdminController and HookProxy all branch
 * on them), exactly as tests/phpunit/bootstrap.php does on
 * `muplugins_loaded`. They also arm tests/pest/mu-plugins/site-reviews-tests.php,
 * which loads the integration stubs and disables the deprecated fallbacks
 * before `plugins_loaded` fires — the one thing that cannot be done from
 * here, because deprecated.php registers its filters on that hook.
 *
 * WP_ROOT overrides the WordPress path to run against any other install
 * (e.g. a Local site with a DISPOSABLE database — the tests write to it
 * between transaction boundaries).
 */

define('GLSR_UNIT_TESTS', true);
define('PHPUNIT_TESTING', true);

$root = rtrim((string) (getenv('WP_ROOT') ?: '/var/www/html'), '/');
if (!file_exists("{$root}/wp-load.php")) {
    fwrite(STDOUT, implode(PHP_EOL, [
        "wp-load.php not found at: {$root}",
        'Run the suite inside wp-env (`make test`),',
        'or point WP_ROOT at a WordPress install with a disposable database.',
        '',
    ]));
    exit(1);
}

// Shims WP expects from a web SAPI when loaded from the CLI.
$_SERVER['HTTP_HOST'] ??= 'localhost';
$_SERVER['REMOTE_ADDR'] ??= '127.0.0.1';
$_SERVER['REQUEST_METHOD'] ??= 'GET';
$_SERVER['REQUEST_URI'] ??= '/';
$_SERVER['SERVER_NAME'] ??= 'localhost';
$_SERVER['SERVER_PROTOCOL'] ??= 'HTTP/1.1';

require "{$root}/wp-load.php";

if (!function_exists('glsr')) {
    fwrite(STDOUT, 'The Site Reviews plugin is not active in the test WordPress.'.PHP_EOL);
    exit(1);
}
if (!defined('GLSR_TEST_MU_PLUGIN')) {
    fwrite(STDOUT, implode(PHP_EOL, [
        'The test mu-plugin did not load, so the integration stubs are missing and the',
        'deprecated v5-v8 fallbacks are still active — results would not match the',
        'phpunit suite. Map tests/pest/mu-plugins into the install:',
        '',
        '    wp-env  → the "mappings" key in .wp-env.json (already configured)',
        '    WP_ROOT → symlink it into wp-content/mu-plugins',
        '',
    ]));
    exit(1);
}

// Support/ is autoloaded by composer (see autoload-dev in composer.json):
// PSR-4 for the classes and traits, "files" for helpers.php.

// The plugin's tables are created on activation; run the installer anyway so
// a fresh or half-migrated database is usable. It is idempotent, and it runs
// OUTSIDE the per-test transactions (DDL would implicitly commit one).
glsr(\GeminiLabs\SiteReviews\Install::class)->run();

/*
 * And migrate, ONCE, here — for the same reason, and for two more.
 *
 * This used to live in resetPluginState(), which is the beforeEach of thirty-five test
 * files. runAll() deletes the migrations option first, so every one of those tests
 * re-ran all nineteen migrations from scratch and then rolled the whole thing back:
 * roughly eight thousand migration runs a suite, against a database that (inside the
 * transaction) had nothing in it to migrate, and whose settings changes were overwritten
 * by the replace($defaults) on the very next line anyway. Measured: 61s -> 39s.
 *
 * Six of the migrations contain DDL (Migrate_6_0_0, Migrate_6_2_1, Migrate_7_0_0,
 * Migrate_7_1_0, Migrate_8_0_0, Migrate_5_25_0/MigrateDatabase). Every one of them is
 * guarded — "does this index already exist?" — so on an already-migrated database none
 * of it fires, which is the only reason the per-test run was not implicitly COMMITting
 * the transaction that isolates the tests from each other. Run from here it cannot,
 * whatever those guards do in future.
 *
 * A test that wants a migration run runs it itself (Migrate_8_1_0Test does exactly
 * that), and the Tools page's "Migrate Plugin" button has its own test.
 */
glsr(\GeminiLabs\SiteReviews\Modules\Migrate::class)->runAll();

// Nothing may actually leave the test container.
\GeminiLabs\SiteReviews\Tests\interceptMail();
\GeminiLabs\SiteReviews\Tests\blockHttpRequests();
