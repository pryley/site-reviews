<?php

use GeminiLabs\SiteReviews\Migrations\Migrate_6_2_1;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;
use function GeminiLabs\SiteReviews\Tests\withDatabaseAnswering;
use function GeminiLabs\SiteReviews\Tests\withDdlFreeDatabase;
use function GeminiLabs\SiteReviews\Tests\withTablesAnswering;

/*
 * Repairs the PRIMARY key of the three assignment tables — the v6.0.0 migration
 * built it a way MariaDB would not accept — and removes duplicate custom field
 * values.
 *
 * wp-env's tables have their PRIMARY key, so the repair is driven by a Database
 * that reports the constraints of a table that does not, and swallows the ALTERs
 * that would follow: they are DDL, which the per-test transaction cannot roll
 * back.
 */

beforeEach(fn () => resetPluginState());

test('a table that already has its primary key is only asked for its constraints', function () {
    withDdlFreeDatabase(function ($fake) {
        expect((new Migrate_6_2_1())->run())->toBeTrue();

        // the constraints all exist, so addForeignConstraints() issues nothing either
        expect(implode("\n", $fake->queries))->not->toContain('ALTER TABLE');
    });
});

test('a table that is not innodb is skipped entirely', function () {
    // Foreign keys and this repair are both InnoDB-only; a MyISAM assignment
    // table is left as it is rather than half-repaired.
    withTablesAnswering(['isInnodb' => false], function () {
        withDdlFreeDatabase(function ($fake) {
            expect((new Migrate_6_2_1())->run())->toBeTrue();

            expect(implode("\n", $fake->queries))->not->toContain('ALTER TABLE');
        });
    });
});

test('a missing primary key is rebuilt from the unique index it replaces', function () {
    // The constraints of a table repaired the old way: the unique index is
    // there and PRIMARY is not.
    $constraints = [
        glsr()->prefix.'assigned_posts_rating_id_post_id_unique',
        glsr()->prefix.'assigned_terms_rating_id_term_id_unique',
        glsr()->prefix.'assigned_users_rating_id_user_id_unique',
    ];
    withDatabaseAnswering(['dbGetCol' => $constraints], function ($fake) use ($constraints) {
        expect((new Migrate_6_2_1())->run())->toBeTrue();

        $sql = implode("\n", $fake->queries);
        expect($sql)->toContain("DROP INDEX {$constraints[0]}")
            ->toContain('ADD PRIMARY KEY (rating_id,post_id)')
            ->toContain('ADD PRIMARY KEY (rating_id,term_id)')
            ->toContain('ADD PRIMARY KEY (rating_id,user_id)');
    });
});

test('a missing primary key is rebuilt even where there is no unique index to drop', function () {
    withDatabaseAnswering(['dbGetCol' => ['some_other_constraint']], function ($fake) {
        expect((new Migrate_6_2_1())->run())->toBeTrue();

        $sql = implode("\n", $fake->queries);
        expect($sql)->not->toContain('DROP INDEX')
            ->and($sql)->toContain('ADD PRIMARY KEY (rating_id,post_id)');
    });
});

test('only the newest of a duplicated custom field value survives', function () {
    global $wpdb;
    $review = createReview();
    $post = createPost();
    // Two rows for the same key, which is what the duplicate looks like in the
    // table; add_post_meta() is the only way to write the second one.
    foreach ([$review->ID, $post] as $postId) {
        add_post_meta($postId, '_custom_colour', 'first');
        add_post_meta($postId, '_custom_colour', 'second');
        add_post_meta($postId, '_not_custom', 'first');
        add_post_meta($postId, '_not_custom', 'second');
    }

    expect((new Migrate_6_2_1())->run())->toBeTrue();

    wp_cache_delete($review->ID, 'post_meta');
    wp_cache_delete($post, 'post_meta');
    expect(get_post_meta($review->ID, '_custom_colour'))->toBe(['second'])
        ->and(get_post_meta($review->ID, '_not_custom'))->toBe(['first', 'second'])
        // the review post type is the only one swept
        ->and(get_post_meta($post, '_custom_colour'))->toBe(['first', 'second']);
});
