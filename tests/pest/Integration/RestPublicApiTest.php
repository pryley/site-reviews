<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Encryption;
use GeminiLabs\SiteReviews\Modules\Honeypot;
use GeminiLabs\SiteReviews\Modules\Validator\TurnstileValidator;
use GeminiLabs\SiteReviews\Tests\SubmitsReviews;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createReviews;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\interceptHttp;
use function GeminiLabs\SiteReviews\Tests\mutexLock;
use function GeminiLabs\SiteReviews\Tests\releaseMutexLock;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;
use function GeminiLabs\SiteReviews\Tests\restRequest;

uses(SubmitsReviews::class);

/*
 * The public REST routes — the frontend's transport for the four visitor actions,
 * beside the admin-ajax routes which still serve the same commands:
 *
 *   POST site-reviews/v1/submissions              submit-review        (RestSubmissionController)
 *   GET  site-reviews/v1/render/reviews           fetch-paged-reviews  (RestRenderController)
 *   GET  site-reviews/v1/render/reviews/<id>      approved-review and, with a
 *                                                 `verified` token, verified-review
 *
 * They serve anonymous visitors on CACHED pages, so no route requires a login, a
 * capability, or a nonce — a cached nonce is a stale nonce, and the REST API refuses a
 * stale X-WP-Nonce with a 403 before the permission callback ever runs. The only gate is
 * the submission mutex, which is the same lock the Router takes (Modules\Mutex), so a
 * parallel submission is refused whichever door it came through.
 *
 * Unlike the admin-ajax envelope, the body is the payload itself and failure travels as
 * the HTTP status: 400 validation, 404 refused review, 429 mutex.
 */

beforeEach(function () {
    resetPluginState();
    $this->setUpSubmitsReviews(); // this calls setUpAjax() too
    wp_set_current_user(0); // every route must work for a logged-out visitor
    // rest_api_init has not fired: this is a front-end request, not /wp-json.
    $GLOBALS['wp_rest_server'] = new WP_REST_Server();
    do_action('rest_api_init', $GLOBALS['wp_rest_server']);
});

afterEach(function () {
    $this->tearDownAjax();
    unset($GLOBALS['wp_rest_server']);
});

/**
 * A submission no real form would be refused: the trait's base request plus the two
 * things a hand-built one forgets — the encrypted form signature and an empty honeypot.
 * See PublicControllerTest for why each exists.
 */
function restSubmission(array $values): array
{
    $values['form_signature'] = glsr(Encryption::class)->encrypt(
        serialize(['form_id' => $values['form_id']])
    );
    $values[glsr(Honeypot::class)->hash($values['form_id'])] = '';
    return $values;
}

test('registers the public routes', function () {
    $routes = $GLOBALS['wp_rest_server']->get_routes();
    expect($routes)->toHaveKey('/site-reviews/v1/submissions')
        ->toHaveKey('/site-reviews/v1/render/reviews')
        ->toHaveKey('/site-reviews/v1/render/reviews/(?P<id>[\d]+)');
});

test('no render route asks for a login, a capability, or a nonce', function () {
    // The contract of the transport. A permission callback that asked for anything would
    // turn every cached anonymous page into a broken one.
    $routes = $GLOBALS['wp_rest_server']->get_routes();
    foreach (['/site-reviews/v1/render/reviews', '/site-reviews/v1/render/reviews/(?P<id>[\d]+)'] as $route) {
        foreach ($routes[$route] as $handler) {
            expect($handler['permission_callback'])->toBe('__return_true');
        }
    }
});

/*
 * The form submission.
 */

test('a visitor can submit a review with no login and no nonce', function () {
    releaseMutexLock();

    $response = restRequest('POST', '/site-reviews/v1/submissions', [
        glsr()->id => restSubmission($this->request([
            'content' => 'Submitted over REST.',
            'email' => 'jane@example.org',
            'name' => 'Jane',
            'rating' => 5,
            'terms' => 1,
            'title' => 'A lovely stay',
        ])),
    ]);

    expect($response->get_status())->toBe(201);
    $data = $response->get_data();
    // This shape IS the contract with the frontend JS (CreateReview::response()); the
    // admin-ajax route sends the same array, so the two doors stay interchangeable.
    expect($data)->toHaveKeys(['errors', 'html', 'message', 'redirect', 'review', 'reviews', 'summary', 'success']);
    expect($data['success'])->toBeTrue()
        ->and($data['message'])->toBe($this->messageSuccess);
});

