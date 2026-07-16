<?php

use GeminiLabs\SiteReviews\Controllers\TaxonomyController;

use function GeminiLabs\SiteReviews\Tests\createTerm;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Review categories, and the priority that orders them.
 *
 * A review category is an ordinary WordPress term with one thing bolted on: a `term_priority` meta
 * letting a site owner choose the order. WordPress sorts terms by name; a site with categories
 * "Excellent", "Good" and "Awful" does not want that order.
 *
 * The ordering rewrites the ORDER BY of the terms query — a filter that fires for EVERY term query
 * on the site (every menu, tag cloud, category widget belonging to anyone), so its four guards each
 * matter. And whether ANY term has a priority is cached in a transient, or the plugin would COUNT
 * termmeta on every page to learn the answer is no — so the cache is dropped whenever a priority is
 * added, changed or removed (including the meta being deleted by someone else's code).
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
});

afterEach(function () {
    set_current_screen('front');
    delete_transient(glsr()->prefix.'term_priority');
});

function taxonomyController(): TaxonomyController
{
    return glsr(TaxonomyController::class);
}

function reviewCategory(array $args = []): int
{
    return createTerm(wp_parse_args($args, ['taxonomy' => glsr()->taxonomy]));
}

/**
 * The clauses WP_Term_Query hands to the `terms_clauses` filter.
 */
function termClauses(): array
{
    return [
        'fields' => 't.*, tt.*',
        'join' => 'INNER JOIN wp_term_taxonomy AS tt ON t.term_id = tt.term_id',
        'orderby' => 'ORDER BY t.name',
        'order' => 'ASC',
        'where' => "tt.taxonomy IN ('site-review-category')",
        'limits' => '',
    ];
}

/*
 * The columns on the categories screen.
 */

test('the categories screen gains a priority column, and the ids behind it', function () {
    $columns = taxonomyController()->filterColumns(['name' => 'Name', 'posts' => 'Count']);

    expect(array_keys($columns))->toBe(['name', 'posts', 'term_priority', 'term_id', 'term_taxonomy_id']);
});

test('a site that does not want term priority does not get the columns', function () {
    add_filter('site-reviews/taxonomy/disable_term_priority', '__return_true');

    expect(taxonomyController()->filterColumns(['name' => 'Name']))->toBe(['name' => 'Name']);
});

test('the columns show the priority, and the two ids people need when something goes wrong', function () {
    // term_id and term_taxonomy_id are different numbers and confusing them is a classic
    // WordPress bug — the plugin shows both, because a support thread that says "category 172"
    // is ambiguous until you know which one they read.
    $termId = reviewCategory();
    update_term_meta($termId, 'term_priority', 7);
    $term = get_term($termId, glsr()->taxonomy);

    expect(taxonomyController()->filterColumnValue('', 'term_priority', $termId))->toBe('7')
        ->and(taxonomyController()->filterColumnValue('', 'term_id', $termId))->toBe((string) $termId)
        ->and(taxonomyController()->filterColumnValue('', 'term_taxonomy_id', $termId))->toBe((string) $term->term_taxonomy_id);

    // and a column belonging to somebody else is handed back untouched
    expect(taxonomyController()->filterColumnValue('a value', 'posts', $termId))->toBe('a value');
});

test('a category with no priority shows zero, not an empty cell', function () {
    expect(taxonomyController()->filterColumnValue('', 'term_priority', reviewCategory()))->toBe('0');
});

test('the id columns are hidden by default, and only on our own screen', function () {
    // They are there for a support thread, not for everyday use.
    $ours = WP_Screen::get('edit-'.glsr()->taxonomy);
    $theirs = WP_Screen::get('edit-category');

    expect(taxonomyController()->filterDefaultHiddenColumns([], $ours))
        ->toBe(['term_id', 'term_taxonomy_id']);
    expect(taxonomyController()->filterDefaultHiddenColumns(['slug'], $theirs))
        ->toBe(['slug']);
});

test('the row actions carry the term id', function () {
    $term = get_term(reviewCategory(), glsr()->taxonomy);

    $actions = taxonomyController()->filterRowActions(['edit' => '<a>Edit</a>'], $term);

    expect(array_key_first($actions))->toBe('id')
        ->and($actions['id'])->toContain((string) $term->term_id)
        ->and($actions['edit'])->toBe('<a>Edit</a>');
});

/*
 * Saving a priority.
 */

test('a priority given when the category is created is saved', function () {
    $termId = reviewCategory();

    taxonomyController()->onTermCreated($termId, 0, ['term_priority' => 5]);

    expect((int) get_term_meta($termId, 'term_priority', true))->toBe(5);
});

