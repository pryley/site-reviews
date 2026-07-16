<?php

use GeminiLabs\SiteReviews\Commands\GeolocateReview;
use GeminiLabs\SiteReviews\Commands\RemoveLocationData;
use GeminiLabs\SiteReviews\Commands\VerifyReview;
use GeminiLabs\SiteReviews\Database\PostMeta;
use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Request;

use function GeminiLabs\SiteReviews\Tests\commitsTransaction;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\interceptHttp;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Three commands, each touching something a person would rather you were careful with: where they
 * were when they wrote a review, whether they can be proven to be who they said, and whether it can
 * be taken away again.
 *
 *   GeolocateReview     one review's IP, sent to ip-api.com. The batch version has its own test;
 *                       this is the one that runs on submission.
 *   VerifyReview        marks a review verified — which on a verification-required site PUBLISHES it.
 *                       The most consequential single flag in the plugin, reached from an email link.
 *   RemoveLocationData  the undo: a site owner who turned geolocation on and thought better of it
 *                       needs everything gone, from both places it is kept.
 */

beforeEach(function () {
    resetPluginState();
});

function geolocateReview(int $reviewId): GeolocateReview
{
    return new GeolocateReview(new Request(['review_id' => $reviewId]));
}

function statsFor(int $ratingId): int
{
    global $wpdb;
    $table = glsr(Tables::class)->table('stats');

    return (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$table} WHERE rating_id = %d", $ratingId));
}

/**
 * The location stored beside a review, or [] if there is none.
 *
 * NOT `(array) $value`. get_metadata() returns an empty STRING when the meta row is not there,
 * and (array) '' is [''] — an array with one empty string in it, which is not empty. A helper
 * that got this wrong would report a location that had been deleted as still present, and the
 * test asserting the deletion would fail for a reason that has nothing to do with the code.
 */
function locationOf(int $reviewId): array
{
    wp_cache_delete($reviewId, 'post_meta'); // written with a raw INSERT, so the meta cache is stale
    $value = glsr(PostMeta::class)->get($reviewId, 'geolocation');

    return is_array($value) ? $value : [];
}

function geolocationReplyFor(string $ip, array $overrides = []): array
{
    return ['body' => (string) wp_json_encode(array_replace([
        'city' => 'Vancouver',
        'countryCode' => 'CA',
        'query' => $ip,
        'region' => 'BC',
        'status' => 'success',
    ], $overrides))];
}

/*
 * Geolocating one review.
 */

test('a reviewer\'s location is looked up and stored beside their review', function () {
    $review = createReview(['ip_address' => '203.0.113.9']);
    $requests = interceptHttp(geolocationReplyFor('203.0.113.9'));

    (new GeolocateReview(new Request(['review_id' => $review->ID])))->handle();

    expect($requests)->toHaveCount(1)
        ->and($requests[0]['url'])->toContain('ip-api.com')
        ->and($requests[0]['url'])->toContain('203.0.113.9');
    expect(locationOf($review->ID)['country'])->toBe('CA');
});

test('a local address is never sent anywhere', function () {
    // The admin testing their own form, or every review on a site behind a bad proxy. Sending it
    // is a pointless disclosure that buys nothing back.
    $review = createReview(['ip_address' => '127.0.0.1']);
    $requests = interceptHttp(geolocationReplyFor('127.0.0.1'));

    geolocateReview($review->ID)->handle();

    expect($requests)->toHaveCount(0)
        ->and(statsFor($review->rating_id))->toBe(0);
});

test('a review that does not exist is not looked up', function () {
    $requests = interceptHttp(geolocationReplyFor('203.0.113.9'));

    geolocateReview(999999)->handle();

    expect($requests)->toHaveCount(0);
});

test('an address the service cannot place is not stored as a place', function () {
    $review = createReview(['ip_address' => '203.0.113.9']);
    interceptHttp(geolocationReplyFor('203.0.113.9', ['status' => 'fail', 'message' => 'reserved range']));

    geolocateReview($review->ID)->handle();

    expect(statsFor($review->rating_id))->toBe(0);
});

