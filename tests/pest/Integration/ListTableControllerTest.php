<?php

use GeminiLabs\SiteReviews\Controllers\ListTableController;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Overrides\ReviewsListTable;
use GeminiLabs\SiteReviews\Tests\InteractsWithAjax;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

uses(InteractsWithAjax::class);

/*
 * The review list table.
 *
 * WordPress draws it; the plugin bends it into shape through filters: which columns exist, which are
 * hidden, which sort, what the row actions are, and — the one with teeth — what a search actually
 * searches. Every method here is a filter callback, so each test calls it as WordPress would, with
 * the arguments WordPress passes.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
    $this->pagenow = $GLOBALS['pagenow'] ?? 'index.php';
    $this->theQuery = $GLOBALS['wp_the_query'] ?? null;
    $this->query = $GLOBALS['wp_query'] ?? null;
});

afterEach(function () {
    set_current_screen('front');
    $GLOBALS['pagenow'] = $this->pagenow ?? 'index.php';
    $GLOBALS['wp_the_query'] = $this->theQuery ?? new WP_Query();
    $GLOBALS['wp_query'] = $this->query ?? new WP_Query();
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

/*
 * =============================================================================
 * FILTERING AND ORDERING THE TABLE
 * =============================================================================
 *
 * None of what follows could be tested until the suite shadowed filter_input() (see
 * tests/pest/Support/filter-input.php). Every one of these paths reads the request through
 * filter_input(), which does not read $_GET — it reads the SAPI's own copy of the request,
 * which a CLI process does not have. So the whole of the table's filtering and ordering was
 * dark, and it is the biggest file in the plugin.
 *
 * The guard on all of it is hasQueryPermission(): the admin, the MAIN query, our post type,
 * and $pagenow === 'edit.php'. Everything below sets that up, and one test takes it away
 * again — because these filters hang off `posts_clauses` and `pre_get_posts`, which fire for
 * every query on the site, including the ones on the front end.
 */

/**
 * The reviews list table, mid-request, with the query string the person is filtering by.
 *
 * The query itself comes from mainReviewQuery() above — is_main_query() is an IDENTITY check
 * against $GLOBALS['wp_the_query'], not a flag, and faking it any other way gives a query that
 * fails the guard and a test that passes for the wrong reason.
 */
function onReviewsListTable(array $get = []): WP_Query
{
    $_GET = $get;
    $query = mainReviewQuery();
    // is_main_query() asks $wp_the_query, which mainReviewQuery() sets. But isListOrdered()
    // asks get_query_var(), which reads $wp_query — a DIFFERENT global that WordPress happens
    // to point at the same object on the main query. Set only one of them and the ordering
    // path is never entered, and the test passes for the wrong reason.
    $GLOBALS['wp_query'] = $query;

    return $query;
}

/**
 * The clauses WP_Query hands to `posts_clauses`.
 */
function postClauses(): array
{
    global $wpdb;

    return [
        'distinct' => '',
        'fields' => "{$wpdb->posts}.ID",
        'groupby' => '',
        'join' => '',
        'limits' => 'LIMIT 0, 20',
        'orderby' => "{$wpdb->posts}.post_date DESC",
        'where' => " AND {$wpdb->posts}.post_type = 'site-review'",
    ];
}

test('filtering by rating joins the ratings table and asks it for the rating', function () {
    $query = onReviewsListTable(['rating' => '5']);

    $clauses = glsr(ListTableController::class)->filterPostClauses(postClauses(), $query);

    expect($clauses['join'])->toContain('INNER JOIN')
        ->and($clauses['join'])->toContain('glsr_ratings')
        ->and($clauses['where'])->toContain("glsr_ratings.rating = '5'")
        ->and($clauses['where'])->toContain("post_type = 'site-review'"); // and the original where survives
});

test('filtering by an assigned page asks for the reviews about it', function () {
    $query = onReviewsListTable(['assigned_post' => '42']);

    $clauses = glsr(ListTableController::class)->filterPostClauses(postClauses(), $query);

    expect($clauses['join'])->toContain('glsr_assigned_posts')
        ->and($clauses['where'])->toContain('glsr_assigned_posts.post_id = 42');
});

test('filtering by "no assigned page" asks for the reviews about nothing', function () {
    // A LEFT JOIN and an IS NULL, not an INNER JOIN — the whole point is the reviews that have
    // no row in that table at all. An INNER JOIN here would return nothing, every time.
    $query = onReviewsListTable(['assigned_post' => '0']);

    $clauses = glsr(ListTableController::class)->filterPostClauses(postClauses(), $query);

    expect($clauses['join'])->toContain('LEFT JOIN')
        ->and($clauses['where'])->toContain('glsr_assigned_posts.post_id IS NULL');
});

