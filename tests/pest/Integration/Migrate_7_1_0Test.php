<?php

use GeminiLabs\SiteReviews\Migrations\Migrate_7_1_0;
use GeminiLabs\SiteReviews\Modules\Console;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;
use function GeminiLabs\SiteReviews\Tests\withDatabaseAnswering;

/*
 * Two indexes on the ratings table. wp-env's schema already has both, so the
 * "index is missing" arms are driven by a Database that reports no indexes at
 * all and swallows the ALTERs — the real ones would be DDL, which the per-test
 * transaction cannot roll back.
 */

beforeEach(fn () => resetPluginState());

test('a database that already has both indexes is only stamped with the version', function () {
    delete_option(glsr()->prefix.'db_version');

    expect((new Migrate_7_1_0())->run())->toBeTrue();

    expect(get_option(glsr()->prefix.'db_version'))->toBe('1.4');
});

test('the missing indexes are added, and the database version stamped', function () {
    delete_option(glsr()->prefix.'db_version');

    withDatabaseAnswering(['dbGetResults' => []], function ($fake) {
        expect((new Migrate_7_1_0())->run())->toBeTrue();

        expect($fake->queries)->toHaveCount(2)
            ->and($fake->queries[0])->toContain('glsr_ratings_rating_type_is_approved_index (rating,type,is_approved)')
            ->and($fake->queries[1])->toContain('glsr_ratings_is_flagged_index (is_flagged)');
    });

    expect(get_option(glsr()->prefix.'db_version'))->toBe('1.4');
});

test('a refused ALTER is logged and the database version is not stamped', function () {
    delete_option(glsr()->prefix.'db_version');

    withDatabaseAnswering(['dbGetResults' => []], function () {
        expect((new Migrate_7_1_0())->run())->toBeTrue(); // the migration itself still succeeds
    }, $writesFail = true);

    expect(get_option(glsr()->prefix.'db_version'))->toBeFalse();
    expect(glsr(Console::class)->get())
        ->toContain('the [rating_type_is_approved] index was not added')
        ->toContain('the [is_flagged] index was not added');
});
