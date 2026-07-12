<?php

use GeminiLabs\SiteReviews\Controllers\ListTableController;
use GeminiLabs\SiteReviews\Overrides\ReviewsListTable;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The review list table.
 *
 * WordPress draws it, and the plugin bends it into shape through filters: which
 * columns exist, which are hidden, which can be sorted, what the row actions are,
 * and — the one with teeth — what a search actually searches.
 *
 * Every method here is a filter callback, so each test calls it the way WordPress
 * would, with the arguments WordPress passes.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
    $this->pagenow = $GLOBALS['pagenow'] ?? 'index.php';
    $this->theQuery = $GLOBALS['wp_the_query'] ?? null;
});

afterEach(function () {
    set_current_screen('front');
    $GLOBALS['pagenow'] = $this->pagenow ?? 'index.php';
    $GLOBALS['wp_the_query'] = $this->theQuery ?? new WP_Query();
});

/**
 * The query the review list table is actually drawn from.
 *
 * AbstractController::hasQueryPermission() guards every query filter here, and it
 * wants FOUR things at once: an admin screen, $pagenow === 'edit.php', the review
 * post type, and is_main_query() — which is not a flag but an identity check
 * against $GLOBALS['wp_the_query']. Set up anything less and the filter returns its
 * argument untouched, and a test asserting "it was left alone" would pass for
 * entirely the wrong reason.
 */
function mainReviewQuery(array $args = []): WP_Query
{
    set_current_screen('edit-'.glsr()->post_type);
    $GLOBALS['pagenow'] = 'edit.php';
    $query = new WP_Query();
    $query->set('post_type', glsr()->post_type);
    foreach ($args as $key => $value) {
        $query->set($key, $value);
    }
    $GLOBALS['wp_the_query'] = $query; // this is what is_main_query() asks

    return $query;
}

test('the review columns replace the ones wordpress would have shown', function () {
    // The stored column list is built at registration (RegisterPostType::setColumns).
    // A key WordPress already knows about keeps WordPress's label; the rest are ours.
    $columns = glsr(ListTableController::class)->filterColumnsForPostType([
        'cb' => '<input type="checkbox" />',
        'title' => 'Title',
        'date' => 'Date',
    ]);

    expect($columns)->toHaveKey('rating')
        ->and($columns)->toHaveKey('taxonomy-'.glsr()->taxonomy)
        ->and($columns['cb'])->toBe('<input type="checkbox" />'); // WordPress's own is kept
});

test('a review is submitted, not published', function () {
    // "Published" is the wrong word for something a stranger wrote and an
    // administrator has not looked at yet.
    $review = createReview();
    $post = get_post($review->ID);

    expect(glsr(ListTableController::class)->filterDateColumnStatus('Published', $post))
        ->toBe('Submitted');

    // and an ordinary post is left alone
    expect(glsr(ListTableController::class)->filterDateColumnStatus('Published', get_post(createPost())))
        ->toBe('Published');
});

test('the noisy columns are hidden on the review screen and nowhere else', function () {
    $reviewScreen = WP_Screen::get('edit-'.glsr()->post_type);
    $postScreen = WP_Screen::get('edit-post');

    $hidden = glsr(ListTableController::class)->filterDefaultHiddenColumns(['comments'], $reviewScreen);
    expect($hidden)->toContain('comments')   // whatever WordPress hid stays hidden
        ->toContain('ip_address')
        ->toContain('author_email');

    expect(glsr(ListTableController::class)->filterDefaultHiddenColumns(['comments'], $postScreen))
        ->toBe(['comments']); // the post list table is not ours to touch
});

test('the review list table is drawn by the plugin\'s own class', function () {
    set_current_screen('edit-'.glsr()->post_type);
    expect(glsr(ListTableController::class)->filterListTableClass('WP_Posts_List_Table'))
        ->toBe(ReviewsListTable::class);

    // but only on the list screen, and only for reviews
    set_current_screen('edit-post');
    expect(glsr(ListTableController::class)->filterListTableClass('WP_Posts_List_Table'))
        ->toBe('WP_Posts_List_Table');
});