test('ordering by a review column orders by the ratings table, not the posts table', function () {
    $query = onReviewsListTable();
    $query->set('orderby', 'rating');
    $query->set('order', 'DESC');

    $clauses = glsr(ListTableController::class)->filterPostClauses(postClauses(), $query);

    expect($clauses['orderby'])->toContain('glsr_ratings.rating DESC')
        ->and($clauses['orderby'])->not->toContain('post_date'); // replaced, not appended
});

test('ordering by a column that is often empty puts the empty ones last', function () {
    // Sorting by email with a hundred anonymous reviews at the top is a sort that shows you
    // nothing. NULLIF(...) IS NULL sorts the blanks to the bottom whichever way round it goes.
    //
    // `author_email` is the sortable COLUMN; `email` is the ratings column it maps to
    // (ColumnOrderbyDefaults). They are not the same name and it matters here.
    $query = onReviewsListTable();
    $query->set('orderby', 'author_email');
    $query->set('order', 'ASC');

    $clauses = glsr(ListTableController::class)->filterPostClauses(postClauses(), $query);

    // The real table name, prefix and all — `wp_glsr_ratings` on this install. The other
    // assertions in this file get away with the bare `glsr_ratings` only because it is a
    // substring of it.
    $ratings = glsr(Tables::class)->table('ratings');
    expect($clauses['orderby'])->toContain("NULLIF({$ratings}.email, '') IS NULL")
        ->and($clauses['orderby'])->toContain('post_date'); // the blanks sort last, then by date
});

test('an unfiltered, unordered table is left exactly as wordpress built it', function () {
    // The common case — somebody opened the reviews screen. No join, no rewritten where.
    $query = onReviewsListTable();

    expect(glsr(ListTableController::class)->filterPostClauses(postClauses(), $query))
        ->toBe(postClauses());
});

test('nobody else\'s query is touched', function () {
    // posts_clauses fires for EVERY query on the site. Four guards, and this is what they are
    // for: the front-end query that renders the page a review is displayed on goes through
    // here too, on every page load.
    global $pagenow;
    $query = onReviewsListTable(['rating' => '5']);

    $pagenow = 'index.php'; // …not the list table
    expect(glsr(ListTableController::class)->filterPostClauses(postClauses(), $query))->toBe(postClauses());

    $pagenow = 'edit.php';
    $query->set('post_type', 'post'); // …somebody else's post type
    expect(glsr(ListTableController::class)->filterPostClauses(postClauses(), $query))->toBe(postClauses());

    $query->set('post_type', glsr()->post_type);
    set_current_screen('front'); // …not the admin at all
    expect(glsr(ListTableController::class)->filterPostClauses(postClauses(), $query))->toBe(postClauses());
});

/*
 * pre_get_posts.
 */

test('ordering by the response orders by the meta it lives in', function () {
    // The response is post meta, not a ratings column, so it cannot be ordered the same way.
    $query = onReviewsListTable();
    $query->set('orderby', 'response');

    glsr(ListTableController::class)->setQueryForTable($query);

    expect($query->get('meta_key'))->toBe('_response')
        ->and($query->get('orderby'))->toBe('meta_value');
});

test('filtering by a category asks for the reviews in it', function () {
    $query = onReviewsListTable(['category' => '7']);

    glsr(ListTableController::class)->setQueryForTable($query);

    $taxQuery = $query->get('tax_query');
    expect($taxQuery[0]['taxonomy'])->toBe(glsr()->taxonomy)
        ->and($taxQuery[0]['terms'])->toBe('7');
});

test('filtering by "no category" asks for the reviews in none of them', function () {
    // -1 is the "Uncategorized" option in the dropdown, and it is NOT a term id.
    $query = onReviewsListTable(['category' => '-1']);

    glsr(ListTableController::class)->setQueryForTable($query);

    $taxQuery = $query->get('tax_query');
    expect($taxQuery[0]['operator'])->toBe('NOT EXISTS')
        ->and($taxQuery[0])->not->toHaveKey('terms');
});

/*
 * The filter controls themselves.
 */

test('a column filter shows what it is currently filtered by', function () {
    $_GET = ['rating' => '4'];

    expect(glsr(\GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnFilterRating::class)->value())
        ->toBe('4');
});

