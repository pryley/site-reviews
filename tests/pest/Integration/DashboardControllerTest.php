<?php

use GeminiLabs\SiteReviews\Controllers\DashboardController;
use GeminiLabs\SiteReviews\Database\Cache;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The dashboard widget's cache: the monthly review counts are cached, and the cache is
 * flushed when a review changes status — and only then.
 */

beforeEach(fn () => resetPluginState());

test('the monthly count cache is flushed only for a review status change', function () {
    $controller = glsr(DashboardController::class);
    $review = createReview();
    $primeCache = function () {
        glsr(Cache::class)->store('monthly', 'count', ['primed']);
        return fn () => glsr(Cache::class)->get('monthly', 'count');
    };

    // a plugin passing a null post — "some plugins are bad actors" — is ignored
    $cached = $primeCache();
    $controller->flushMonthlyCountCache('publish', 'pending', null);
    expect($cached())->toBe(['primed']);

    // somebody else's post type is ignored
    $controller->flushMonthlyCountCache('publish', 'pending', get_post(createPost()));
    expect($cached())->toBe(['primed']);

    // a status "change" to the same status is ignored
    $controller->flushMonthlyCountCache('publish', 'publish', get_post($review->ID));
    expect($cached())->toBe(['primed']);

    // a real review status change flushes
    $controller->flushMonthlyCountCache('pending', 'publish', get_post($review->ID));
    expect($cached())->not->toBe(['primed']);
});