test('a logged-in submitter keeps their identity', function () {
    // admin-ajax authenticates a logged-in submitter from their cookie alone. Over REST
    // that requires the wp_rest nonce (EnqueuePublicAssets only prints it for logged-in
    // page loads); this asserts the half the suite can reach — a submission made while
    // logged in is attributed, not anonymous.
    releaseMutexLock();
    wp_set_current_user($userId = createUser());

    $response = restRequest('POST', '/site-reviews/v1/submissions', [
        glsr()->id => restSubmission($this->request([
            'content' => 'Submitted while logged in.',
            'email' => 'jane@example.org',
            'name' => 'Jane',
            'rating' => 4,
            'terms' => 1,
            'title' => 'Attributed',
        ])),
    ]);

    expect($response->get_status())->toBe(201);
    $review = glsr_get_review($response->get_data()['review']['ID']);
    expect($review->author_id)->toBe($userId);
});

test('the captcha token rides the submission, exactly as it does over admin-ajax', function () {
    // Request::inputPost() copies the widget's top-level token field into _captcha; the
    // REST controller must do the same, or every captcha-protected submission is refused.
    releaseMutexLock();
    glsr(OptionManager::class)->set('settings.forms.captcha.integration', 'turnstile');
    glsr(OptionManager::class)->set('settings.forms.captcha.usage', 'all');
    glsr(OptionManager::class)->set('settings.forms.turnstile.key', 'a-key');
    glsr(OptionManager::class)->set('settings.forms.turnstile.secret', 'a-secret');
    $requests = interceptHttp(['body' => (string) wp_json_encode(['success' => true])]);

    $response = restRequest('POST', '/site-reviews/v1/submissions', [
        glsr()->id => restSubmission($this->request([
            'content' => 'Submitted past the captcha.',
            'email' => 'jane@example.org',
            'name' => 'Jane',
            'rating' => 5,
            'terms' => 1,
            'title' => 'Challenged',
        ])),
        'cf-turnstile-response' => 'a-token-from-the-browser',
    ]);

    expect($response->get_status())->toBe(201);
    // and the service was asked about the token the browser sent (the interception
    // catches every HTTP request the pipeline makes, so select the verify call)
    $verify = array_values(array_filter(
        iterator_to_array($requests),
        fn ($request) => TurnstileValidator::API_URL === $request['url']
    ));
    expect($verify)->toHaveCount(1);
    $body = (array) $verify[0]['args']['body'];
    expect($body['response'])->toBe('a-token-from-the-browser');
});

test('a submission that fails validation is a 400 and the errors survive', function () {
    releaseMutexLock();

    $response = restRequest('POST', '/site-reviews/v1/submissions', [
        glsr()->id => $this->request([
            'content' => '', // required
            'rating' => 0,   // required
        ]),
    ]);

    expect($response->get_status())->toBe(400);
    $data = $response->get_data();
    expect($data['success'])->toBeFalse()
        ->and($data['errors'])->not->toBeEmpty();
});

test('a second submission arriving in the same moment is refused with a 429', function () {
    releaseMutexLock();

    restRequest('POST', '/site-reviews/v1/submissions', [
        glsr()->id => $this->request([]),
    ]);
    // the lock is the SAME transient the Router takes: one visitor, one lock, two doors
    expect(get_transient(mutexLock()))->not->toBeFalse();

    $second = restRequest('POST', '/site-reviews/v1/submissions', [
        glsr()->id => $this->request([]),
    ]);

    expect($second->get_status())->toBe(429);
    expect($second->get_data()['code'])->toBe('glsr_too_many_requests');
    // and the visitor-facing message stays a visitor's message
    expect($second->get_data()['message'])->toContain('could not be submitted');
});

/*
 * The next page of reviews.
 */