test('a priority of zero is not stored at all', function () {
    // Zero is the default, and a termmeta row per category saying "no priority" is a row that
    // makes termPriorityExists() answer yes when the honest answer is no — which would turn on
    // the ORDER BY rewrite for every term query on the site, for nothing.
    $termId = reviewCategory();

    taxonomyController()->onTermCreated($termId, 0, ['term_priority' => 0]);

    expect(get_term_meta($termId, 'term_priority', true))->toBe('');
});

test('editing a category to a priority of zero takes the priority away', function () {
    $termId = reviewCategory();
    update_term_meta($termId, 'term_priority', 5);

    taxonomyController()->onTermUpdated($termId, 0, ['term_priority' => 0]);

    expect(get_term_meta($termId, 'term_priority', true))->toBe('');
});

test('editing a category to a new priority updates it', function () {
    $termId = reviewCategory();
    update_term_meta($termId, 'term_priority', 5);

    taxonomyController()->onTermUpdated($termId, 0, ['term_priority' => 9]);

    expect((int) get_term_meta($termId, 'term_priority', true))->toBe(9);
});

/*
 * The cache. This is the part that goes quietly wrong.
 */

test('whether any category has a priority is remembered, and forgotten when one changes', function () {
    // Asserted through the ORDERING rather than the transient's value, because the value is
    // the point of neither: what matters is that the answer is remembered (no COUNT against
    // termmeta on every page of the site) and that it is forgotten the moment it stops being
    // true (or the site owner sets a priority and nothing happens until tomorrow).
    $transient = glsr()->prefix.'term_priority';
    $reordered = fn () => taxonomyController()->filterTermsClauses(termClauses(), [glsr()->taxonomy], []) !== termClauses();
    $termId = reviewCategory();

    // Nothing has a priority: no reordering, and the answer is cached.
    expect($reordered())->toBeFalse()
        ->and(get_transient($transient))->not->toBeFalse();

    // Somebody gives a category one. The cached "no" has to go with it.
    taxonomyController()->onTermUpdated($termId, 0, ['term_priority' => 5]);
    expect(get_transient($transient))->toBeFalse();
    expect($reordered())->toBeTrue()                            // and now it reorders
        ->and(get_transient($transient))->not->toBeFalse();     // and remembers that too

    // And when the last priority goes, so does the cache — onTermUpdated writes the change through
    // delete_term_meta, and the flush is hooked to the meta write (deleted_term_meta), not to this
    // method, so it fires whoever removes the meta.
    taxonomyController()->onTermUpdated($termId, 0, ['term_priority' => 0]);
    expect(get_transient($transient))->toBeFalse();
    expect($reordered())->toBeFalse();
});

/*
 * The ordering itself. This filter fires for EVERY term query on the site.
 */

test('review categories are ordered by priority, highest first', function () {
    // Set through the controller, which is the path the categories screen takes: onTermUpdated
    // writes the meta, the meta write flushes the "no category has a priority" cache, and the
    // ordering turns on. (Setting the meta directly does the same now — see the test below.)
    $termId = reviewCategory();
    taxonomyController()->onTermUpdated($termId, 0, ['term_priority' => 5]);

    $clauses = taxonomyController()->filterTermsClauses(termClauses(), [glsr()->taxonomy], []);

    expect($clauses['orderby'])->toContain('tm.meta_value+0 DESC')
        ->and($clauses['orderby'])->toContain('t.name') // and ties still fall back to the name
        ->and($clauses['join'])->toContain('LEFT JOIN')
        ->and($clauses['join'])->toContain('term_priority');
    // LEFT, so that a category with no priority at all is still returned
    expect($clauses['where'])->toContain('tm.term_id IS NULL');
});

test('nobody else\'s terms are reordered', function () {
    // terms_clauses fires for every menu, tag cloud and category widget on the site. Four
    // guards, and each one is a way this could reach somebody else's query.
    $termId = reviewCategory();
    update_term_meta($termId, 'term_priority', 5);
    $unchanged = termClauses();

    // …a different taxonomy
    expect(taxonomyController()->filterTermsClauses($unchanged, ['category'], []))->toBe($unchanged);

    // …a query spanning ours AND somebody else's: not ours to reorder
    expect(taxonomyController()->filterTermsClauses($unchanged, [glsr()->taxonomy, 'category'], []))->toBe($unchanged);

    // …in the admin, where the categories screen sorts by its own columns
    set_current_screen('edit-'.glsr()->taxonomy);
    expect(taxonomyController()->filterTermsClauses($unchanged, [glsr()->taxonomy], []))->toBe($unchanged);
    set_current_screen('front');

    // …and on a site that has switched the feature off
    add_filter('site-reviews/taxonomy/disable_term_priority', '__return_true');
    expect(taxonomyController()->filterTermsClauses($unchanged, [glsr()->taxonomy], []))->toBe($unchanged);
});

