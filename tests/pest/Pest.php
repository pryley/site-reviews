<?php

/*
 * Test isolation, which is the whole job WP_UnitTestCase used to do for the
 * phpunit suite. Three kinds of state leak between tests, and all three are
 * reset here for every test — Unit and Integration alike:
 *
 *   the database   a transaction per test, rolled back afterwards. The plugin's
 *                  settings live in the options table, so even a "unit" test
 *                  that only builds a field writes to the database.
 *   the hooks      a filter a test adds would otherwise still be registered for
 *                  every test after it (ValidationTest adds a
 *                  `site-reviews/validators` filter in almost every test).
 *   the request    the logged-in user and the request superglobals.
 *
 * The object cache does NOT roll back with the transaction, so it is flushed.
 *
 * The suites differ in what they touch, not in how they isolate:
 *
 *   Unit        — logic against the booted WordPress: helpers, casts,
 *                 sanitizers, encryption, the HTML builders. No posts, terms,
 *                 users or reviews are created.
 *   Integration — creates content and exercises the database: the review
 *                 manager, the query builder, migrations, ajax submissions.
 *
 * The Setup trait the phpunit suite mixed into six of its test cases is ported
 * as resetPluginState() (Support/helpers.php) and called from a beforeEach in
 * those same six files — not globally: running the migrations for all 320 tests
 * would be a needless cost.
 */

use function GeminiLabs\SiteReviews\Tests\backupHooks;
use function GeminiLabs\SiteReviews\Tests\emptyMailbox;
use function GeminiLabs\SiteReviews\Tests\resetRequestState;
use function GeminiLabs\SiteReviews\Tests\restoreHooks;

uses()
    ->beforeEach(function () {
        global $wpdb;
        $wpdb->query('SET autocommit = 0');
        $wpdb->query('START TRANSACTION');
        backupHooks();
        emptyMailbox();
    })
    ->afterEach(function () {
        global $wpdb;
        $wpdb->query('ROLLBACK');
        $wpdb->query('SET autocommit = 1');
        restoreHooks();
        resetRequestState();
        wp_cache_flush();
        // Roles are the one piece of state a rollback cannot restore on its own.
        // WP_Roles::remove_cap()/add_cap() change $wp_roles IN MEMORY and write the
        // wp_user_roles option; get_role() then hands back the cached WP_Role object.
        // The rollback restores the option, but the global still holds the modified
        // role, so it has to be dropped — WP_Roles::for_site() re-reads the option
        // when the global is rebuilt.
        unset($GLOBALS['wp_roles']);
    })
    ->in('Unit', 'Integration', 'ThirdParty', 'Import');
