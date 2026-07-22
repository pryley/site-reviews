<?php

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Migrations\Migrate_5_25_0\MigrateReviews;

use function GeminiLabs\SiteReviews\Tests\commitsTransaction;
use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The pass that moves a review out of post meta and into the custom tables: a
 * row per review in the ratings table, and a row per assignment in each of the
 * assignment tables. Reviews before v5.25.0 were nothing but a post and its
 * meta, so the fixtures here are posts and meta — glsr_create_review() would
 * write the very rows this migration exists to create.
 *
 * Each of the four passes is wrapped in Database::beginTransaction() /
 * finishTransaction(), which on InnoDB is a literal START TRANSACTION and
 * COMMIT, so every test here declares commitsTransaction().
 */

beforeEach(function () {
    resetPluginState();
    commitsTransaction();
});

test('a review that is only a post and its meta becomes a rating row', function () {
    $review = legacyReview([
        '_author' => 'Jane Doe',
        '_avatar' => 'https://example.org/avatar.png',
        '_email' => 'jane@example.org',
        '_ip_address' => '127.0.0.1',
        '_pinned' => '1',
        '_rating' => '4',
        '_review_type' => 'local',
        '_url' => 'https://example.org/reviewed-elsewhere',
    ]);

    expect(glsr(MigrateReviews::class)->run())->toBeTrue();

    expect(ratingRow($review))->toMatchArray([
        'avatar' => 'https://example.org/avatar.png',
        'email' => 'jane@example.org',
        'ip_address' => '127.0.0.1',
        'is_approved' => '1', // the post is published
        'is_pinned' => '1',
        'name' => 'Jane Doe',
        'rating' => '4',
        'review_id' => (string) $review,
        'type' => 'local',
        'url' => '', // a local review has no url, whatever the meta says
    ]);
});

test('a review with no type at all is a local one, and an unpublished one is unapproved', function () {
    $review = legacyReview(['_author' => 'Jane Doe', '_rating' => '3'], 'pending');

    expect(glsr(MigrateReviews::class)->run())->toBeTrue();

    expect(ratingRow($review))->toMatchArray([
        'is_approved' => '0',
        'type' => 'local',
    ]);
});

test('a review that already has a rating row is not migrated twice', function () {
    $review = legacyReview(['_author' => 'Jane Doe', '_rating' => '5']);
    glsr(MigrateReviews::class)->run();
    $before = ratingRow($review);

    glsr(MigrateReviews::class)->run();

    expect(ratingRows($review))->toHaveCount(1)
        ->and(ratingRow($review))->toBe($before);
});

test('a serialized meta value is not mistaken for a rating field', function () {
    // _custom holds an array; the rating fields are all scalars, so anything
    // that unserializes to an array is skipped rather than cast to one.
    $review = legacyReview([
        '_author' => 'Jane Doe',
        '_custom' => ['colour' => 'blue'],
        '_rating' => '5',
    ]);

    expect(glsr(MigrateReviews::class)->run())->toBeTrue();

    expect(ratingRow($review))->toMatchArray(['name' => 'Jane Doe', 'rating' => '5']);
});

test('the post a review was assigned to becomes an assignment row', function () {
    $assignedTo = createPost();
    $unassigned = legacyReview(['_author' => 'Jane Doe', '_rating' => '5', '_assigned_to' => '0']);
    $review = legacyReview([
        '_assigned_to' => (string) $assignedTo,
        '_author' => 'Jane Doe',
        '_rating' => '5',
    ]);

    expect(glsr(MigrateReviews::class)->run())->toBeTrue();

    expect(assignedPosts($review))->toBe([['post_id' => (string) $assignedTo, 'is_published' => '1']])
        ->and(assignedPosts($unassigned))->toBe([]); // 0 is not an assignment
});

test('the categories a review was in become assignment rows', function () {
    // An EXISTING term, not one the factory makes: the pass commits, so a term
    // created here would outlive the rollback (purgeCommittedRows sweeps posts
    // and users, not terms) and collide with itself on the next run.
    $term = (int) get_option('default_category');
    $review = legacyReview(['_author' => 'Jane Doe', '_rating' => '5']);
    wp_set_object_terms($review, [$term], 'category');

    expect(glsr(MigrateReviews::class)->run())->toBeTrue();

    expect(assignedTerms($review))->toBe([(string) $term]);
});