test('a priority set outside the categories screen still turns the ordering on', function () {
    // WP-CLI, an importer, a site migration, another plugin. The meta gets written and the
    // plugin's own save path never runs.
    //
    // This used to do nothing at all. Whether ANY category has a priority is cached — otherwise
    // the front end would COUNT the termmeta table on every page to be told "no" — and the
    // cache was dropped on `deleted_term_meta` but on neither `added_term_meta` nor
    // `updated_term_meta`. So the cached "no category has a priority" survived, and since the
    // transient is set with no expiration, it survived FOREVER: the site owner set their
    // priorities and the ordering silently never happened.
    reviewCategory();
    $unordered = taxonomyController()->filterTermsClauses(termClauses(), [glsr()->taxonomy], []);
    expect($unordered)->toBe(termClauses()); // nothing has a priority, and that answer is cached

    update_term_meta(reviewCategory(), 'term_priority', 5); // not through the plugin

    $clauses = taxonomyController()->filterTermsClauses(termClauses(), [glsr()->taxonomy], []);

    expect($clauses['orderby'])->toContain('tm.meta_value+0 DESC');
});

test('somebody else\'s term_priority does not flush our cache', function () {
    // `added_term_meta` fires for every taxonomy. A plugin with a `term_priority` of its own on
    // its own terms must not make us re-COUNT on every page.
    reviewCategory();
    taxonomyController()->filterTermsClauses(termClauses(), [glsr()->taxonomy], []); // caches
    expect(get_transient(glsr()->prefix.'term_priority'))->not->toBeFalse();

    $theirs = createTerm(['taxonomy' => 'category']);
    update_term_meta($theirs, 'term_priority', 5);

    expect(get_transient(glsr()->prefix.'term_priority'))->not->toBeFalse();
});

test('a site where no category has a priority pays nothing for the feature', function () {
    // The common case, and the reason the transient exists: no priorities means no join, no
    // rewritten ORDER BY, and no cost on any page of the site.
    reviewCategory();
    $unchanged = termClauses();

    expect(taxonomyController()->filterTermsClauses($unchanged, [glsr()->taxonomy], []))->toBe($unchanged);
});

/*
 * Switching the feature off. `disable_term_priority` is the one filter that turns all of this off,
 * and every write path checks it so that a site that does not want ordered categories is not made
 * to store priority meta it will never read.
 */

test('with term priority switched off, a created category stores no priority', function () {
    add_filter('site-reviews/taxonomy/disable_term_priority', '__return_true');
    $termId = reviewCategory();

    taxonomyController()->onTermCreated($termId, 0, ['term_priority' => 5]);

    expect(get_term_meta($termId, 'term_priority', true))->toBe(''); // the early return: nothing written
});

test('with term priority switched off, editing a category leaves its priority alone', function () {
    add_filter('site-reviews/taxonomy/disable_term_priority', '__return_true');
    $termId = reviewCategory();
    update_term_meta($termId, 'term_priority', 5);

    taxonomyController()->onTermUpdated($termId, 0, ['term_priority' => 9]);

    expect((int) get_term_meta($termId, 'term_priority', true))->toBe(5); // untouched
});

/*
 * The three places the priority field is drawn.
 */

test('the add-category screen gets a priority field', function () {
    ob_start();
    taxonomyController()->renderAddFields();

    expect((string) ob_get_clean())->toContain('term_priority');
});

test('the edit-category screen gets a priority field carrying the current value', function () {
    $termId = reviewCategory();
    update_term_meta($termId, 'term_priority', 7);
    $term = get_term($termId, glsr()->taxonomy);

    ob_start();
    taxonomyController()->renderEditFields($term);
    $html = (string) ob_get_clean();

    expect($html)->toContain('term_priority')
        ->and($html)->toContain('value="7"'); // pre-filled with the category's current priority
});

test('the quick-edit box gets a priority field, and only for our own column', function () {
    // quick_edit_custom_box fires for every column of every taxonomy on the site; the guard draws
    // the field only for our priority column, on our own taxonomy.
    ob_start();
    taxonomyController()->renderQuickEditFields('term_priority', 'edit-tags', glsr()->taxonomy);
    $ours = (string) ob_get_clean();

    ob_start();
    taxonomyController()->renderQuickEditFields('name', 'edit-tags', glsr()->taxonomy); // not our column
    $theirs = (string) ob_get_clean();

    expect($ours)->toContain('term_priority')
        ->and($theirs)->toBe('');
});
