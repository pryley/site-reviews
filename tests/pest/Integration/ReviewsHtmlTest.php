<?php

use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Modules\Html\ReviewHtml;
use GeminiLabs\SiteReviews\Modules\Html\ReviewsHtml;

use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The two ArrayObjects a theme touches from a template: {{ review.rating }} and
 * friends resolve through offsetGet, so what a template can reach — and what it
 * cannot — is these classes' contract.
 */

beforeEach(fn () => resetPluginState());

function reviewsHtml(array $args = []): ReviewsHtml
{
    return glsr(ReviewManager::class)->reviews($args)->build();
}

test('a template reaches the review context, the attributes, and nothing private', function () {
    $review = createReview(['rating' => 4]);
    $html = new ReviewHtml($review);

    expect($html['rating'])->toContain('4') // a context value (the built stars markup)
        ->and($html['attributes'])->toContain('data-shortcode="site_reviews"') // built on demand
        ->and($html['review'])->toBeInstanceOf(GeminiLabs\SiteReviews\Review::class) // a public property
        ->and($html['no_such_key'])->toBe(''); // everything else: the filter's answer, cast to string
});

test('a review whose context was filtered away renders as nothing', function () {
    add_filter('site-reviews/review/build/after', '__return_empty_array');
    $html = new ReviewHtml(createReview());

    expect((string) $html)->toBe('');
});

test('the reviews list exposes its pieces the same way', function () {
    createReview(['rating' => 5]);
    $html = reviewsHtml(['display' => 1, 'pagination' => 'ajax']);

    expect($html['attributes'])->toContain('data-') // built on demand from the shortcode args
        ->and($html[0])->not->toBeEmpty() // the first rendered review
        ->and($html['max_num_pages'])->toBeGreaterThanOrEqual(1) // a public property
        ->and($html['no_such_key'])->toBe(''); // the filter's answer, cast to string
});

test('ajax and load-more pagination each mark the wrapper for their script', function () {
    createReview();
    createReview();

    expect((string) reviewsHtml(['display' => 1, 'pagination' => 'ajax']))
        ->toContain('glsr-ajax-pagination');
    expect((string) reviewsHtml(['display' => 1, 'pagination' => 'loadmore']))
        ->toContain('glsr-ajax-loadmore');
});
