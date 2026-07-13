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
 * the files that need a known-good baseline of plugin settings — not globally,
 * because replacing the whole options array is not free and most tests do not
 * touch it. The migrations it used to run have moved to bootstrap.php, which
 * says why.
 */

use function GeminiLabs\SiteReviews\Tests\backupHooks;
use function GeminiLabs\SiteReviews\Tests\commitWasDeclared;
use function GeminiLabs\SiteReviews\Tests\emptyMailbox;
use function GeminiLabs\SiteReviews\Tests\purgeCommittedRows;
use function GeminiLabs\SiteReviews\Tests\resetRequestState;
use function GeminiLabs\SiteReviews\Tests\restoreHooks;

/*
 * The name of the current test's sentinel row (see the afterEach below). It is held here
 * rather than on $this, because $this is PHPUnit's TestCase and a property it does not
 * declare is a dynamic property, which PHP 8.2 deprecates.
 */
$sentinel = new stdClass();

uses()
    ->beforeEach(function () use ($sentinel) {
        global $wpdb;
        $wpdb->query('SET autocommit = 0');
        $wpdb->query('START TRANSACTION');
        $sentinel->name = 'glsr_sentinel_'.uniqid();
        $wpdb->insert($wpdb->options, [
            'option_name' => $sentinel->name,
            'option_value' => '1',
            'autoload' => 'no',
        ]);
        backupHooks();
        emptyMailbox();
    })
    ->afterEach(function () use ($sentinel) {
        global $wpdb;
        $wpdb->query('ROLLBACK');
        $wpdb->query('SET autocommit = 1');
        // The tripwire. The sentinel was written INSIDE the transaction, so a rollback
        // must take it away with everything else. If it is still there, the transaction
        // was committed while the test was running, every row the test wrote before that
        // point is now permanent, and the next test that asks for the same username or
        // slug will fail somewhere else entirely — which is exactly how this was found.
        //
        // DDL is what does it: MySQL commits the open transaction implicitly on CREATE,
        // ALTER and DROP TABLE. So does an explicit START TRANSACTION.
        //
        // A test that MEANS to do this says so with commitsTransaction() — the Import suite, whose
        // TableTmp is created and dropped per import, and the three tests that reach
        // Migrate::runAll(). Those are cleaned up by hand instead of being failed.
        $committed = (bool) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name = %s", $sentinel->name
        ));
        if ($committed) {
            $wpdb->delete($wpdb->options, ['option_name' => $sentinel->name]);
        }
        $declared = commitWasDeclared();
        commitWasDeclared(false);
        if ($committed && $declared) {
            purgeCommittedRows(); // autocommit is back on, so this sticks
        }
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
        if ($committed && !$declared) {
            throw new RuntimeException(
                'This test COMMITTED its transaction — the rows it wrote before that point '.
                'are now permanent, and will break a later test, in a later run, in another '.
                'file. Something it called ran DDL (CREATE/ALTER/DROP TABLE) or issued its '.
                'own START TRANSACTION. If that is intended, declare it with commitsTransaction().'
            );
        }
    })
    ->in('Unit', 'Integration', 'ThirdParty', 'Import');
