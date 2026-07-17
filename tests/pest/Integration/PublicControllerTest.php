<?php

use GeminiLabs\SiteReviews\Controllers\PublicController;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Encryption;
use GeminiLabs\SiteReviews\Modules\Honeypot;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;
use GeminiLabs\SiteReviews\Tests\InteractsWithAjax;
use GeminiLabs\SiteReviews\Tests\InteractsWithExits;
use GeminiLabs\SiteReviews\Tests\SubmitsReviews;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createReviews;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

uses(InteractsWithAjax::class, InteractsWithExits::class, SubmitsReviews::class);

/*
 * The front end: the four routes a VISITOR can reach, and the schema in the footer.
 *
 * Everything runs for logged-out people on someone else's site, and three of the four are unguarded
 * — no nonce, since a page cache would serve a stale one. So each route decides what it will hand
 * out, and the interesting assertions are about what they REFUSE:
 *
 *   approved-review      hands back one review's HTML by id to anybody. It must refuse a PENDING
 *                        review, or a moderation queue is readable by anyone who can count.
 *   fetch-paged-reviews  page 2 of a list, rebuilt from attributes the BROWSER sent — the only
 *                        reason the shortcode's restrict() matters.
 *   submit-review        the form post. Redirects on success; on failure does NOT, because the
 *                        errors must survive to be printed.
 *
 * The schema is the JSON-LD Google reads, printed in the footer unless an SEO plugin owns it —
 * printing two is worse than none.
 */

beforeEach(function () {
    resetPluginState();
    $this->setUpSubmitsReviews(); // this calls setUpAjax() too
});

/**
 * The paged-reviews request the browser actually sends.
 *
 * `url` is not optional, and it is not defaulted: NormalizePaginationArgs does
 * `Url::path($args->url)` on whatever is in the store, and Arguments returns NULL for a key that
 * is not there — which is a TypeError, on a PUBLIC unguarded route, from browser-controlled input.
 * The javascript always sends it, so nobody has hit this; a crafted request would.
 */
function pagedRequest(array $atts, int $page = 1): Request
{
    return new Request([
        'atts' => $atts,
        'page' => $page,
        'url' => get_permalink(createPost()),
    ]);
}

afterEach(fn () => $this->tearDownAjax());

/*
 * One review, by id.
 */

test('a visitor can fetch an approved review', function () {
    // This is what the "your review has been posted" javascript calls to slot the review into the
    // page without a reload.
    $review = createReview(['content' => 'The room was lovely.']);

    $response = $this->jsonSentBy(fn () => glsr(PublicController::class)->approvedReviewAjax(
        new Request(['review_id' => $review->ID])
    ));

    expect($response['success'])->toBeTrue()
        ->and($response['data']['review'])->toContain('The room was lovely.')
        ->and($response['data'])->toHaveKey('attributes');
});

test('a review awaiting moderation is not handed to anybody who asks for it', function () {
    // THE assertion of this file. The route takes an id and no nonce. Without the is_approved
    // check, anybody could read a site's entire moderation queue by counting upwards — including
    // the reviews the owner is holding back precisely because they do not want them seen.
    $pending = createReview(['content' => 'Held back for a reason.', 'is_approved' => false]);

    $response = $this->jsonSentBy(fn () => glsr(PublicController::class)->approvedReviewAjax(
        new Request(['review_id' => $pending->ID])
    ));

    expect($response['success'])->toBeFalse();
    expect(wp_json_encode($response))->not->toContain('Held back for a reason.');
});

test('an id that is not a review at all is refused', function () {
    // A page, a post, an attachment, or nothing — the id comes from the browser.
    $response = $this->jsonSentBy(fn () => glsr(PublicController::class)->approvedReviewAjax(
        new Request(['review_id' => createPost()])
    ));

    expect($response['success'])->toBeFalse();
});

/*
 * The next page of reviews.
 */

test('the second page of reviews is fetched without reloading the page', function () {
    createReviews(6);

    $response = $this->jsonSentBy(fn () => glsr(PublicController::class)->fetchPagedReviewsAjax(
        pagedRequest(['display' => 2], 2)
    ));

    expect($response['success'])->toBeTrue()
        ->and($response['data'])->toHaveKeys(['max_num_pages', 'pagination', 'reviews'])
        ->and($response['data']['max_num_pages'])->toBeGreaterThan(1)
        ->and($response['data']['reviews'])->not->toBeEmpty();
});

test('the pagination it returns is unwrapped, because the page already has the wrapper', function () {
    // getPagination(false). The javascript replaces the INSIDE of the existing pagination element;
    // sending the wrapper back would nest a second one inside it on every page change.
    createReviews(6);

    $response = $this->jsonSentBy(fn () => glsr(PublicController::class)->fetchPagedReviewsAjax(
        pagedRequest(['display' => 2], 2)
    ));

    expect($response['data']['pagination'])->not->toContain('glsr-navigation');
});

