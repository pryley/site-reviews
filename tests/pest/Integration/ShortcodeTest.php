<?php

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createReviews;
use function GeminiLabs\SiteReviews\Tests\createTerm;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The four shortcodes, rendered end to end.
 *
 * These are the plugin's real entry points: a shortcode pulls its arguments
 * through the Defaults pipeline, queries the database, and renders through the
 * template/tag/style machinery. Asserting on the rendered HTML is the cheapest
 * way to hold that whole stack to its contract, and the most honest one — it is
 * exactly what a visitor gets.
 *
 * The assertions check content and structure, not exact markup: the HTML varies
 * by style framework (see config/styles), and pinning it byte-for-byte would
 * turn every CSS tweak into a test failure.
 *
 * Note the two different field-name prefixes, both verified against the source:
 * the review form uses glsr()->id ("site-reviews", from Field::namePrefix),
 * while the settings form uses OptionManager::databaseKey() ("site_reviews",
 * from SettingField::namePrefix).
 */

beforeEach(fn () => resetPluginState());

test('renders reviews', function () {
    $review = createReview([
        'content' => 'The pizza was excellent and the staff were friendly.',
        'name' => 'Jane Doe',
        'title' => 'Excellent pizza',
    ]);
    $html = do_shortcode('[site_reviews]');
    expect($html)->toContain('The pizza was excellent and the staff were friendly.')
        ->toContain('Excellent pizza')
        ->toContain('Jane Doe')
        ->toContain('glsr-reviews-wrap')
        ->toContain('data-rating="5"')
        ->toContain('id="review-'.$review->ID.'"');
});

test('renders no reviews when there are none', function () {
    $html = do_shortcode('[site_reviews]');
    expect($html)->not->toContain('id="review-');
});

test('limits the number of reviews rendered', function () {
    createReviews(5);
    $html = do_shortcode('[site_reviews display=2]');
    expect(substr_count($html, 'id="review-'))->toBe(2);
});

test('hides the review fields it is told to hide', function () {
    // hide options: title, rating, date, assigned_links, content, avatar, author,
    // verified, response (SiteReviewsShortcode::hideOptions)
    createReview([
        'content' => 'Hidden content check.',
        'title' => 'Hidden title check',
    ]);
    $html = do_shortcode('[site_reviews hide=title,content]');
    expect($html)->toContain('id="review-')
        ->not->toContain('Hidden content check.')
        ->not->toContain('Hidden title check');
});

test('renders only the reviews assigned to a post', function () {
    $postId = createPost();
    createReview(['assigned_posts' => $postId, 'content' => 'Assigned to the post.']);
    createReview(['content' => 'Assigned to nothing.']);
    $html = do_shortcode("[site_reviews assigned_posts={$postId}]");
    expect($html)->toContain('Assigned to the post.')
        ->not->toContain('Assigned to nothing.');
});

test('renders only the reviews assigned to a category', function () {
    $termId = createTerm(['taxonomy' => glsr()->taxonomy]);
    createReview(['assigned_terms' => $termId, 'content' => 'Assigned to the category.']);
    createReview(['content' => 'Assigned to nothing.']);
    $html = do_shortcode("[site_reviews assigned_terms={$termId}]");
    expect($html)->toContain('Assigned to the category.')
        ->not->toContain('Assigned to nothing.');
});

test('renders only the reviews assigned to a user', function () {
    $userId = createUser();
    createReview(['assigned_users' => $userId, 'content' => 'Assigned to the user.']);
    createReview(['content' => 'Assigned to nothing.']);
    $html = do_shortcode("[site_reviews assigned_users={$userId}]");
    expect($html)->toContain('Assigned to the user.')
        ->not->toContain('Assigned to nothing.');
});

test('renders only the reviews rated at least the given rating', function () {
    // Sql::clauseAndRating() is "rating > (rating - 1)", i.e. at least.
    createReview(['content' => 'A five star review.', 'rating' => 5]);
    createReview(['content' => 'A one star review.', 'rating' => 1]);
    $html = do_shortcode('[site_reviews rating=5]');
    expect($html)->toContain('A five star review.')
        ->not->toContain('A one star review.');
});

test('paginates', function () {
    // pagination enum: ajax, loadmore, 1, true (SiteReviewsDefaults)
    createReviews(3);
    $html = do_shortcode('[site_reviews display=1 pagination=true]');
    expect(substr_count($html, 'id="review-'))->toBe(1);
    expect($html)->toContain('class="pagination"');
});

test('renders a single review', function () {
    // The review is selected with post_id; "id" is the shortcode's own custom id.
    $review = createReview(['content' => 'The single review.']);
    $html = do_shortcode("[site_review post_id={$review->ID}]");
    expect($html)->toContain('The single review.')
        ->toContain('id="review-'.$review->ID.'"');
});

test('renders nothing for a single review that does not exist', function () {
    $html = do_shortcode('[site_review post_id=999999001]');
    expect($html)->not->toContain('id="review-');
});

test('renders the summary of all reviews', function () {
    createReview(['rating' => 5]);
    createReview(['rating' => 3]);
    $html = do_shortcode('[site_reviews_summary]');
    // data-rating / data-reviews are set by the star-rating partial.
    expect($html)->toContain('glsr-summary-wrap')
        ->toContain('data-reviews="2"');
});

test('renders the summary of only the reviews assigned to a post', function () {
    $postId = createPost();
    createReview(['assigned_posts' => $postId, 'rating' => 1]);
    createReview(['rating' => 5]); // not assigned: must not count
    $html = do_shortcode("[site_reviews_summary assigned_posts={$postId}]");
    expect($html)->toContain('glsr-summary-wrap')
        ->toContain('data-rating="1"')
        ->toContain('data-reviews="1"');
});

test('renders the review form', function () {
    $html = do_shortcode('[site_reviews_form]');
    expect($html)->toContain('<form')
        ->toContain('glsr-form-wrap')
        ->toContain('name="site-reviews[content]"')
        ->toContain('name="site-reviews[rating]"')
        // hidden fields, from ReviewForm::configHidden()
        ->toContain('name="site-reviews[_action]"')
        ->toContain('name="site-reviews[_nonce]"')
        ->toContain('name="site-reviews[form_id]"');
});

test('hides the review form fields it is told to hide', function () {
    // hide options: rating, title, content, name, email, terms
    // (SiteReviewsFormShortcode::hideOptions)
    $html = do_shortcode('[site_reviews_form hide=title,email]');
    expect($html)->toContain('name="site-reviews[content]"')
        ->not->toContain('name="site-reviews[title]"')
        ->not->toContain('name="site-reviews[email]"');
});

test('renders the review form honeypot', function () {
    // Form::build() always appends Honeypot::build(), hidden with inline styles.
    $html = do_shortcode('[site_reviews_form]');
    expect($html)->toContain('display:none;');
});
