<?php

/*
 * Per-test isolation, applied to every suite. Three kinds of state leak between
 * tests and are reset here:
 *
 *   database — a transaction per test, rolled back afterwards. The plugin's
 *              settings live in the options table, so even a field-building
 *              "unit" test writes to it.
 *   hooks    — a filter a test adds would otherwise stay registered for every
 *              test after it.
 *   request  — the logged-in user and the request superglobals.
 *
 * The object cache does NOT roll back, so it is flushed. Suites differ in what
 * they touch, not how they isolate: Unit exercises logic (no posts/terms/users/
 * reviews created), Integration creates content and hits the database.
 *
 * resetPluginState() (Support/helpers.php) restores a known-good baseline of
 * plugin settings from a beforeEach in the files that need one — not globally,
 * since replacing the whole options array is not free and most tests skip it.
 */

use function GeminiLabs\SiteReviews\Tests\backupHooks;
use function GeminiLabs\SiteReviews\Tests\commitWasDeclared;
use function GeminiLabs\SiteReviews\Tests\emptyMailbox;
use function GeminiLabs\SiteReviews\Tests\purgeCommittedRows;
use function GeminiLabs\SiteReviews\Tests\resetGlobalState;
use function GeminiLabs\SiteReviews\Tests\resetRequestState;
use function GeminiLabs\SiteReviews\Tests\restoreHooks;
use function GeminiLabs\SiteReviews\Tests\wpImportingWasDeclared;

/*
 * Name of the current test's sentinel row (see afterEach). Held here, not on $this: $this is
 * PHPUnit's TestCase and an undeclared property is a dynamic property, deprecated in PHP 8.2.
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
        // Tripwire: the sentinel was written INSIDE the transaction, so the rollback must remove
        // it. If it survives, the transaction was committed mid-test, every earlier write is now
        // permanent, and a later test asking for the same username or slug fails elsewhere. DDL
        // does it (MySQL commits implicitly on CREATE/ALTER/DROP TABLE), as does an explicit
        // START TRANSACTION. A test that MEANS to commit says so with commitsTransaction() — the
        // Import suite and the three tests reaching Migrate::runAll(); those are cleaned up by
        // hand rather than failed.
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
        // The object cache does not roll back either; flush it BEFORE resetGlobalState() re-reads
        // the plugin's settings, which would otherwise get the copy cached before the ROLLBACK.
        wp_cache_flush();
        // Everything else a transaction cannot roll back: globals, container, the plugin's
        // in-memory session and settings, the fakes' statics. If a test passes alone but fails
        // under `make test:random`, what it leaned on is missing from resetGlobalState().
        resetGlobalState();
        if ($committed && !$declared) {
            throw new RuntimeException(
                'This test COMMITTED its transaction — the rows it wrote before that point '.
                'are now permanent, and will break a later test, in a later run, in another '.
                'file. Something it called ran DDL (CREATE/ALTER/DROP TABLE) or issued its '.
                'own START TRANSACTION. If that is intended, declare it with commitsTransaction().'
            );
        }
        // WP_IMPORTING is a one-way door: define() cannot be undone, and the plugin reads it in
        // fourteen places to mean "this review did not come from a person filling in a form". A
        // test outside the Import suite that defines it silently changes every test after it — no
        // avatar, no verification email, no count recalculation, no cache flush, and the protected
        // fields (is_pinned, is_verified, ip_address) stop being protected. The Import suite is
        // declared LAST in phpunit.xml precisely so it may.
        //
        // It is DECLARED, not detected: Pest compiles each test file into an eval()'d class, so
        // there is no test directory to check. The Import suite says definesWpImporting() in its
        // beforeEach, exactly as a DDL test says commitsTransaction(); anything else is a bug.
        $wasImporting = wpImportingWasDeclared();
        wpImportingWasDeclared(false);
        if (defined('WP_IMPORTING') && !$wasImporting) {
            throw new RuntimeException(
                'This test caused WP_IMPORTING to be defined, and did not say so. The constant '.
                'cannot be unset, so EVERY test that runs after it in this process is now running '.
                'as though its reviews were imported rather than submitted: no avatar, no '.
                'verification email, no recalculated counts, no cache flush, and is_pinned / '.
                'is_verified / ip_address are no longer protected fields. Anything that reaches '.
                'ImportManager, ProcessCsvFile or ImportReviewsAttachments belongs in the Import '.
                'suite (tests/pest/Import/), which phpunit.xml declares LAST for this reason — and '.
                'which says definesWpImporting() in its beforeEach.'
            );
        }
    })
    ->in('Unit', 'Integration', 'ThirdParty', 'Import');