test('the assigned columns cannot be sorted, and the rest can', function () {
    // Sorting by "assigned pages" is meaningless — a review can be assigned to
    // several, and there is nothing to order by.
    $columns = glsr(ListTableController::class)->filterSortableColumns([]);

    expect($columns)->toHaveKey('rating')
        ->and($columns)->not->toHaveKey('cb')
        ->and($columns)->not->toHaveKey('assigned_posts')
        ->and($columns)->not->toHaveKey('taxonomy-'.glsr()->taxonomy);
});

test('searching for a number finds the review with that id', function () {
    // The one filter here with teeth. An administrator hunting a review from a
    // support ticket has its ID, not its text — so a numeric search matches the ID as
    // well as the content, rather than instead of it.
    $review = createReview();
    $query = mainReviewQuery(['s' => (string) $review->ID]);

    global $wpdb;
    $search = " AND ((({$wpdb->posts}.post_title LIKE '%123%'))) ";

    $filtered = glsr(ListTableController::class)->filterSearchQuery($search, $query);

    expect($filtered)->toContain("{$wpdb->posts}.ID = {$review->ID}")
        ->toContain('post_title LIKE'); // and it still searches the text too
});

test('searching for text is left alone', function () {
    $query = mainReviewQuery(['s' => 'some words']);

    global $wpdb;
    $search = " AND ((({$wpdb->posts}.post_title LIKE '%some words%'))) ";

    expect(glsr(ListTableController::class)->filterSearchQuery($search, $query))->toBe($search);
});

test('the row actions offer approving and responding to somebody who may', function () {
    $review = createReview(['is_approved' => false]);
    $post = get_post($review->ID);

    $actions = glsr(ListTableController::class)->filterRowActions(['edit' => 'Edit'], $post);

    expect($actions)->toHaveKey('id')                     // the ID, for support tickets
        ->and($actions)->toHaveKey('approve')
        ->and($actions)->toHaveKey('unapprove')
        ->and($actions)->toHaveKey('respond hide-if-no-js')
        ->and($actions)->toHaveKey('edit')                // WordPress's own survive
        ->and($actions)->not->toHaveKey('inline hide-if-no-js'); // Quick Edit is replaced
    expect($actions['approve'])->toContain('_wpnonce');   // and it is nonced
});

test('the row actions offer nothing to somebody who may not', function () {
    $review = createReview();
    $post = get_post($review->ID);
    wp_set_current_user(createUser(['role' => 'subscriber']));

    $actions = glsr(ListTableController::class)->filterRowActions(['edit' => 'Edit'], $post);

    expect($actions)->toHaveKey('id')
        ->and($actions)->not->toHaveKey('approve')
        ->and($actions)->not->toHaveKey('respond hide-if-no-js');
});

test('a trashed review and an ordinary post keep their own row actions', function () {
    $review = createReview();
    wp_trash_post($review->ID);
    $trashed = get_post($review->ID);
    $actions = ['edit' => 'Edit', 'untrash' => 'Restore'];

    expect(glsr(ListTableController::class)->filterRowActions($actions, $trashed))->toBe($actions);
    expect(glsr(ListTableController::class)->filterRowActions($actions, get_post(createPost())))->toBe($actions);
});

test('a column prints the value of its review', function () {
    $review = createReview(['rating' => 4]);

    ob_start();
    glsr(ListTableController::class)->renderColumnValues('rating', $review->ID);
    $output = (string) ob_get_clean();

    expect($output)->toContain('star-rating');
});

test('a column with nothing in it prints a dash rather than a blank', function () {
    // An empty cell reads as a rendering bug. A dash reads as "there is nothing here".
    $review = createReview(); // assigned to nothing

    ob_start();
    glsr(ListTableController::class)->renderColumnValues('assigned_posts', $review->ID);
    $output = (string) ob_get_clean();

    expect($output)->toBe('&mdash;');
});

test('a column asked about a post that is not a review prints nothing', function () {
    ob_start();
    glsr(ListTableController::class)->renderColumnValues('rating', createPost());
    $output = (string) ob_get_clean();

    expect($output)->toBe('');
});