test('the second page of reviews is fetched by a logged-out visitor', function () {
    createReviews(6);

    $response = restRequest('GET', '/site-reviews/v1/render/reviews', [
        'atts' => ['display' => 2],
        'page' => 2,
        'url' => get_permalink(createPost()),
    ]);

    expect($response->get_status())->toBe(200);
    $data = $response->get_data();
    expect($data)->toHaveKeys(['max_num_pages', 'pagination', 'reviews']);
    expect($data['max_num_pages'])->toBeGreaterThan(1);
    expect($data['reviews'])->toContain('glsr-review');
    // unwrapped, because the page already has the wrapper (see PublicControllerTest)
    expect($data['pagination'])->not->toContain('glsr-navigation');
});

test('the browser cannot smuggle an attribute the shortcode does not know', function () {
    // `atts` is whatever the page's data attribute said, and anybody can edit that; the
    // shortcode's restrict() guards this door exactly as it guards the admin-ajax one.
    createReviews(3);

    $response = restRequest('GET', '/site-reviews/v1/render/reviews', [
        'atts' => ['display' => 2, 'a_key_nobody_declared' => '<script>alert(1)</script>'],
        'page' => 1,
        'url' => get_permalink(createPost()),
    ]);

    expect($response->get_status())->toBe(200);
    expect($response->get_data()['reviews'])->toContain('glsr-review'); // rendered normally, junk ignored
});

/*
 * One review, by id.
 */

test('a visitor can fetch an approved review by id', function () {
    $review = createReview(['content' => 'The room was lovely.']);

    $response = restRequest('GET', "/site-reviews/v1/render/reviews/{$review->ID}");

    expect($response->get_status())->toBe(200);
    $data = $response->get_data();
    expect($data['review'])->toContain('The room was lovely.')
        ->and($data)->toHaveKey('attributes')
        ->and($data)->not->toHaveKey('message'); // the message belongs to verification
});

test('a review awaiting moderation is not handed to anybody who asks for it', function () {
    // Same refusal as the admin-ajax route: without it, anybody could read the site's
    // moderation queue by counting upwards.
    $pending = createReview(['content' => 'Held back for a reason.', 'is_approved' => false]);

    $response = restRequest('GET', "/site-reviews/v1/render/reviews/{$pending->ID}");

    expect($response->get_status())->toBe(404);
    expect(wp_json_encode($response->get_data()))->not->toContain('Held back for a reason.');
});

test('an id that is not a review at all is refused', function () {
    $response = restRequest('GET', '/site-reviews/v1/render/reviews/'.createPost());

    expect($response->get_status())->toBe(404);
    expect($response->get_data()['code'])->toBe('glsr_review_not_found');
});

/*
 * The verification redirect.
 */

test('the verification redirect can fetch the review with its token', function () {
    $review = createReview(['content' => 'Verified content.']);
    $token = glsr(Encryption::class)->encrypt($review->ID);

    $response = restRequest('GET', "/site-reviews/v1/render/reviews/{$review->ID}", [
        'verified' => $token,
    ]);

    expect($response->get_status())->toBe(200);
    expect($response->get_data()['message'])->toContain('has been verified');
});

test('a verified review still awaiting approval says so, and is reachable only by its token', function () {
    // The token outranks is_approved: the person who owns the review may see it pending;
    // without the token the same review is a 404 (asserted above).
    $pending = createReview(['content' => 'Verified but pending.', 'is_approved' => false]);
    $token = glsr(Encryption::class)->encrypt($pending->ID);

    $response = restRequest('GET', "/site-reviews/v1/render/reviews/{$pending->ID}", [
        'verified' => $token,
    ]);

    expect($response->get_status())->toBe(200);
    expect($response->get_data()['message'])->toContain('awaiting approval');
});

test('a token for a different review opens nothing', function () {
    $review = createReview();
    $other = createReview();
    $token = glsr(Encryption::class)->encrypt($other->ID);

    $response = restRequest('GET', "/site-reviews/v1/render/reviews/{$review->ID}", [
        'verified' => $token,
    ]);

    expect($response->get_status())->toBe(400);
    expect($response->get_data()['code'])->toBe('glsr_invalid_token');
});

test('a tampered token opens nothing', function () {
    $review = createReview();
    $token = glsr(Encryption::class)->encrypt($review->ID);

    $response = restRequest('GET', "/site-reviews/v1/render/reviews/{$review->ID}", [
        'verified' => 'tampered'.$token,
    ]);

    expect($response->get_status())->toBe(400);
    expect($response->get_data()['code'])->toBe('glsr_invalid_token');
});
