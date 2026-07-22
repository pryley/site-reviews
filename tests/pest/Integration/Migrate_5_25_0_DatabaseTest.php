<?php

use GeminiLabs\SiteReviews\Migrations\Migrate_5_25_0\MigrateDatabase;
use GeminiLabs\SiteReviews\Modules\Console;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;
use function GeminiLabs\SiteReviews\Tests\withDdlFreeDatabase;
use function GeminiLabs\SiteReviews\Tests\withTablesAnswering;

/*
 * The v5.25.0 database pass: install anything missing, add the ratings table's
 * `terms` column, drop the rows that no longer point at a review, and recount.
 *
 * wp-env's ratings table already has the column, so the paths that add it run
 * against a Tables that says otherwise and a Database that swallows the ALTER —
 * the real one is DDL, which the per-test transaction cannot roll back.
 */

beforeEach(fn () => resetPluginState());

test('a database that is already current is left at the version it has', function () {
    update_option(glsr()->prefix.'db_version', '1.5');

    expect(glsr(MigrateDatabase::class)->run())->toBeTrue();

    expect(get_option(glsr()->prefix.'db_version'))->toBe('1.5');
});

test('a database older than the column it already has is stamped 1.1', function () {
    update_option(glsr()->prefix.'db_version', '1.0');

    expect(glsr(MigrateDatabase::class)->run())->toBeTrue();

    expect(get_option(glsr()->prefix.'db_version'))->toBe('1.1');
});

test('the missing column is added after url, and the version stamped', function () {
    update_option(glsr()->prefix.'db_version', '1.0');
    // The column is absent when the migration looks, and there once the ALTER
    // has run — which is how the migration decides the ALTER worked.
    $calls = 0;
    withTablesAnswering(['columnExists' => function () use (&$calls) {
        return ++$calls > 1;
    }], function () {
        withDdlFreeDatabase(function ($fake) {
            expect(glsr(MigrateDatabase::class)->run())->toBeTrue();

            expect(implode("\n", $fake->queries))->toContain('ADD COLUMN terms')
                ->toContain('AFTER url');
        });
    });

    expect(get_option(glsr()->prefix.'db_version'))->toBe('1.1');
});

test('on sqlite the column is added without saying where', function () {
    // SQLite's ALTER TABLE has no AFTER clause.
    withTablesAnswering(['columnExists' => false, 'isSqlite' => true], function () {
        withDdlFreeDatabase(function ($fake) {
            glsr(MigrateDatabase::class)->run();

            expect(implode("\n", $fake->queries))->toContain('ADD COLUMN terms')
                ->not->toContain('AFTER');
        });
    });
});

test('an ALTER that leaves the column missing is logged', function () {
    // The migration does not read the query's return value: it asks the table
    // again, so an ALTER that reported success but changed nothing is caught.
    withTablesAnswering(['columnExists' => false], function () {
        withDdlFreeDatabase(function () {
            expect(glsr(MigrateDatabase::class)->run())->toBeTrue(); // the pass still reports success
        });
    });

    expect(glsr(Console::class)->get())->toContain('the [terms] column was not added');
});

// NOTE (ceiling): repairDatabase()'s network branch — the switch_to_blog() loop over
// Install::sites() — is gated on is_plugin_active_for_network(), which returns false on
// any single site because it asks is_multisite() first. It is unreachable from this
// suite by construction; tests/multisite is where it belongs.
