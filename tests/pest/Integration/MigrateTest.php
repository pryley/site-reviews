<?php

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Modules\Migrate;

use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The migration orchestrator. The real migrations ran once in bootstrap; these
 * tests drive the MACHINERY — discovery, pending bookkeeping, and the two ways
 * a migration class can disappoint — against fixtures, so nothing here touches
 * a real Migrate_* class or its DDL.
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
