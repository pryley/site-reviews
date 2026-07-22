<?php

use GeminiLabs\SiteReviews\Migrations\Migrate_8_0_0;
use GeminiLabs\SiteReviews\Modules\Console;

use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;
use function GeminiLabs\SiteReviews\Tests\withDatabaseAnswering;

/*
 * v8.0.0: an index on the ratings table, the stats table, and the dismissed
 * notices moving off user meta. The three content migrations it delegates to
 * have their own tests.
 *
 * wp-env's ratings table already has the index, so the "index is missing" arm
 * is driven by a Database that reports no indexes and swallows the ALTER — the
 * real one is DDL, which the per-test transaction cannot roll back.
 */

beforeEach(fn () => resetPluginState());

test('a database that is already current is stamped, and the notices are dropped', function () {
    $user = createUser();
    update_user_meta($user, '_glsr_notices', ['welcome' => 123]);
    delete_option(glsr()->prefix.'db_version');

    expect((new Migrate_8_0_0())->migrateDatabase())->toBeTrue();

    expect(get_option(glsr()->prefix.'db_version'))->toBe('1.5');
    wp_cache_delete($user, 'user_meta');
    expect(get_user_meta($user, '_glsr_notices', true))->toBe('');
});

test('the missing index is added', function () {
    delete_option(glsr()->prefix.'db_version');

    withDatabaseAnswering(['dbGetResults' => []], function ($fake) {
        expect((new Migrate_8_0_0())->migrateDatabase())->toBeTrue();

        expect($fake->queries[0])->toContain('glsr_ratings_ip_address_index (ip_address)');
    });

    expect(get_option(glsr()->prefix.'db_version'))->toBe('1.5');
});

test('a refused ALTER is logged and the migration reports failure', function () {
    delete_option(glsr()->prefix.'db_version');

    withDatabaseAnswering(['dbGetResults' => []], function () {
        expect((new Migrate_8_0_0())->migrateDatabase())->toBeFalse();
    }, $writesFail = true);

    expect(get_option(glsr()->prefix.'db_version'))->toBeFalse()
        ->and(glsr(Console::class)->get())->toContain('the [ip_address_index] index was not added');
});

test('the whole migration runs the three content migrations and the database one', function () {
    delete_option(glsr()->prefix.'db_version');

    expect((new Migrate_8_0_0())->run())->toBeTrue();

    expect(get_option(glsr()->prefix.'db_version'))->toBe('1.5');
});
