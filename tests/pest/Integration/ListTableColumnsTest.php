<?php

use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnFilterAssignedPost;
use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnFilterAuthor;
use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnFilterCategory;
use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnFilterRating;
use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnFilterType;
use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnValueAssignedPosts;
use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnValueAssignedUsers;
use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnValueAuthorEmail;
use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnValueAuthorName;
use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnValueIpAddress;
use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnValueIsPinned;
use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnValueIsVerified;
use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnValueRating;
use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnValueResponse;
use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnValueType;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createTerm;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The review list table's columns, and the dropdowns that filter it.
 *
 * A ColumnValue turns one review into one cell of HTML. A ColumnFilter renders one
 * dropdown above the table. Both are reached from ListTableController on
 * manage_{post_type}_posts_custom_column and restrict_manage_posts, which are admin
 * hooks — so these are called directly, which is what the controller does anyway.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
});

test('the rating is drawn as stars', function () {
    $review = createReview(['rating' => 4]);

    expect(glsr(ColumnValueRating::class)->handle($review))
        ->toContain('star-rating')
        ->toContain('4'); // wp_star_rating puts the rating in the screen-reader text
});

test('a rating beyond the star scale is shown as a number', function () {
    // The scale is filterable, and an addon that raises MAX_RATING past 5 would draw
    // eleven stars in a table cell. Beyond five it degrades to "8 / 10".
    add_filter('site-reviews/const/MAX_RATING', fn () => 10);
    $review = createReview(['rating' => 8]);

    expect(glsr(ColumnValueRating::class)->handle($review))
        ->not->toContain('star-rating')
        ->toContain('8 / 10');
});

test('the assigned pages are linked by title', function () {
    $postId = createPost(['post_title' => 'The Big Page']);
    $review = createReview(['assigned_posts' => $postId]);

    expect(glsr(ColumnValueAssignedPosts::class)->handle($review))
        ->toContain('The Big Page')
        ->toContain(get_the_permalink($postId));
});

test('an assigned page with no title falls back to something identifiable', function () {
    // A cell reading "(no title)" tells nobody which page it is.
    $postId = createPost(['post_title' => '', 'post_name' => 'a-slug']);
    $review = createReview(['assigned_posts' => $postId]);

    expect(glsr(ColumnValueAssignedPosts::class)->handle($review))->toContain('a-slug');
});

test('a review assigned to nothing has an empty cell', function () {
    expect(glsr(ColumnValueAssignedPosts::class)->handle(createReview()))->toBe('');
    expect(glsr(ColumnValueAssignedUsers::class)->handle(createReview()))->toBe('');
});

test('the assigned users are linked by name', function () {
    $userId = createUser(['display_name' => 'Jane Doe']);
    $review = createReview(['assigned_users' => $userId]);

    expect(glsr(ColumnValueAssignedUsers::class)->handle($review))
        ->toContain('Jane Doe')
        ->toContain(get_author_posts_url($userId));
});

test('an assigned user who has since been deleted is skipped, not fatal', function () {
    $userId = createUser();
    $review = createReview(['assigned_users' => $userId]);
    wp_delete_user($userId);
    $review->refresh();

    expect(glsr(ColumnValueAssignedUsers::class)->handle($review))->toBe('');
});

test('the author name links to the user when the review belongs to one', function () {
    $userId = createUser(['display_name' => 'Jane Doe']);
    $review = createReview(['author_id' => $userId, 'name' => 'Jane Doe']);

    expect(glsr(ColumnValueAuthorName::class)->handle($review))
        ->toContain('<a')
        ->toContain(get_author_posts_url($userId));
});

test('the author name is plain text for a review left by a stranger', function () {
    // Nobody is logged in, so there is no user to link to. It has to be said out
    // loud: CreateReviewDefaults sanitizes author_id with `user-id:current_user`, so
    // a review created while an administrator is logged in belongs to that
    // administrator — which is right for one added in the admin, and wrong for the
    // visitor this test is about.
    wp_set_current_user(0);
    $review = createReview(['name' => 'A Visitor']);
    expect((int) $review->author_id)->toBe(0);

    expect(glsr(ColumnValueAuthorName::class)->handle($review))->toBe('A Visitor');
});

test('the email and ip address columns are the raw values', function () {
    $review = createReview(['email' => 'someone@example.org', 'ip_address' => '127.0.0.1']);

    expect(glsr(ColumnValueAuthorEmail::class)->handle($review))->toBe('someone@example.org');
    expect(glsr(ColumnValueIpAddress::class)->handle($review))->toBe('127.0.0.1');
});

test('the response column says whether there is one, not what it is', function () {
    expect(glsr(ColumnValueResponse::class)->handle(createReview()))->toBe('No');
    expect(glsr(ColumnValueResponse::class)->handle(createReview(['response' => 'Thanks!'])))->toBe('Yes');
});

test('the pinned column is clickable only for someone who may pin', function () {
    // The class is what the admin JS binds the click handler to. Without the
    // capability the icon is still drawn — it just does nothing.
    $review = createReview(['is_pinned' => true]);

    expect(glsr(ColumnValueIsPinned::class)->handle($review))
        ->toContain('pinned')
        ->toContain('pin-review')                    // an administrator may
        ->toContain('data-id="'.$review->ID.'"');

    wp_set_current_user(createUser(['role' => 'subscriber']));
    expect(glsr(ColumnValueIsPinned::class)->handle($review))
        ->toContain('pinned')
        ->not->toContain('pin-review');              // a subscriber may not
});