test('the browser cannot smuggle an attribute the shortcode does not know', function () {
    // `atts` comes from the BROWSER — it is whatever the page's data attribute said, and anybody
    // can edit that. It is fed straight into the shortcode, so the shortcode's own restrict() is
    // the only thing between a visitor and the query. What is NOT asserted here is script
    // injection into the response: the unwrapped ajax reply embeds no atts at all, so an
    // alert(1) assertion was guarding a sink the value cannot reach even with restrict()
    // removed (shortcode_atts() drops unknown keys independently). The observable is that the
    // junk is ignored and the request renders normally.
    createReviews(3);

    $response = $this->jsonSentBy(fn () => glsr(PublicController::class)->fetchPagedReviewsAjax(
        pagedRequest(['display' => 2, 'a_key_nobody_declared' => '<script>alert(1)</script>'])
    ));

    expect($response['success'])->toBeTrue();
    expect($response['data']['reviews'])->toContain('glsr-review'); // rendered normally, junk ignored
});

/*
 * Submitting the form without javascript.
 */

test('a review submitted by a plain form post redirects back to the page it came from', function () {
    // The no-javascript path. A redirect is what stops a refresh from posting the review twice.
    // A submission that is refused proves nothing about the redirect, so this one is REAL — which
    // means satisfying the two things a hand-built request forgets:
    //
    //   terms            the default settings require them, and the trait's template leaves the
    //                    field blank on purpose so that every test says whether it accepted.
    //   form_signature   the form ships an encrypted, serialized copy of the fields it was
    //                    rendered with, and SignatureValidator refuses any submission whose posted
    //                    values disagree with it. That is what stops a bot from posting a form it
    //                    never loaded. It is built here the way the form builds it, rather than
    //                    filtered out of the validator list, because a redirect test that skipped
    //                    the signature check would be testing a submission no real form can make.
    $values = $this->request([
        'content' => 'Submitted without javascript.',
        'email' => 'jane@example.org',
        'name' => 'Jane',
        'rating' => 5,
        'terms' => 1,
        'title' => 'A lovely stay',
    ]);
    //   honeypot         a hidden text field whose NAME is a hash of the form_id, which must be
    //                    present and empty. A bot that fills every input it finds fills this one
    //                    too; a bot that posts a payload it made up does not send it at all — and
    //                    `isset() && empty()` catches both.
    $values['form_signature'] = glsr(Encryption::class)->encrypt(
        serialize(['form_id' => $values['form_id']])
    );
    $values[glsr(Honeypot::class)->hash($values['form_id'])] = '';
    $request = new Request($values);

    $location = $this->expectsRedirectAndExit(
        fn () => glsr(PublicController::class)->submitReview($request)
    );

    expect($location)->not->toBeEmpty();
});

test('a submission that fails does NOT redirect, because the errors have to survive', function () {
    // A redirect would throw the validation errors away and hand the person back an empty form
    // with no explanation — and they would have to type it all again to find out what was wrong.
    $this->interceptExits();

    glsr(PublicController::class)->submitReview(new Request($this->request([
        'content' => '', // required
        'rating' => 0,   // required
    ])));

    expect(true)->toBeTrue(); // it RETURNED — no WpRedirectException was thrown
    expect(glsr()->sessionGet('form_errors'))->not->toBeEmpty();
});

/*
 * The schema in the footer.
 */

test('the schema is printed for google', function () {
    // The footer does not GENERATE the schema, it prints what the page collected: a reviews
    // shortcode with `schema` on stores it, and this is what puts it on the page. A footer hook
    // that printed on a page with no reviews block would describe a page that does not exist.
    createReview(['rating' => 5]);
    glsr(OptionManager::class)->set('settings.schema.integration.plugin', '');
    glsr(SiteReviewsShortcode::class)->build(['schema' => true]);

    ob_start();
    glsr(PublicController::class)->renderSchema();

    expect((string) ob_get_clean())->toContain('application/ld+json');
});

test('and is not printed when an SEO plugin has been told to own it', function () {
    // Two JSON-LD blocks describing the same page is worse than none: Google picks one, and which
    // one it picks is not up to the site owner.
    createReview(['rating' => 5]);
    glsr(SiteReviewsShortcode::class)->build(['schema' => true]);
    glsr(OptionManager::class)->set('settings.schema.integration.plugin', 'yoast');

    ob_start();
    glsr(PublicController::class)->renderSchema();

    expect((string) ob_get_clean())->toBe('');
});

/*
 * The style layer.
 */

test('a view is routed through the style the site chose', function () {
    // Every template the plugin renders passes through here, so that a site using Bootstrap gets
    // Bootstrap's markup rather than the plugin's own.
    expect(glsr(PublicController::class)->filterRenderView('templates/review'))
        ->toBeString()
        ->not->toBeEmpty();
});
