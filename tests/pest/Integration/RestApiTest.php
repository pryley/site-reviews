<?php

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createReviews;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The REST API, driven through rest_do_request() — the same path a real HTTP request takes,
 * permission callbacks and all.
 *
 * Routes:
 *
 *   site-reviews/v1/reviews            RestReviewController, registered by WordPress
 *   site-reviews/v1/reviews/<id>       because the post type declares it as its rest_controller_class
 *   site-reviews/v1/summary            RestSummaryController  ) both registered by
 *   site-reviews/v1/summary/rating                            ) RestController::registerRoutes
 *   site-reviews/v1/shortcode/<name>   RestShortcodeController
 *
 * Every one requires a logged-in user (ReviewPermissions, get_summary_permissions_check,
 * get_items_permissions_check), so the logged-out cases are part of the contract.
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
    expect($response->get_data()['average'])->toBe(4) // Arguments::toArray JSON round-trips, so a whole average arrives as an int
        ->and($response->get_data()['reviews'])->toBe(2);
});

test('returns the summary of only the reviews assigned to a post', function () {
    actAsAdmin();
    $postId = createPost();
    createReview(['assigned_posts' => $postId, 'rating' => 1]);
    createReview(['rating' => 5]); // not assigned
    $response = restRequest('GET', '/'.REST_NS.'/summary', ['assigned_posts' => $postId]);
    expect($response->get_status())->toBe(200);
    // A controller that ignored assigned_posts would return the site-wide summary: 2 reviews, average 3.
    expect($response->get_data()['average'])->toBe(1)
        ->and($response->get_data()['reviews'])->toBe(1);
});

test('refuses to return a summary to a logged out visitor', function () {
    $response = restRequest('GET', '/'.REST_NS.'/summary');
    expect($response->get_status())->toBe(401);
});

