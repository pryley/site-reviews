<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Migrations\Migrate_7_0_0;
use GeminiLabs\SiteReviews\Modules\Console;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;
use function GeminiLabs\SiteReviews\Tests\withDatabaseAnswering;
use function GeminiLabs\SiteReviews\Tests\withDdlFreeDatabase;
use function GeminiLabs\SiteReviews\Tests\withTablesAnswering;

/*
 * v7.0.0: the settings option loses its version suffix, the ratings table gains
 * is_flagged, and the notification message's tags catch up with the ones the
 * plugin actually replaces.
 *
 * wp-env's ratings table already HAS is_flagged, so the column paths are driven
 * through a Tables that says it does not and a Database that swallows the ALTER
 * — the real one is DDL, which the per-test transaction cannot roll back.
 */

beforeEach(fn () => resetPluginState());

test('the v5 settings and the cloudflare cache are dropped', function () {
    update_option(OptionManager::databaseKey(5), ['settings' => ['general' => []]]);
    set_transient(glsr()->prefix.'cloudflare_ips', ['1.2.3.4']);

    expect((new Migrate_7_0_0())->run())->toBeTrue();

    expect(get_option(OptionManager::databaseKey(5)))->toBeFalse()
        ->and(get_transient(glsr()->prefix.'cloudflare_ips'))->toBeFalse();
});

test('a database that already has the column is stamped with the version', function () {
    delete_option(glsr()->prefix.'db_version');

    expect((new Migrate_7_0_0())->run())->toBeTrue();

    expect(get_option(glsr()->prefix.'db_version'))->toBe('1.3');
});

test('the missing column is added after is_pinned', function () {
    delete_option(glsr()->prefix.'db_version');

    withTablesAnswering(['columnExists' => false], function () {
        withDdlFreeDatabase(function ($fake) {
            (new Migrate_7_0_0())->migrateDatabase();

            expect($fake->queries[0])->toContain('ADD COLUMN is_flagged')
                ->and($fake->queries[0])->toContain('AFTER is_pinned');
        });
    });

    expect(get_option(glsr()->prefix.'db_version'))->toBe('1.3');
});

test('on sqlite the column is added without saying where', function () {
    // SQLite's ALTER TABLE has no AFTER clause.
    withTablesAnswering(['columnExists' => false, 'isSqlite' => true], function () {
        withDdlFreeDatabase(function ($fake) {
            (new Migrate_7_0_0())->migrateDatabase();

            expect($fake->queries[0])->toContain('ADD COLUMN is_flagged')
                ->and($fake->queries[0])->not->toContain('AFTER');
        });
    });
});

test('a refused ALTER is logged and the database version is not stamped', function () {
    delete_option(glsr()->prefix.'db_version');

    withTablesAnswering(['columnExists' => false], function () {
        withDatabaseAnswering([], function () {
            (new Migrate_7_0_0())->migrateDatabase();
        }, $writesFail = true);
    });

    expect(get_option(glsr()->prefix.'db_version'))->toBeFalse()
        ->and(glsr(Console::class)->get())->toContain('the [is_flagged] column was not added');
});

test('the migration bookkeeping is taken out of the settings', function () {
    // It moved to an option of its own; left where it was, it would be exported
    // and imported as though it were a setting.
    $settings = get_option(OptionManager::databaseKey());
    $settings['last_migration_run'] = 1600000000;
    update_option(OptionManager::databaseKey(), $settings);

    expect((new Migrate_7_0_0())->run())->toBeTrue();

    expect(get_option(OptionManager::databaseKey()))->not->toHaveKey('last_migration_run');
});

test('the notification message tags catch up with the ones the plugin replaces', function () {
    $settings = get_option(OptionManager::databaseKey());
    $settings['settings']['general']['notification_message'] =
        "{review_author}  - {review_ip}\n{review_link}\n{review_content}";
    update_option(OptionManager::databaseKey(), $settings);

    expect((new Migrate_7_0_0())->run())->toBeTrue();

    expect(get_option(OptionManager::databaseKey())['settings']['general']['notification_message'])
        ->toBe("{review_author} ({review_email}) - {review_ip}\n<a href=\"{edit_url}\">Edit Review</a>\n{review_content}");
});
