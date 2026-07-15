<?php

use GeminiLabs\SiteReviews\Commands\GeolocateReview;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Response;

use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\interceptHttp;
use function GeminiLabs\SiteReviews\Tests\protectedMethod;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Geolocating a SINGLE review — the per-review lookup that runs from the queued action, as opposed
 * to the bulk GeolocateReviews tool. The happy path (a successful lookup stored beside the review)
 * is covered by the batch command's tests; what is pinned here is the failure path.
 *
 * A lookup that does not come back is not dropped and not looped on for ever: it is retried, on a
 * transient counter, and given up on after MAX_RETRIES consecutive failures. Get that wrong and a
 * dead licence server (or an IP the far end simply cannot place) either reschedules the same lookup
 * endlessly or abandons it on the first hiccup.
 */

beforeEach(fn () => resetPluginState());

function geolocateOneReview(int $reviewId): GeolocateReview
{
    return new GeolocateReview(new Request(['review_id' => $reviewId]));
}

function geolocateOneRetryKey(int $reviewId): string
{
    return glsr()->prefix."geolocation_retry_{$reviewId}";
}

test('a lookup that fails is retried, and the failure is counted', function () {
    // handle() sees the lookup fail and reschedules; the transient counter is what stops that
    // rescheduling from running for ever.
    $review = createReview(['ip_address' => '203.0.113.9']); // a real, non-local address
    interceptHttp(['response' => ['code' => 500, 'message' => 'Server Error']]); // the far end says no

    geolocateOneReview($review->ID)->handle();

    expect((int) get_transient(geolocateOneRetryKey($review->ID)))->toBe(1); // one failed attempt recorded
});

test('a lookup is abandoned once the retry limit is reached', function () {
    // At the limit the counter is cleared and the review is given up on, rather than the count
    // climbing past the limit and the lookup being rescheduled anyway.
    $review = createReview(['ip_address' => '203.0.113.9']);
    set_transient(geolocateOneRetryKey($review->ID), GeolocateReview::MAX_RETRIES); // already at the limit
    $failed = new Response(new \WP_Error('http_request_failed', 'cURL error 28'));

    protectedMethod(GeolocateReview::class, 'retryLookup')
        ->invoke(geolocateOneReview($review->ID), $failed);

    expect(get_transient(geolocateOneRetryKey($review->ID)))->toBeFalse(); // cleared, not incremented
});

test('the response carries the location and any notices', function () {
    $command = geolocateOneReview(createReview()->ID);

    $response = $command->response();

    expect($response)->toHaveKey('location')
        ->and($response)->toHaveKey('notices')
        ->and($response['location'])->toBe([]); // nothing geolocated on a fresh command
});
