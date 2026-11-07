<?php

use GeminiLabs\SiteReviews\Database\OptionManager;

use function GeminiLabs\SiteReviews\Tests\commitsTransaction;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;
use function GeminiLabs\SiteReviews\Tests\restRequest;
use function GeminiLabs\SiteReviews\Tests\sentMail;
use function GeminiLabs\SiteReviews\Tests\sentTo;

/*
 * The parts of a review and of the collection (RestReviewPartsController), driven through
 * rest_do_request(): what the review list's toggles, the review editor and the Tools page call.
 */

const REVIEWS_NS = '/site-reviews/v1/reviews';

beforeEach(function () {
    resetPluginState();
    $GLOBALS['wp_rest_server'] = new WP_REST_Server();
    do_action('rest_api_init', $GLOBALS['wp_rest_server']);
    wp_set_current_user(createUser(['role' => 'administrator']));
});

afterEach(function () {
    unset($GLOBALS['wp_rest_server']);
});

test('registers the review part routes', function () {
    $routes = $GLOBALS['wp_rest_server']->get_routes();
    foreach (['pinned', 'status', 'verification-requests', 'verified'] as $part) {
        expect($routes)->toHaveKey(REVIEWS_NS.'/(?P<id>\d+)/'.$part);
    }
});

test('a part of a review that does not exist is a 404 with a glsr_ code', function () {
    $response = restRequest('PUT', REVIEWS_NS.'/999999/pinned', ['value' => true]);
    expect($response->get_status())->toBe(404);
    expect($response->get_data()['code'])->toBe('glsr_invalid_review');
});

test('approving a review answers with everything the row needs redrawn, outside wp-admin', function () {
    // The row title, the status links above the table with their new counts, and the pending count
    // for the menu bubble; wp-admin's own code draws them, so the route loads it.
    $review = createReview(['is_approved' => false]);

    $response = restRequest('PUT', REVIEWS_NS."/{$review->ID}/status", ['status' => 'publish']);

    expect($response->get_status())->toBe(200);
    $data = $response->get_data();
    expect($data['class'])->toBe('status-publish')
        ->and($data['link'])->toContain('row-title')
        ->and($data['counts'])->toContain('Approved')
        ->and((int) $data['pending'])->toBe(0);
    expect(get_post_status($review->ID))->toBe('publish');

    expect(restRequest('PUT', REVIEWS_NS."/{$review->ID}/status", ['status' => 'pending'])->get_status())->toBe(200);
    expect(get_post_status($review->ID))->toBe('pending');
    expect(restRequest('PUT', REVIEWS_NS."/{$review->ID}/status", ['status' => 'approve'])->get_status())->toBe(400); // not a status
});

test('a review is not approved by somebody who may not publish it', function () {
    $review = createReview(['is_approved' => false]);
    wp_set_current_user(createUser(['role' => 'subscriber']));

    $response = restRequest('PUT', REVIEWS_NS."/{$review->ID}/status", ['status' => 'publish']);

    expect($response->get_status())->toBe(403);
    expect($response->get_data()['code'])->toBe('glsr_forbidden');
    expect(get_post_status($review->ID))->toBe('pending');
});

test('a review is pinned with an explicit value, and unpinned', function () {
    $review = createReview();

    $pinned = restRequest('PUT', REVIEWS_NS."/{$review->ID}/pinned", ['value' => true]);
    expect($pinned->get_status())->toBe(200);
    expect($pinned->get_data()['value'])->toBe(1);
    expect($pinned->get_data()['notices'])->toContain('Review pinned');
    expect(glsr_get_review($review->ID)->is_pinned)->toBeTrue();

    $unpinned = restRequest('PUT', REVIEWS_NS."/{$review->ID}/pinned", ['value' => false]);
    expect($unpinned->get_data()['value'])->toBe(0);
    expect(glsr_get_review($review->ID)->is_pinned)->toBeFalse();

    expect(restRequest('PUT', REVIEWS_NS."/{$review->ID}/pinned")->get_status())->toBe(400); // the value is required
});

test('a review is verified only while an addon enables verification', function () {
    $review = createReview();

    $refused = restRequest('PUT', REVIEWS_NS."/{$review->ID}/verified", ['value' => true]);
    expect($refused->get_status())->toBe(403);
    expect($refused->get_data()['code'])->toBe('glsr_verification_disabled');
    expect(glsr_get_review($review->ID)->is_verified)->toBeFalse();

    add_filter('site-reviews/verification/enabled', '__return_true');
    $verified = restRequest('PUT', REVIEWS_NS."/{$review->ID}/verified", ['value' => true]);
    expect($verified->get_status())->toBe(200);
    expect($verified->get_data()['value'])->toBe(1);
    expect(glsr_get_review($review->ID)->is_verified)->toBeTrue();
    remove_filter('site-reviews/verification/enabled', '__return_true');
});