test('fetches a shortcode option list', function () {
    // The endpoint feeds the editor dialogs' dropdowns: {id, title} rows for the option named
    // in the request. assigned_posts always leads with its two placeholder rows, then the
    // matching pages.
    actAsAdmin();
    $postId = createPost(['post_title' => 'Fetched through the shortcode endpoint']);
    $response = restRequest('GET', '/'.REST_NS.'/shortcode/site_reviews', [
        'option' => 'assigned_posts',
        'search' => 'Fetched through',
    ]);
    expect($response->get_status())->toBe(200);
    expect($response->get_data())->toBe([
        ['id' => 'post_id', 'title' => 'The Current Page'],
        ['id' => 'parent_id', 'title' => 'The Parent Page'],
        ['id' => $postId, 'title' => 'Fetched through the shortcode endpoint'],
    ]);
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

/*
 * =============================================================================
 * WHO MAY DO WHAT
 * =============================================================================
 *
 * The permission callbacks (ReviewPermissions) are the only thing between the REST API and
 * anybody on the internet who can find /wp-json. The happy paths above prove the routes work;
 * these prove they say NO — which is the half that matters, and the half that is silent when
 * it breaks. A permission callback that wrongly returns true does not throw, does not log, and
 * does not fail any test that only checks the happy path.
 *
 * Two things are worth naming because they are easy to get backwards:
 *
 *   - a review is PUBLIC content on the front end, and the REST API still refuses to list it
 *     to a logged-out visitor. That is deliberate: the API returns the email address, the IP
 *     address and the moderation state, none of which the front end ever renders.
 *   - `context=edit` is not decoration. It is what decides whether the response carries the
 *     personal data at all, so it has its own capability check on every read route.
 */

function actAs(string $role): int
{
    $userId = createUser(['role' => $role]);
    wp_set_current_user($userId);

    return $userId;
}

test('a subscriber cannot read the personal data on a review', function () {
    // A subscriber is a logged-in user — the login check alone is not enough. `context=edit` is
    // what unwraps the email, the IP address and the moderation state, and it takes edit_posts.
    $review = createReview(['email' => 'jane@example.org', 'ip_address' => '203.0.113.9']);
    actAs('subscriber');

    $response = restRequest('GET', '/'.REST_NS.'/reviews/'.$review->ID, ['context' => 'edit']);

    expect($response->get_status())->toBe(403)
        ->and($response->get_data()['code'])->toBe('rest_forbidden_context');
    expect((string) wp_json_encode($response->get_data()))->not->toContain('jane@example.org');
});

test('a subscriber cannot list the reviews in the edit context either', function () {
    createReview();
    actAs('subscriber');

    expect(restRequest('GET', '/'.REST_NS.'/reviews', ['context' => 'edit'])->get_status())->toBe(403);
});

test('an unapproved review is not readable by somebody who cannot moderate', function () {
    // The one that would be a leak. An unapproved review is one a site owner has NOT agreed to
    // publish — a spam accusation, a competitor's libel, somebody's phone number. It is not
    // front-end content and it must not be readable by any logged-in account.
    $review = createReview(['is_approved' => false, 'content' => 'Not published yet.']);
    actAs('subscriber');

    $response = restRequest('GET', '/'.REST_NS.'/reviews/'.$review->ID);

    expect($response->get_status())->toBe(403)
        ->and($response->get_data()['code'])->toBe('rest_cannot_view');
    expect((string) wp_json_encode($response->get_data()))->not->toContain('Not published yet.');
});

test('a subscriber cannot create a review', function () {
    actAs('subscriber');

    $response = restRequest('POST', '/'.REST_NS.'/reviews', [
        'content' => 'Let me in',
        'rating' => 5,
        'title' => 'A review',
    ]);

    expect($response->get_status())->toBe(403)
        ->and($response->get_data()['code'])->toBe('rest_cannot_create');
});

test('a review cannot be created under somebody else\'s name', function () {
    // `author` is who the review is attributed to. Without this check, any account able to
    // create a review could publish one as the site owner.
    $victim = createUser(['role' => 'administrator']);
    actAs('author'); // may create posts, may NOT edit others'

    $response = restRequest('POST', '/'.REST_NS.'/reviews', [
        'author' => $victim,
        'content' => 'Words put in their mouth',
        'rating' => 5,
        'title' => 'Not mine',
    ]);

    expect($response->get_status())->toBe(403)
        ->and($response->get_data()['code'])->toBe('rest_cannot_edit_others');
});

test('a review can be created under your own name', function () {
    // The other side of the same check — it must not lock somebody out of authoring their own.
    $userId = actAs('administrator');

    $response = restRequest('POST', '/'.REST_NS.'/reviews', [
        'author' => $userId,
        'content' => 'My own review',
        'rating' => 5,
        'title' => 'Mine',
    ]);

    expect($response->get_status())->toBe(201);
});

test('a review that already exists cannot be created again', function () {
    $review = createReview();
    actAsAdmin();

    $response = restRequest('POST', '/'.REST_NS.'/reviews', [
        'content' => 'Again',
        'id' => $review->ID,
        'rating' => 5,
        'title' => 'Again',
    ]);

    expect($response->get_status())->toBe(400)
        ->and($response->get_data()['code'])->toBe('rest_review_exists');
});

test('a subscriber cannot edit or delete a review', function () {
    $review = createReview();
    actAs('subscriber');

    $update = restRequest('PUT', '/'.REST_NS.'/reviews/'.$review->ID, ['title' => 'Rewritten']);
    expect($update->get_status())->toBe(403)
        ->and($update->get_data()['code'])->toBe('rest_cannot_edit');

    $delete = restRequest('DELETE', '/'.REST_NS.'/reviews/'.$review->ID, ['force' => true]);
    expect($delete->get_status())->toBe(403)
        ->and($delete->get_data()['code'])->toBe('rest_cannot_delete');

    // and it is still there, and still says what it said
    expect(get_post($review->ID))->not->toBeNull();
});

test('editing or deleting a review that does not exist is a 404, not a 403', function () {
    // The distinction matters: a 403 on a non-existent id tells an attacker the id space is
    // guarded; a 404 tells them nothing. Both routes check validity BEFORE capability.
    actAsAdmin();

    expect(restRequest('PUT', '/'.REST_NS.'/reviews/999999', ['title' => 'x'])->get_status())->toBe(404);
    expect(restRequest('DELETE', '/'.REST_NS.'/reviews/999999', ['force' => true])->get_status())->toBe(404);
});
