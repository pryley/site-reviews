<?php

use GeminiLabs\SiteReviews\Integrations\DuplicatePage\Controller;
use GeminiLabs\SiteReviews\Tests\InteractsWithExits;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\protectedMethod;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

uses(InteractsWithExits::class);

/*
 * The Duplicate Page integration.
 *
 * Unlike Duplicate Post, this plugin offers no hook for copying the extra data —
 * it just makes the post — so the integration takes the action over entirely
 * (admin_action_dt_duplicate_post_as_draft, at priority 1), duplicates the review
 * itself, and redirects. A review's rating lives in a custom table, so a plain
 * post copy would be a review with no rating.
 *
 * duplicateReview() ends in wp_redirect() + exit, and two of its guards end in
 * wp_die(). InteractsWithExits turns both into exceptions — see that file for why
 * neither needs a change in production code — so the whole method is under test
 * here, redirect included.
 *
 * The controller is CALLED rather than fired through its hook, which is simply
 * clearer — HookProxy's try/catch would not have got in the way, since it is
 * skipped when PHPUNIT_TESTING is defined (see Support/InteractsWithExits).
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
});

/**
 * The request Duplicate Page sends: its "Duplicate This" link is a GET with the
 * post and a nonce it keys to that post. wp_create_nonce is bound to the current
 * user, so this has to run as whoever the test is logged in as.
 */
function duplicatePageRequest(int $postId): void
{
    $_GET['post'] = (string) $postId;
    $_GET['nonce'] = wp_create_nonce("dt-duplicate-page-{$postId}");
}

function duplicatePageRedirectUrl(int $postId): string
{
    return protectedMethod(Controller::class, 'redirectUrl')
        ->invoke(glsr(Controller::class), $postId);
}

/**
 * The review the redirect points at.
 */
function duplicatedReviewId(string $location): int
{
    parse_str((string) parse_url($location, PHP_URL_QUERY), $query);

    return (int) ($query['post'] ?? 0);
}

test('duplicating a review copies the review, not just the post', function () {
    $review = createReview(['rating' => 4, 'title' => 'The original']);
    duplicatePageRequest($review->ID);

    $location = $this->expectsRedirect(fn () => glsr(Controller::class)->duplicateReview());

    $copyId = duplicatedReviewId($location);
    expect($copyId)->not->toBe($review->ID);

    // The point of the whole integration: the copy has a row in the ratings table,
    // which is where a review's rating lives and which Duplicate Page knows
    // nothing about.
    $copy = glsr_get_review($copyId);
    expect($copy->isValid())->toBeTrue()
        ->and($copy->rating)->toBe(4)
        ->and($copy->content)->toBe($review->content);
});

test('the copy is titled the way duplicate page was told to title it', function () {
    // duplicate_post_suffix is Duplicate Page's own setting, and the integration
    // honours it rather than imposing its own.
    update_option('duplicate_page_options', ['duplicate_post_suffix' => 'copy']);
    $review = createReview(['title' => 'The original']);
    duplicatePageRequest($review->ID);

    $location = $this->expectsRedirect(fn () => glsr(Controller::class)->duplicateReview());

    expect(get_post(duplicatedReviewId($location))->post_title)->toBe('The original -- copy');
});

test('the copy keeps its title when duplicate page has no suffix', function () {
    delete_option('duplicate_page_options');
    $review = createReview(['title' => 'The original']);
    duplicatePageRequest($review->ID);

    $location = $this->expectsRedirect(fn () => glsr(Controller::class)->duplicateReview());

    expect(get_post(duplicatedReviewId($location))->post_title)->toBe('The original');
});

test('an ordinary post is handed back to duplicate page untouched', function () {
    // The first guard, and the important one: the action is registered for every
    // post type on the site, so without this the integration would hijack the
    // duplication of every page.
    $postId = createPost();
    duplicatePageRequest($postId);

    glsr(Controller::class)->duplicateReview(); // returns: no redirect, no wp_die

    expect(get_posts(['post_type' => glsr()->post_type, 'post_status' => 'any']))->toBe([]);
});

test('a bad nonce is handed back to duplicate page untouched', function () {
    // It returns rather than dying, so that Duplicate Page reports the failure in
    // its own words.
    $review = createReview();
    $_GET['post'] = (string) $review->ID;
    $_GET['nonce'] = 'not-the-nonce';
    $reviewCount = fn (): int => count(get_posts([
        'fields' => 'ids',
        'numberposts' => -1,
        'post_status' => 'any',
        'post_type' => glsr()->post_type,
    ]));
    $before = $reviewCount();

    glsr(Controller::class)->duplicateReview();

    expect($reviewCount())->toBe($before); // nothing was duplicated
});

test('a user who cannot edit posts is refused', function () {
    $review = createReview();
    wp_set_current_user(createUser(['role' => 'subscriber']));
    duplicatePageRequest($review->ID); // the nonce belongs to the subscriber

    $message = $this->expectsWpDie(fn () => glsr(Controller::class)->duplicateReview());

    expect($message)->toBe('Unauthorized Access.');
});

test('a review post with no review behind it is refused', function () {
    // A post of the review type that never went through the plugin — no row in the
    // ratings table — passes Review::isReview() but has nothing to duplicate.
    $postId = (int) wp_insert_post([
        'post_status' => 'publish',
        'post_title' => 'Not really a review',
        'post_type' => glsr()->post_type,
    ], true);
    duplicatePageRequest($postId);

    $message = $this->expectsWpDie(fn () => glsr(Controller::class)->duplicateReview());

    expect($message)->toBe('Invalid review.');
});

test('the duplicate opens for editing by default', function () {
    delete_option('duplicate_page_options');
    $postId = createPost();

    expect(duplicatePageRedirectUrl($postId))
        ->toBe(admin_url('post.php?action=edit&post='.$postId));
});

test('the duplicate returns to the review list when duplicate page says so', function () {
    update_option('duplicate_page_options', ['duplicate_post_redirect' => 'to_page']);
    $postId = createPost();

    // Note it returns to the REVIEW list, not to the list of whatever post type
    // Duplicate Page was invoked from — a duplicated review is a review.
    expect(duplicatePageRedirectUrl($postId))
        ->toBe(admin_url('edit.php?post_type='.glsr()->post_type));
});
