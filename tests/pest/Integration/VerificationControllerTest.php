<?php

use GeminiLabs\SiteReviews\Compat\Controllers\AjaxController;
use GeminiLabs\SiteReviews\Controllers\VerificationController;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\PostMeta;
use GeminiLabs\SiteReviews\Modules\Encryption;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Tests\InteractsWithAjax;
use GeminiLabs\SiteReviews\Tests\InteractsWithExits;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;
use function GeminiLabs\SiteReviews\Tests\sentMail;
use function GeminiLabs\SiteReviews\Tests\sentTo;

uses(InteractsWithAjax::class, InteractsWithExits::class);

/*
 * Proving that the person who wrote a review owns the email address they gave.
 *
 * A review arrives with an unchecked email. The plugin emails a link to it, and clicking the link
 * marks the review verified — so the ONLY thing between "verified" and anyone who can guess a review
 * id is the token in that link.
 *
 * The token is the review's id, encrypted (Modules\Encryption, sodium secretbox, keyed off
 * wp_salt('nonce')). Two places check it:
 *
 *   verifyReview()        the GET the mail client opens. The token IS the route — the router has
 *                         already decrypted and validated it, which is why this takes a plain review
 *                         id and checks nothing. It verifies, then redirects back to the review's page.
 *   verifiedReviewAjax()  the admin-ajax route used before 9.0.0 (Compat\Controllers\AjaxController).
 *                         Public and unguarded, so the `verified` token must decrypt to the review
 *                         id asked for.
 *
 * The admin's side, a button in the editor that sends (or re-sends) the email and a toggle that
 * marks a review verified by hand, is REST routes (RestReviewPartsApiTest); the editor's button is drawn here.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
    $this->setUpAjax();
});

afterEach(function () {
    $this->tearDownAjax();
    set_current_screen('front');
});

/**
 * The verification token for a review — the same one the emailed link carries.
 */
function verificationToken(int $reviewId): string
{
    return glsr(Encryption::class)->encrypt($reviewId);
}

/*
 * The link in the email.
 */

test('following the link verifies the review and sends the person back to the page they left it on', function () {
    // The whole feature, end to end. The redirect is not decoration: the person is in their mail
    // client, and what they get back has to be the page with their review on it — carrying the
    // review_id and the token, which is what the javascript picks up to say "thank you".
    $postId = createPost();
    $review = createReview(['assigned_posts' => $postId, 'email' => 'jane@example.org']);
    expect($review->is_verified)->toBeFalse();

    $location = $this->expectsRedirectAndExit(fn () => glsr(VerificationController::class)->verifyReview(
        new Request(['data' => [$review->ID, '/a-page/']])
    ));

    expect(glsr_get_review($review->ID)->is_verified)->toBeTrue();
    expect($location)->toContain('/a-page/')
        ->toContain('review_id='.$review->ID)
        ->toContain('verified=');
});

test('a link for a review that is not there still lands somewhere, rather than nowhere', function () {
    // The review may have been deleted between the email being sent and the link being clicked —
    // which is a thing that happens, because the email sits in an inbox for weeks. A person who
    // clicks it gets the home page, not a blank screen or a fatal.
    $location = $this->expectsRedirectAndExit(fn () => glsr(VerificationController::class)->verifyReview(
        new Request(['data' => [999999, '/a-page/']])
    ));

    expect($location)->toBe(get_home_url())
        ->and($location)->not->toContain('verified=');
});

test('a review that is already verified is not sent a token to verify it again', function () {
    // VerifyReview reports failure for a review that is already verified, and no token is added —
    // so the page it lands on does not say "thank you, your review has been verified" a second
    // time, weeks later, to somebody who clicked an old email.
    $review = createReview();
    $this->expectsRedirectAndExit(fn () => glsr(VerificationController::class)->verifyReview(
        new Request(['data' => [$review->ID, '/']])
    ));

    $location = $this->expectsRedirectAndExit(fn () => glsr(VerificationController::class)->verifyReview(
        new Request(['data' => [$review->ID, '/']])
    ));

    expect($location)->toContain('review_id='.$review->ID)
        ->and($location)->not->toContain('verified=');
});

