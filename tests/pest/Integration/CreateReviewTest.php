<?php

use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Helpers\Url;
use GeminiLabs\SiteReviews\Request;

use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The command behind a review submission — the full happy path is exercised through the public
 * controller and the validation suite. What is pinned here are the corners those do not reach: the
 * glsr_create_review validation failure, the empty-referer fallback, and the two "reload" helpers
 * that hand the ajax response fresh shortcode HTML once an approved review has been created.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
});

test('isRequestValid rejects a submission that fails validation', function () {
    // glsr_create_review validates through isRequestValid() rather than validate(); an empty
    // submission fails its required fields and is logged and refused.
    $command = new CreateReview(new Request([
        'content' => '', 'email' => '', 'name' => '', 'rating' => '', 'title' => '', 'form_id' => 'test-form',
    ]));

    expect($command->isRequestValid())->toBeFalse();
});

test('the referer falls back to the site home when the form carried none', function () {
    // referer() prefers an explicit redirect, then the form's referer; with neither it logs the
    // empty referer and sends the visitor to the home page rather than nowhere.
    $command = new CreateReview(new Request(['content' => 'Fine', 'rating' => '4']));
    $command->referer = '';  // the form carried no referer
    $command->post_id = 0;   // …and there is no redirect_to meta to find

    expect($command->referer())->toBe(Url::home());
});

test('an approved review reloads the reviews shortcode for the ajax response', function () {
    // When the submission form sits inside a reviews shortcode, an approved review reloads that
    // shortcode's HTML so the page can drop the new review in without a refresh.
    $command = new CreateReview(new Request(['_reviews_atts' => ['pagination' => 'ajax']]));
    (fn () => $this->review->is_approved = true)->call($command); // as it is once an approved review is created

    expect($command->reloadedReviews())->toBeString();
});

test('an approved review reloads the summary shortcode for the ajax response', function () {
    $command = new CreateReview(new Request(['_summary_atts' => ['rating' => 'this']]));
    (fn () => $this->review->is_approved = true)->call($command);

    expect($command->reloadedSummary())->toBeString();
});