test('the verified column is clickable only when verification is on', function () {
    // Even an administrator cannot verify by hand on a site where the feature is
    // switched off.
    $review = createReview();

    expect(glsr(ColumnValueIsVerified::class)->handle($review))->not->toContain('verify-review');

    add_filter('site-reviews/verification/enabled', '__return_true');
    expect(glsr(ColumnValueIsVerified::class)->handle($review))->toContain('verify-review');
});

test('a review from a platform with no logo shipped for it degrades to the word', function () {
    // A local review is just the word. A review imported from another platform is
    // shown with that platform's logo — but only if one is shipped, which is what
    // stops an addon inventing a platform and rendering a broken image.
    expect(glsr(ColumnValueType::class)->handle(createReview()))->not->toContain('<svg');

    $review = createReview();
    $review->set('type', 'notaplatform');

    expect(glsr(ColumnValueType::class)->handle($review))
        ->not->toContain('<svg')
        ->not->toBeEmpty();
});

/*
 * The filter dropdowns.
 */

test('a filter is drawn hidden until it is switched on', function () {
    // Every filter is always rendered — the "Show filters" toggle in the admin just
    // unhides them — so the class is the whole difference.
    $filter = glsr(ColumnFilterRating::class);

    expect($filter->handle([]))->toContain('is-hidden');
    expect($filter->handle(['rating']))->not->toContain('is-hidden');
});

test('a filter knows its own name, id and action', function () {
    // All three are derived from the class name, and the JS binds to them.
    $filter = glsr(ColumnFilterAssignedPost::class);

    expect($filter->name())->toBe('assigned_post')
        ->and($filter->id())->toBe('glsr-filter-by-assigned_post')
        ->and($filter->action())->toBe('filter-assigned_post');
});

test('the rating filter offers every rating on the scale', function () {
    // Keyed by the rating, and each option is drawn as filled and empty stars rather
    // than as a number — so an option is an array, not a string.
    $options = glsr(ColumnFilterRating::class)->options();

    expect(array_keys($options))->toBe([5, 4, 3, 2, 1, 0]) // highest first
        ->and($options[5]['text'])->toBe('★★★★★')
        ->and($options[3]['text'])->toBe('★★★☆☆')
        ->and($options[0]['text'])->toBe('☆☆☆☆☆');
});

test('the category filter offers the categories that have reviews in them', function () {
    // get_terms() is called with hide_empty, so a category nobody has used is not
    // offered — filtering by it could only ever return nothing.
    $used = createTerm(['name' => 'Service', 'taxonomy' => glsr()->taxonomy]);
    $unused = createTerm(['name' => 'Nobody Uses This', 'taxonomy' => glsr()->taxonomy]);
    createReview(['assigned_terms' => $used]);

    $options = glsr(ColumnFilterCategory::class)->options();

    expect($options)->toHaveKey($used)
        ->and($options[$used])->toBe('Service')
        ->and($options)->not->toHaveKey($unused);

    // and "No category" is prepended, so a review with none can be found
    expect($options)->toHaveKey('-1');
});

test('the category filter names itself, and the empty state, for the screen', function () {
    $filter = glsr(ColumnFilterCategory::class);

    expect($filter->label())->toBe('Filter by category')
        ->and($filter->placeholder())->toBe('Any category');
});

test('the category filter, on a filtered screen, shows the category by its taxonomy id', function () {
    // On the review screen WordPress puts the category SLUG in the main query; the dropdown is keyed
    // by term_taxonomy_id, so value() translates the one to the other to stay selected.
    $termId = createTerm(['name' => 'Service', 'taxonomy' => glsr()->taxonomy]);
    $term = get_term($termId, glsr()->taxonomy);

    global $wp_query;
    $original = $wp_query;
    $wp_query = new WP_Query();
    $wp_query->set(glsr()->taxonomy, $term->slug);
    try {
        $value = glsr(ColumnFilterCategory::class)->value();
    } finally {
        $wp_query = $original;
    }

    expect($value)->toBe((string) $term->term_taxonomy_id);
});

test('with no category in the query, the filter reads its value straight from the request', function () {
    // Off the taxonomy-archive path — the dropdown was just changed by hand — there is no slug in
    // the query, so it falls back to the number in the URL.
    $_GET['category'] = '42';

    global $wp_query;
    $original = $wp_query;
    $wp_query = new WP_Query(); // no taxonomy query var, so get_term_by() finds nothing
    try {
        $value = glsr(ColumnFilterCategory::class)->value();
    } finally {
        $wp_query = $original;
        unset($_GET['category']);
    }

    expect($value)->toBe('42');
});

test('the author filter names itself for the screen', function () {
    // It reuses the assigned-user machinery for everything but its labels — this is the one it
    // overrides, so the dropdown reads "Filter by author", not "Filter by assigned user".
    expect(glsr(ColumnFilterAuthor::class)->label())->toBe('Filter by author');
});

test('the type filter is offered when there is more than one type of review', function () {
    $options = glsr(ColumnFilterType::class)->options();

    // "local" is always there; a platform addon adds its own.
    expect($options)->not->toBeEmpty();
});