test('the custom fields of a review become meta of their own', function () {
    $review = legacyReview([
        '_author' => 'Jane Doe',
        '_custom' => ['colour' => 'blue', 'size' => 'large'],
        '_rating' => '5',
    ]);

    expect(glsr(MigrateReviews::class)->run())->toBeTrue();

    wp_cache_delete($review, 'post_meta');
    expect(get_post_meta($review, '_custom_colour', true))->toBe('blue')
        ->and(get_post_meta($review, '_custom_size', true))->toBe('large')
        ->and(get_post_meta($review, '_custom', true))->toBe(['colour' => 'blue', 'size' => 'large']);
});

test('a custom field value that is not an array is left where it is', function () {
    $review = legacyReview([
        '_author' => 'Jane Doe',
        '_custom' => 'not-an-array',
        '_rating' => '5',
    ]);

    expect(glsr(MigrateReviews::class)->run())->toBeTrue();

    wp_cache_delete($review, 'post_meta');
    expect(get_post_meta($review, '_custom', true))->toBe('not-an-array')
        ->and(get_post_meta($review, '_custom_not-an-array', true))->toBe('');
});

test('an empty custom field array is not even looked at', function () {
    // The query excludes it, so the pass has nothing to prepare and no insert
    // to make.
    $review = legacyReview([
        '_author' => 'Jane Doe',
        '_custom' => [],
        '_rating' => '5',
    ]);

    expect(glsr(MigrateReviews::class)->run())->toBeTrue();

    wp_cache_delete($review, 'post_meta');
    expect(get_post_meta($review, '_custom', true))->toBe([]);
});

test('the reviews are walked in pages, not all at once', function () {
    // The four passes each page through their query; with a limit of one, a
    // three-review site takes four turns of each loop instead of two.
    $reviews = [
        legacyReview(['_author' => 'Jane Doe', '_rating' => '1']),
        legacyReview(['_author' => 'John Doe', '_rating' => '2']),
        legacyReview(['_author' => 'Jo Doe', '_rating' => '3']),
    ];
    $migration = glsr(MigrateReviews::class);
    $migration->limit = 1;

    expect($migration->run())->toBeTrue();

    foreach ($reviews as $index => $review) {
        expect(ratingRow($review))->toMatchArray(['rating' => (string) ($index + 1)]);
    }
});

/**
 * A review as v5.24 left it: a post of the review type, with its values in meta
 * and nothing in the custom tables.
 */
function legacyReview(array $meta, string $status = 'publish'): int
{
    $postId = createPost(['post_status' => $status, 'post_type' => glsr()->post_type]);
    foreach ($meta as $key => $value) {
        add_post_meta($postId, $key, $value);
    }
    return $postId;
}

function ratingRows(int $reviewId): array
{
    return glsr(Database::class)->dbGetResults(
        glsr(Query::class)->sql("SELECT * FROM table|ratings WHERE review_id = %d", $reviewId),
        ARRAY_A
    );
}

function ratingRow(int $reviewId): array
{
    return ratingRows($reviewId)[0] ?? [];
}

function assignedPosts(int $reviewId): array
{
    return glsr(Database::class)->dbGetResults(
        glsr(Query::class)->sql("
            SELECT ap.post_id, ap.is_published
            FROM table|assigned_posts AS ap
            INNER JOIN table|ratings AS r ON (r.ID = ap.rating_id)
            WHERE r.review_id = %d
        ", $reviewId),
        ARRAY_A
    );
}

function assignedTerms(int $reviewId): array
{
    return glsr(Database::class)->dbGetCol(
        glsr(Query::class)->sql("
            SELECT at.term_id
            FROM table|assigned_terms AS at
            INNER JOIN table|ratings AS r ON (r.ID = at.rating_id)
            WHERE r.review_id = %d
        ", $reviewId)
    );
}
