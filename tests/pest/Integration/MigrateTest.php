<?php

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Modules\Migrate;

use GeminiLabs\SiteReviews\Tests\NullQueue;

use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The migration orchestrator. The real migrations ran once in bootstrap; these
 * tests drive the MACHINERY — discovery, pending bookkeeping, the queueing
 * guard, and the two ways a migration class can disappoint — against fixtures,
 * so nothing here touches a real Migrate_* class or its DDL.
 */

beforeEach(function () {
    resetPluginState();
    require_once glsr()->path('tests/pest/fixtures/migrations/fake-migration-classes.php');
});

test('with no migrations at all there is nothing to need', function () {
    $migrate = new Migrate();
    $migrate->migrations = [];

    expect($migrate->isMigrationNeeded())->toBeFalse();
});

test('the pending versions read as versions, not class names', function () {
    // What MigrationNotice prints: Migrate_0_0_1 the class is 0.0.1 the version.
    // (0.0.x deliberately: a REAL migration name would be found in the stored
    // bookkeeping, already marked as run, and so never pending.)
    $migrate = new Migrate();
    $migrate->migrations = ['Migrate_0_0_1', 'Migrate_0_0_2'];

    expect($migrate->pendingVersions())->toBe('0.0.1, 0.0.2');
});

test('discovery reads the directory and skips whatever is not a migration', function () {
    // Driven through the site-reviews/path seam: the fixture directory holds one
    // well-formed migration file and one php file that is not one. The real
    // directory contains only Migrate_* files, so the skip is unreachable there.
    $fixtures = glsr()->path('tests/pest/fixtures/migrations');
    $filter = fn ($path, $file) => 'plugin/Migrations' === $file ? $fixtures : $path;
    add_filter('site-reviews/path', $filter, 10, 2);
    try {
        $migrate = new Migrate();

        expect($migrate->migrations)->toBe(['Migrate_1_0_0']);
    } finally {
        remove_filter('site-reviews/path', $filter, 10);
    }
});

test('a class that is not a migration is skipped, and one that fails stays pending', function () {
    $migrate = new Migrate();
    $migrate->migrations = ['Migrate_0_0_1', 'Migrate_0_0_2']; // the fixtures

    $migrate->run();

    $stored = get_option($migrate->migrationsKey);
    expect($stored)->toBe(['Migrate_0_0_1' => false, 'Migrate_0_0_2' => false])
        ->and($migrate->lastRun())->toBeGreaterThan(0);
});

test('a migration cannot be queued while one is pending or too soon after a run', function () {
    $migrate = new Migrate();

    NullQueue::$isPending = true;
    expect($migrate->canQueue())->toBeFalse();

    // A run that ended without clearing its trigger has failed; retrying it
    // immediately cannot succeed. One attempt per cooldown period.
    NullQueue::$isPending = false;
    update_option($migrate->migrationsLastRun, current_time('timestamp'));
    expect($migrate->canQueue())->toBeFalse();

    update_option($migrate->migrationsLastRun, current_time('timestamp') - HOUR_IN_SECONDS - 1);
    expect($migrate->canQueue())->toBeTrue();
});

test('a stale db_version with nothing pending re-runs all migrations', function () {
    // A restore can revert glsr_db_version while the stored bookkeeping still
    // says every migration ran. Nothing is pending, so no migration re-stamps
    // the option, and Application::init() queues the migration on every
    // request, forever. The bookkeeping is what lies: run() must reset it and
    // re-run. The routing is the test — that the real ladder ends at the
    // current version is asserted in ToolsControllerTest's Hard Reset test,
    // which already pays for the transaction commit a real runAll() causes.
    update_option(glsr()->prefix.'db_version', '1.4');
    $migrate = new class extends Migrate {
        public bool $ranAll = false;

        public function runAll(): void
        {
            $this->ranAll = true;
        }
    };
    $migrate->migrations = [];

    $migrate->run();

    expect($migrate->ranAll)->toBeTrue();
});

test('a pending migration owns the version stamp, reconcile stays out', function () {
    // While a migration is still pending (here: the fixture that always
    // fails), the version stamp is its job; reconcile must not stamp over it.
    update_option(glsr()->prefix.'db_version', '1.4');
    $migrate = new Migrate();
    $migrate->migrations = ['Migrate_0_0_2'];

    $migrate->run();

    expect(get_option(glsr()->prefix.'db_version'))->toBe('1.4');
});

test('when the database itself needs migrating, everything is re-run from the start', function () {
    // The signature of a restored posts-only backup (a published review whose
    // rating row is not approved) forces run() through runAll(): the stored
    // bookkeeping is reset so every migration is pending again.
    $review = createReview();
    glsr(Database::class)->update('ratings', ['is_approved' => 0], ['review_id' => $review->ID]);
    expect(glsr(Database::class)->isMigrationNeeded())->toBeTrue();

    $migrate = new Migrate();
    $migrate->migrations = []; // nothing to actually run — the routing is the test

    $migrate->run();

    expect(get_option($migrate->migrationsKey))->toBe([]); // reset, then re-recorded from scratch
});