test('the rating filter offers every rating, and a way out of it', function () {
    $filter = glsr(\GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnFilterRating::class);

    expect($filter->options())->toHaveCount(6) // 5..0
        ->and($filter->options()[5]['text'])->toBe('★★★★★')
        ->and($filter->options()[0]['text'])->toBe('☆☆☆☆☆')
        ->and($filter->placeholder())->toBe('Any rating');
});

test('the filters a person has switched on are remembered, and rating is on by default', function () {
    wp_set_current_user(createUser(['role' => 'administrator']));
    $screen = WP_Screen::get('edit-'.glsr()->post_type);

    $settings = glsr(ListTableController::class)->filterScreenFilters('', $screen);

    expect($settings)->toContain('Rating');
    expect(get_user_meta(get_current_user_id(), 'edit_site-review_filters', true))
        ->toBe(['rating']); // the default, written on first sight of the screen

    // and nobody else's screen gets our settings panel
    expect(glsr(ListTableController::class)->filterScreenFilters('', WP_Screen::get('edit-post')))
        ->toBe('');
});

test('the filter dropdowns are printed above the review table, and no other', function () {
    // restrict_manage_posts fires above every list table on the site; the dropdowns are drawn only
    // for the review post type, and only for the filters the person has switched on.
    update_user_meta(get_current_user_id(), 'edit_'.glsr()->post_type.'_filters', ['rating']);
    set_current_screen('edit-'.glsr()->post_type);

    ob_start();
    glsr(ListTableController::class)->renderColumnFilters(glsr()->post_type);
    $ours = (string) ob_get_clean();

    ob_start();
    glsr(ListTableController::class)->renderColumnFilters('post'); // somebody else's list table
    $theirs = (string) ob_get_clean();

    expect($ours)->toContain('rating')  // at least the rating dropdown
        ->and($theirs)->toBe('');
});

/*
 * Quick Edit: saving a response inline.
 *
 * wp_ajax_inline_save is WordPress's own, and this overrides it for the review screen so that the
 * one thing Quick Edit changes on a review — the site owner's response — is saved and the row
 * redrawn. It refuses any screen that is not ours, so it never touches Quick Edit anywhere else.
 */

test('the inline save leaves every other post type to WordPress', function () {
    // The guard, and it is the whole reason this is safe to hook onto a global ajax action: a
    // wrong screen returns immediately, before the nonce check or anything else.
    $review = createReview();
    $_POST = ['screen' => 'edit-post', 'post_ID' => $review->ID, '_response' => 'should not save'];

    glsr(ListTableController::class)->overrideInlineSaveAjax();

    expect(glsr(ReviewManager::class)->get($review->ID, true)->response)
        ->not->toBe('should not save'); // untouched (a review with no response reads back null)
    $_POST = [];
});

test('the inline save writes the response and redraws the row', function () {
    // The whole feature: the response typed into Quick Edit is saved to the review, and the row is
    // reprinted so the screen updates without a reload. The side effect is what is asserted — the
    // reprinted HTML is discarded by the ajax die, exactly as the browser would replace the row.
    $review = createReview();
    set_current_screen('edit-'.glsr()->post_type);
    $this->setUpAjax();
    $nonce = wp_create_nonce('inlineeditnonce');
    $_POST = [
        'screen' => 'edit-'.glsr()->post_type,
        'post_ID' => (string) $review->ID,
        '_inline_edit' => $nonce,
        '_response' => 'Thank you for the kind words.',
        'post_view' => 'list',
    ];
    $_REQUEST['_inline_edit'] = $nonce; // check_ajax_referer() reads the nonce from $_REQUEST, not $_POST

    try {
        $this->jsonSentBy(fn () => glsr(ListTableController::class)->overrideInlineSaveAjax());
    } finally {
        $this->tearDownAjax();
        $_POST = [];
        unset($_REQUEST['_inline_edit']);
    }

    // Read past the plugin's in-memory review cache, which still holds the pre-save copy.
    expect(glsr(ReviewManager::class)->get($review->ID, true)->response)
        ->toBe('Thank you for the kind words.');
});

/*
 * The heartbeat lock check.
 */

test('the heartbeat check ignores a review nobody is editing', function () {
    // heartbeat_received is asked, every fifteen seconds, who is editing what. A review with no
    // lock on it is not somebody else's to warn about, so nothing is added to the response.
    $review = createReview();
    $data = ['wp-check-locked-posts' => ['post-'.$review->ID]];

    $response = glsr(ListTableController::class)->filterCheckLockedReviews(['other' => 'kept'], $data);

    expect($response)->toBe(['other' => 'kept']); // no lock, nothing added
});
