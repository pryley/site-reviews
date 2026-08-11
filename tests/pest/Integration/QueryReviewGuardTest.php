<?php

use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Review;

use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

uses()->group('plugin');

beforeEach(fn () => resetPluginState());

/*
 * Query::review() skips the fetch for an id no post can have. An id of 0 or less
 * cannot match a row (AUTO_INCREMENT starts at 1, and wp_insert_post() refuses an
 * explicit 0 through import_id), so the query is guaranteed empty — yet the importer
 * once paid it on every dedupe miss, and MultilingualPress pays it whenever a remote
 * post does not exist yet. The get/review action must keep firing either way:
 * premium Authors listens to it with no isValid() gate.
 */

test('an id of zero or less answers with an invalid review and no query', function () {
    $queries = [];
    $filter = function (string $sql) use (&$queries): string {
        if (str_contains($sql, 'glsr_ratings')) {
            $queries[] = $sql; // the review query joins the ratings table; nothing else here does
        }
        return $sql;
    };
    add_filter('query', $filter);
    $zero = glsr(ReviewManager::class)->get(0);
    $negative = glsr(ReviewManager::class)->get(-1);
    remove_filter('query', $filter);

    expect($zero)->toBeInstanceOf(Review::class)
        ->and($zero->isValid())->toBeFalse()
        ->and($negative->isValid())->toBeFalse()
        ->and($queries)->toBeEmpty();
});

test('the get/review action fires for an id that skips the query', function () {
    $fired = [];
    $callback = function ($review, $reviewId) use (&$fired): void {
        $fired[] = [$review, $reviewId];
    };
    add_action('site-reviews/get/review', $callback, 10, 2);
    glsr(ReviewManager::class)->get(0);
    remove_action('site-reviews/get/review', $callback);

    expect($fired)->toHaveCount(1)
        ->and($fired[0][0])->toBeInstanceOf(Review::class)
        ->and($fired[0][0]->isValid())->toBeFalse()
        ->and($fired[0][1])->toBe(0);
});

test('an existing review still hydrates, uncached and cached', function () {
    $review = createReview();

    $uncached = glsr(ReviewManager::class)->get($review->ID, true);
    $cached = glsr(ReviewManager::class)->get($review->ID);

    expect($uncached->isValid())->toBeTrue()
        ->and($uncached->ID)->toBe($review->ID)
        ->and($cached->isValid())->toBeTrue()
        ->and($cached->ID)->toBe($review->ID);
});