/*
 * The javascript on the page they land on.
 */

test('the review is handed back to the page, with a message that says whether it is live yet', function () {
    // Two different messages, and the difference matters to the person reading it: a site that
    // moderates reviews must not tell somebody their review is published when it is not.
    $review = createReview();

    $response = $this->jsonSentBy(fn () => glsr(AjaxController::class)->verifiedReviewAjax(
        new Request([
            'review_id' => $review->ID,
            'verified' => verificationToken($review->ID),
        ])
    ));

    expect($response['success'])->toBeTrue()
        ->and($response['data']['message'])->toContain('has been verified')
        ->and($response['data']['review'])->toContain($review->content);
});

test('a review awaiting approval says so, rather than claiming to be published', function () {
    $review = createReview(['is_approved' => false]);

    $response = $this->jsonSentBy(fn () => glsr(AjaxController::class)->verifiedReviewAjax(
        new Request([
            'review_id' => $review->ID,
            'verified' => verificationToken($review->ID),
        ])
    ));

    expect($response['data']['message'])->toContain('awaiting approval');
});

test('a token for somebody else\'s review will not fetch this one', function () {
    // THE security assertion of this file. The route is public and unguarded — no nonce, no login
    // — so the token is the only thing that ties the request to the review. Without the
    // `$reviewId !== $token` comparison, a valid token for ANY review would hand back ANY other.
    $mine = createReview(['content' => 'My review.']);
    $theirs = createReview(['content' => 'Somebody else\'s review.']);

    $response = $this->jsonSentBy(fn () => glsr(AjaxController::class)->verifiedReviewAjax(
        new Request([
            'review_id' => $theirs->ID,
            'verified' => verificationToken($mine->ID), // a token that is perfectly valid, for MY review
        ])
    ));

    expect($response['success'])->toBeFalse();
});

test('a token that is not a token at all is refused', function () {
    $review = createReview();

    $response = $this->jsonSentBy(fn () => glsr(AjaxController::class)->verifiedReviewAjax(
        new Request(['review_id' => $review->ID, 'verified' => 'not-a-token'])
    ));

    expect($response['success'])->toBeFalse();
});

test('a request with no review id is refused before anything is decrypted', function () {
    $response = $this->jsonSentBy(fn () => glsr(AjaxController::class)->verifiedReviewAjax(
        new Request(['review_id' => 0, 'verified' => ''])
    ));

    expect($response['success'])->toBeFalse();
});

/*
 * The admin's side.
 */

test('the editor offers to send a verification request, and then to send it again', function () {
    // The label is the only feedback the admin gets that the first one went — the review does not
    // become verified until the person clicks the link, so an unchanged button reads as a button
    // that did nothing.
    $review = createReview();
    glsr(OptionManager::class)->set('settings.general.request_verification', 'yes');
    set_current_screen(glsr()->post_type);

    ob_start();
    glsr(VerificationController::class)->renderVerifyAction(get_post($review->ID));
    $before = (string) ob_get_clean();

    glsr(PostMeta::class)->set($review->ID, 'verified_requested', true);

    ob_start();
    glsr(VerificationController::class)->renderVerifyAction(get_post($review->ID));
    $after = (string) ob_get_clean();

    expect($before)->toContain('Send Verification Request')
        ->and($before)->not->toContain('Resend Verification Request');
    expect($after)->toContain('Resend Verification Request');
});

test('the verify action is not drawn on anything that is not a review', function () {
    // post_submitbox_misc_actions fires in the editor of EVERY post type. Without the guard, this
    // would print a "Send Verification Request" button into the editor of every page on the site.
    ob_start();
    glsr(VerificationController::class)->renderVerifyAction(get_post(createPost()));

    expect((string) ob_get_clean())->toBe('');
});

