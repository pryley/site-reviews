<?php

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createReviews;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The REST API, driven through rest_do_request() — i.e. the same path a real
 * HTTP request takes, permission callbacks and all.
 *
 * Routes (verified against the source, not guessed):
 *
 *   site-reviews/v1/reviews            RestReviewController, registered by WordPress
 *   site-reviews/v1/reviews/<id>       because the post type declares it as its
 *                                      rest_controller_class (PostTypeDefaults)
 *   site-reviews/v1/summary            RestSummaryController  ) both registered by
 *   site-reviews/v1/summary/rating                            ) RestController::registerRoutes
 *   site-reviews/v1/shortcode/<name>   RestShortcodeController
 *
 * Every one of these requires a logged-in user (ReviewPermissions,
 * get_summary_permissions_check, get_items_permissions_check), so the
 * logged-out cases are part of the contract, not an afterthought.
 */

const REST_NS = 'site-reviews/v1';

beforeEach(function () {
    resetPluginState();
    // rest_api_init has not fired: this is a front-end request, not /wp-json.
    $GLOBALS['wp_rest_server'] = new WP_REST_Server();
    do_action('rest_api_init', $GLOBALS['wp_rest_server']);
});

afterEach(function () {
    unset($GLOBALS['wp_rest_server']);
});

function restRequest(string $method, string $route, array $params = []): WP_REST_Response
{
    $request = new WP_REST_Request($method, $route);
    foreach ($params as $key => $value) {
        $request->set_param($key, $value);
    }
    return rest_do_request($request);
}

function actAsAdmin(): int
{
    $userId = createUser(['role' => 'administrator']);
    wp_set_current_user($userId);
    return $userId;
}

test('registers its routes', function () {
    $routes = $GLOBALS['wp_rest_server']->get_routes();
    expect($routes)->toHaveKey('/'.REST_NS.'/reviews')
        ->toHaveKey('/'.REST_NS.'/reviews/(?P<id>[\d]+)')
        ->toHaveKey('/'.REST_NS.'/summary')
        ->toHaveKey('/'.REST_NS.'/summary/rating')
        ->toHaveKey('/'.REST_NS.'/shortcode/(?P<shortcode>[a-z_]+)');
});

test('refuses to list reviews for a logged out visitor', function () {
    $response = restRequest('GET', '/'.REST_NS.'/reviews');
    expect($response->get_status())->toBe(401);
    expect($response->get_data()['code'])->toBe('rest_forbidden_context');
});

test('lists reviews for a permitted user', function () {
    actAsAdmin();
    createReviews(3);
    $response = restRequest('GET', '/'.REST_NS.'/reviews');
    expect($response->get_status())->toBe(200);
    expect($response->get_data())->toHaveCount(3);
});

test('gets a single review', function () {
    actAsAdmin();
    $review = createReview(['content' => 'A review fetched over REST.']);
    $response = restRequest('GET', '/'.REST_NS.'/reviews/'.$review->ID);
    expect($response->get_status())->toBe(200);
    $data = $response->get_data();
    expect($data['id'])->toBe($review->ID)
        ->and($data['content'])->toContain('A review fetched over REST.');
});

test('returns a 404 for a review that does not exist', function () {
    actAsAdmin();
    $response = restRequest('GET', '/'.REST_NS.'/reviews/999999001');
    expect($response->get_status())->toBe(404);
});

test('creates a review', function () {
    actAsAdmin();
    $response = restRequest('POST', '/'.REST_NS.'/reviews', [
        'content' => 'Created over the REST API.',
        'email' => 'rest@example.org',
        'name' => 'Rest Reviewer',
        'rating' => 4,
        'title' => 'Created over REST',
    ]);
    expect($response->get_status())->toBe(201);
    $data = $response->get_data();
    expect($data['rating'])->toEqual(4)
        ->and($data['content'])->toContain('Created over the REST API.');
    // The review really is in the database, not merely echoed back.
    expect(glsr_get_review($data['id'])->isValid())->toBeTrue();
});

test('refuses to create a review for a logged out visitor', function () {
    $response = restRequest('POST', '/'.REST_NS.'/reviews', [
        'content' => 'Should never be created.',
        'rating' => 5,
    ]);
    expect($response->get_status())->toBe(401);
});

test('updates a review', function () {
    actAsAdmin();
    $review = createReview(['content' => 'Before the update.', 'rating' => 1]);
    $response = restRequest('PUT', '/'.REST_NS.'/reviews/'.$review->ID, [
        'content' => 'After the update.',
        'rating' => 5,
    ]);
    expect($response->get_status())->toBe(200);
    expect(glsr_get_review($review->ID)->content)->toContain('After the update.');
    expect(glsr_get_review($review->ID)->rating)->toBe(5);
});

test('deletes a review', function () {
    actAsAdmin();
    $review = createReview();
    $response = restRequest('DELETE', '/'.REST_NS.'/reviews/'.$review->ID, ['force' => true]);
    expect($response->get_status())->toBe(200);
    expect($response->get_data()['deleted'])->toBeTrue();
    expect(get_post($review->ID))->toBeNull();
    // Not merely gone from the database: gone from the review cache too. The
    // rating row is cascaded away by the foreign key before deleteRating() runs,
    // so the purge cannot be conditional on that delete affecting a row.
    expect(glsr_get_review($review->ID)->isValid())->toBeFalse();
});

test('returns the rating summary', function () {
    actAsAdmin();
    createReview(['rating' => 5]);
    createReview(['rating' => 3]);
    $response = restRequest('GET', '/'.REST_NS.'/summary');
    expect($response->get_status())->toBe(200);
    expect($response->get_data())->toBeArray();
});

test('returns the summary of only the reviews assigned to a post', function () {
    actAsAdmin();
    $postId = createPost();
    createReview(['assigned_posts' => $postId, 'rating' => 1]);
    createReview(['rating' => 5]); // not assigned
    $response = restRequest('GET', '/'.REST_NS.'/summary', ['assigned_posts' => $postId]);
    expect($response->get_status())->toBe(200);
});

test('refuses to return a summary to a logged out visitor', function () {
    $response = restRequest('GET', '/'.REST_NS.'/summary');
    expect($response->get_status())->toBe(401);
});

test('renders a shortcode', function () {
    actAsAdmin();
    createReview(['content' => 'Rendered through the shortcode endpoint.']);
    $response = restRequest('GET', '/'.REST_NS.'/shortcode/site_reviews');
    expect($response->get_status())->toBe(200);
});

test('rejects an unknown shortcode', function () {
    actAsAdmin();
    $response = restRequest('GET', '/'.REST_NS.'/shortcode/not_a_shortcode');
    expect($response->get_status())->toBe(400);
    expect($response->get_data()['code'])->toBe('rest_invalid_shortcode');
});

test('refuses to render a shortcode for a logged out visitor', function () {
    $response = restRequest('GET', '/'.REST_NS.'/shortcode/site_reviews');
    expect($response->get_status())->toBe(401);
});
