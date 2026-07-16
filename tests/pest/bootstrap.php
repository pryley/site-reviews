<?php

/*
 * Boots a REAL WordPress — by default the wp-env instance the suite runs in
 * (see .wp-env.json / the Makefile) — with Site Reviews active. Core's test
 * framework is avoided: it needs PHPUnit 9, Pest needs 12. Support/ supplies
 * the factories and admin-ajax harness; isolation is a per-test DB transaction
 * that rolls back (see Pest.php).
 *
 * Constants are defined BEFORE wp-load so the plugin sees them as it loads
 * (Router, Tables, Queue, AdminController, HookProxy branch on them), and so
 * mu-plugins/site-reviews-tests.php can load the integration stubs and disable
 * the deprecated fallbacks before plugins_loaded fires.
 *
 * WP_ROOT overrides the WordPress path, to run against any install with a
 * DISPOSABLE database (the tests write to it between transaction boundaries).
 */

define('GLSR_UNIT_TESTS', true);

/*
 * The suite never touches the network — belt and braces. blockHttpRequests()
 * (below) turns an un-intercepted request into a WP_Error naming the URL, but
 * it is a filter and can be removed. WP_HTTP_BLOCK_EXTERNAL is checked inside
 * WP_Http::request() after the filter, catching anything that slips through;
 * WP_ACCESSIBLE_HOSTS is empty, so nothing is allowed, not even wordpress.org.
 *
 * Everything the plugin talks to (licence, updates, geolocation, CAPTCHA,
 * tutorials) is mocked per test with interceptHttp(); mail the same way.
 * Defined before wp-load.php, where WordPress reads them.
 */
define('WP_HTTP_BLOCK_EXTERNAL', true);
define('WP_ACCESSIBLE_HOSTS', '');


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

/*
 * filter_input() reads the SAPI request table, not the superglobals, and a CLI process has
 * none — so it returns null whatever $_GET holds, killing ~70 call sites. The suite shadows
 * filter_input() in the plugin's namespaces (an unqualified call resolves there first),
 * leaving production semantics untouched. See Support/filter-input.php.
 */
require __DIR__.'/Support/filter-input.php';

require "{$root}/wp-load.php";

if (!function_exists('glsr')) {
    fwrite(STDOUT, 'The Site Reviews plugin is not active in the test WordPress.'.PHP_EOL);
    exit(1);
}
if (!defined('GLSR_TEST_MU_PLUGIN')) {
    fwrite(STDOUT, implode(PHP_EOL, [
        'The test mu-plugin did not load, so the integration stubs are missing and the',
        'deprecated v5-v8 fallbacks are still active, so results would be wrong.',
        'Map tests/pest/mu-plugins into the install:',
        '',
        '    wp-env  → the "mappings" key in .wp-env.json (already configured)',
        '    WP_ROOT → symlink it into wp-content/mu-plugins',
        '',
    ]));
    exit(1);
}

// Support/ is autoloaded by composer (autoload-dev): PSR-4 for classes and
// traits, "files" for helpers.php.

// Create the plugin's tables. Idempotent, and run OUTSIDE the per-test
// transactions — its DDL would implicitly commit one.
glsr(\GeminiLabs\SiteReviews\Install::class)->run();

/*
 * Migrate once, here. Run per test it would re-run all nineteen migrations against an empty
 * transaction and roll them back — ~8000 runs a suite (measured 61s -> 39s). Six migrations
 * contain DDL, which implicitly COMMITs the isolating transaction; MigrateReviews also wraps
 * each pass in a real START TRANSACTION/COMMIT. Run before the first test opens a transaction,
 * neither bites. A test that needs a migration runs it itself (Migrate_8_1_0Test), and the
 * Tools page's "Migrate Plugin" button has its own test — both declare commitsTransaction().
 */
glsr(\GeminiLabs\SiteReviews\Modules\Migrate::class)->runAll();

/*
 * Photograph the Application's storage as a fresh request has it. The Storage trait is an
 * Arguments object on the singleton — no option, hook or table — so Pest.php's teardown cannot
 * reach it, yet twenty registers write to it and several leak between tests. restoreStorage()
 * puts it back after every test. See snapshotStorage() in Support/helpers.php.
 */
\GeminiLabs\SiteReviews\Tests\snapshotStorage();

/*
 * Start from a known-empty database. A test that COMMITs its transaction (see
 * commitsTransaction()) and dies before cleanup leaves rows behind; user_login is unique and
 * the factory reuses "User 120" each run, so the leftovers collide with the test that leaked
 * them until the table is emptied by hand.
 */
\GeminiLabs\SiteReviews\Tests\purgeCommittedRows();

/*
 * The console log is a file, appended across runs; the harness clears it after every test
 * (resetGlobalState), and this clears whatever booting the plugin just logged so the first
 * test starts as clean as the rest.
 */
glsr(\GeminiLabs\SiteReviews\Modules\Console::class)->clear();

/*
 * Nothing is scheduled: the plugin queues a geolocation lookup, a notification and an avatar
 * per review, and the suite creates thousands. Rebinding rather than a flag inside Queue —
 * Queue is a shared singleton (Provider.php) resolved via glsr(Queue::class), and bind() drops
 * the stale instance, so this replaces it everywhere. See Support/NullQueue.php.
 */
glsr()->bind(
    \GeminiLabs\SiteReviews\Modules\Queue::class,
    \GeminiLabs\SiteReviews\Tests\NullQueue::class,
    $shared = true
);

/*
 * Rethrow throwables from proxied hooks instead of logging and swallowing them. HookProxy
 * catches \Throwable so a third party's bad data degrades gracefully in production, but here a
 * subject that throws inside a hook would pass silently, and wp_die()/wp_redirect() are
 * intercepted BY THROWING (Support/InteractsWithExits.php), which the catch would eat. The
 * plugin exposes this as a filter, so anyone debugging a site can turn it on too.
 */
add_filter('site-reviews/hook/rethrow', '__return_true');

// Nothing may actually leave the test container.
\GeminiLabs\SiteReviews\Tests\interceptMail();
\GeminiLabs\SiteReviews\Tests\blockHttpRequests();