test('looking a review up again replaces its location rather than duplicating it', function () {
    // The stats table has one row per review. A second lookup — a re-run of the tool, a retried
    // queue job — must not leave two.
    $review = createReview(['ip_address' => '203.0.113.9']);
    interceptHttp(geolocationReplyFor('203.0.113.9'));

    geolocateReview($review->ID)->handle();
    geolocateReview($review->ID)->handle();

    expect(statsFor($review->rating_id))->toBe(1);
});

/*
 * Verifying a review. On a site that requires verification, this is what publishes it.
 */

test('verifying a review records that it was verified, and when', function () {
    $review = createReview();

    (new VerifyReview(glsr_get_review($review->ID)))->handle();

    $verified = glsr_get_review($review->ID);
    expect($verified->is_verified)->toBeTrue();
    expect(glsr(PostMeta::class)->get($review->ID, 'verified_on', 'int'))->toBeGreaterThan(0);
});

test('a review that is already verified is not verified twice', function () {
    // `verified_on` is when the PERSON clicked the link in their email. Re-running the command —
    // a double-clicked link, a retried queue job — must not move that timestamp, or the record
    // of when they confirmed becomes the record of when somebody last ran a command.
    $review = createReview();
    (new VerifyReview(glsr_get_review($review->ID)))->handle();
    $verifiedOn = glsr(PostMeta::class)->get($review->ID, 'verified_on', 'int');

    $command = new VerifyReview(glsr_get_review($review->ID));
    $command->handle();

    expect($command->successful())->toBeFalse() // it says so, rather than pretending
        ->and(glsr(PostMeta::class)->get($review->ID, 'verified_on', 'int'))->toBe($verifiedOn);
});

test('verifying fires the action a site can hang its own behaviour on', function () {
    // …and it fires BEFORE verified_on is written, which the source says is deliberate: a
    // listener asking "has this been verified before?" must be able to get the honest answer.
    $review = createReview();
    $fired = new ArrayObject();
    add_action('site-reviews/review/verified', function ($review) use ($fired) {
        $fired->append(glsr(PostMeta::class)->get($review->ID, 'verified_on', 'int'));
    });

    (new VerifyReview(glsr_get_review($review->ID)))->handle();

    expect($fired)->toHaveCount(1)
        ->and($fired[0])->toBe(0); // not yet written, exactly as the comment in the source says
});

/*
 * Taking it all back.
 */

test('removing the location data takes it from both places it is kept', function () {
    // It lives in two: a row in the stats table, and a copy in post meta. A site owner who turns
    // geolocation off and is told it is "removed" has been told a lie if either survives.
    //
    // TableStats::empty() runs TRUNCATE TABLE, and TRUNCATE is DDL — MySQL commits the open
    // transaction the moment it sees it. Correct on a live site (this is a Tools-page action);
    // it just means the test cannot be isolated by a rollback and cleans up after itself.
    commitsTransaction();
    $review = createReview(['ip_address' => '203.0.113.9']);
    interceptHttp(geolocationReplyFor('203.0.113.9'));
    geolocateReview($review->ID)->handle();

    expect(statsFor($review->rating_id))->toBe(1)
        ->and(locationOf($review->ID))->not->toBeEmpty();

    $command = new RemoveLocationData();
    $command->handle();

    expect(statsFor($review->rating_id))->toBe(0)
        ->and(locationOf($review->ID))->toBeEmpty();
    expect(glsr(Notice::class)->get())->toContain('Successfully removed the geolocation data');
    expect($command->response()['notices'])->toContain('Successfully removed');
});

test('removing the location data leaves everything that is not a location alone', function () {
    // It is a DELETE with a join and a meta_key. A missing WHERE would take every meta row on
    // every review with it.
    commitsTransaction(); // TableStats::empty() TRUNCATEs — see above
    $review = createReview(['ip_address' => '203.0.113.9']);
    glsr(PostMeta::class)->set($review->ID, 'response', 'Thank you for the review!');
    interceptHttp(geolocationReplyFor('203.0.113.9'));
    geolocateReview($review->ID)->handle();

    (new RemoveLocationData())->handle();

    expect(glsr(PostMeta::class)->get($review->ID, 'response'))->toBe('Thank you for the review!');
    expect(glsr_get_review($review->ID)->isValid())->toBeTrue(); // and the review is still there
});