test('a verification request sends the email again, and names the button that did', function () {
    $review = createReview(['email' => 'jane@example.org']);
    glsr(OptionManager::class)->set('settings.general.request_verification', 'yes');

    $response = restRequest('POST', REVIEWS_NS."/{$review->ID}/verification-requests");

    expect($response->get_status())->toBe(200);
    expect($response->get_data()['text'])->toBe('Resend Verification Request');
    expect(sentMail())->toHaveCount(1);
    expect(sentTo())->toBe(['jane@example.org']);
});

test('a verification request is refused for somebody who may not edit the review', function () {
    $review = createReview(['email' => 'jane@example.org']);
    glsr(OptionManager::class)->set('settings.general.request_verification', 'yes');
    wp_set_current_user(createUser(['role' => 'subscriber']));

    $response = restRequest('POST', REVIEWS_NS."/{$review->ID}/verification-requests");

    expect($response->get_status())->toBe(403);
    expect($response->get_data()['code'])->toBe('glsr_forbidden');
    expect(sentMail())->toBe([]);
});

/*
 * The parts of the collection the Tools page acts on.
 */

test('registers the collection part routes', function () {
    $routes = $GLOBALS['wp_rest_server']->get_routes();
    foreach (['counts', 'import', 'locations', 'relations'] as $part) {
        expect($routes)->toHaveKey(REVIEWS_NS.'/'.$part);
    }
});

test('every collection part refuses a subscriber with a glsr_ code', function () {
    wp_set_current_user(createUser(['role' => 'subscriber']));
    foreach ([
        ['DELETE', '/locations'],
        ['DELETE', '/relations', ['invalid' => true]],
        ['POST', '/counts'],
        ['POST', '/import', ['stage' => 4]],
        ['POST', '/locations'],
    ] as $call) {
        [$method, $route, $params] = $call + [2 => []];
        $response = restRequest($method, REVIEWS_NS.$route, $params);
        expect($response->get_status())->toBe(403, $route);
        expect($response->get_data()['code'])->toBe('glsr_forbidden', $route);
    }
});

test('the reviews are geolocated', function () {
    $response = restRequest('POST', REVIEWS_NS.'/locations');
    expect($response->get_status())->toBe(200);
    expect($response->get_data()['notices'])->toContain('already been geolocated'); // no review has an IP address here
});

test('the location data is removed', function () {
    commitsTransaction(); // RemoveLocationData empties the stats table with a TRUNCATE, which is DDL
    $response = restRequest('DELETE', REVIEWS_NS.'/locations');
    expect($response->get_status())->toBe(200);
    expect($response->get_data()['notices'])->toContain('Successfully removed the geolocation data');
});

test('the assignment counts are recalculated', function () {
    $response = restRequest('POST', REVIEWS_NS.'/counts');
    expect($response->get_status())->toBe(200);
    expect($response->get_data())->toHaveKey('notices');
});

test('the invalid relations are removed, and the argument has to say so', function () {
    $response = restRequest('DELETE', REVIEWS_NS.'/relations', ['invalid' => true]);
    expect($response->get_status())->toBe(200);
    expect($response->get_data()['notices'])->toContain('repaired');

    expect(restRequest('DELETE', REVIEWS_NS.'/relations')->get_status())->toBe(400);
    expect(restRequest('DELETE', REVIEWS_NS.'/relations', ['invalid' => false])->get_status())->toBe(400);
});

test('an import runs one stage of its source, and knows no other source', function () {
    // The stages themselves are CsvImportTest's; stage 4 is the cleanup, which needs no file.
    $response = restRequest('POST', REVIEWS_NS.'/import', ['stage' => 4]);
    expect($response->get_status())->toBe(200);
    expect($response->get_data())->toHaveKey('notices');

    expect(restRequest('POST', REVIEWS_NS.'/import', ['stage' => 5])->get_status())->toBe(400);
    expect(restRequest('POST', REVIEWS_NS.'/import')->get_status())->toBe(400); // the stage is required
    $unknown = restRequest('POST', REVIEWS_NS.'/import', ['source' => 'tripadvisor', 'stage' => 1]);
    expect($unknown->get_status())->toBe(404);
    expect($unknown->get_data()['code'])->toBe('glsr_invalid_import_source');
});
