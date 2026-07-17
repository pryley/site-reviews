<?php

use GeminiLabs\SiteReviews\Controllers\UserController;
use GeminiLabs\SiteReviews\Role;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The capability arbiter: who may RESPOND to a review (the author of the review, or the
 * author of the page it reviews), and which posts a review may be assigned to at all.
 * Both are WordPress capability filters, so getting them wrong is invisible — the
 * capability simply answers the wrong way, everywhere.
 */

beforeEach(function () {
    resetPluginState();
});

function respondCapsFor(int $userId, int $reviewId): array
{
    return glsr(UserController::class)->filterMapMetaCap(
        [], 'respond_to_'.glsr()->post_type, $userId, [$reviewId]
    );
}

test('nobody may respond to a review that does not exist', function () {
    expect(respondCapsFor(createUser(), 999999001))->toBe(['do_not_allow']);
});

test('the review author may respond, and so may the author of the reviewed page', function () {
    $respond = glsr(Role::class)->capability('respond_to_posts');
    $reviewAuthor = createUser(['role' => 'author']);
    $pageAuthor = createUser(['role' => 'author']);
    $bystander = createUser(['role' => 'author']);
    $pageId = createPost(['post_author' => $pageAuthor]);
    $review = createReview(['author_id' => $reviewAuthor, 'assigned_posts' => $pageId]);

    expect(respondCapsFor($reviewAuthor, $review->ID))->toContain($respond);
    expect(respondCapsFor($pageAuthor, $review->ID))->toContain($respond);
    // anybody else needs the others-capability instead
    expect(respondCapsFor($bystander, $review->ID))
        ->toContain(glsr(Role::class)->capability('respond_to_others_posts'))
        ->not->toContain($respond);
});

test('a review can only be assigned to a post a visitor could actually see', function () {
    $controller = glsr(UserController::class);
    wp_set_current_user(createUser(['role' => 'subscriber']));

    $draft = createPost(['post_status' => 'draft']);
    $allcaps = $controller->filterUserHasCap([], ['assign_post'], ['assign_post', 0, $draft]);
    expect($allcaps)->not->toHaveKey('assign_post'); // a draft is neither public nor private

    $locked = createPost(['post_password' => 'secret']);
    $allcaps = $controller->filterUserHasCap([], ['assign_post'], ['assign_post', 0, $locked]);
    expect($allcaps)->not->toHaveKey('assign_post'); // password-protected, and they may not edit it

    $public = createPost();
    $allcaps = $controller->filterUserHasCap([], ['assign_post'], ['assign_post', 0, $public]);
    expect($allcaps)->toHaveKey('assign_post');
});
