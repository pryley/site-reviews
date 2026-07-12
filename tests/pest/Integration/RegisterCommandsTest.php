<?php

use GeminiLabs\SiteReviews\Commands\RegisterPostMeta;
use GeminiLabs\SiteReviews\Commands\RegisterPostType;
use GeminiLabs\SiteReviews\Commands\RegisterShortcodes;
use GeminiLabs\SiteReviews\Commands\RegisterTaxonomy;
use GeminiLabs\SiteReviews\Commands\RegisterTinymcePopups;
use GeminiLabs\SiteReviews\Commands\RegisterWidgets;
use GeminiLabs\SiteReviews\Database\CountManager;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The registration commands: the review post type, its category taxonomy, the
 * shortcodes, the widgets, the post meta and the TinyMCE popups.
 *
 * All six run on init and have therefore ALREADY RUN by the time any test starts —
 * which is exactly why they are worth calling explicitly. Coverage is not collected
 * during bootstrap.php, so the most-executed code in the plugin reads as 0%, and
 * only an in-test call counts. Re-running them is harmless: register_post_type(),
 * register_taxonomy(), add_shortcode() and register_post_meta() all overwrite.
 *
 * What is asserted is the CONTRACT rather than the call — the capabilities the post
 * type is mapped to, the REST controller it is served by, the columns the list table
 * gets. Those are the things an addon or a site would break against.
 */

beforeEach(fn () => resetPluginState());

test('the review post type is registered, and locked down', function () {
    (new RegisterPostType())->handle();

    $postType = get_post_type_object(glsr()->post_type);
    expect($postType)->not->toBeNull();

    // A review is not a page: it must never be public, never be searchable, and
    // never have an archive. Turning any of these on would put reviews on the front
    // end as posts in their own right.
    expect($postType->public)->toBeFalse()
        ->and($postType->exclude_from_search)->toBeTrue()
        ->and($postType->has_archive)->toBeFalse();

    // but it IS in the admin, and in the REST API through the plugin's own controller
    expect($postType->show_ui)->toBeTrue()
        ->and($postType->show_in_rest)->toBeTrue()
        ->and($postType->rest_namespace)->toBe(glsr()->id.'/v1');

    // Its capabilities are its own, so that "can edit posts" never means "can edit
    // reviews" — the plugin grants those separately (see Role).
    expect($postType->cap->edit_posts)->toBe('edit_'.glsr()->post_type.'s')
        ->and($postType->cap->create_posts)->toBe('create_'.glsr()->post_type.'s')
        ->and($postType->map_meta_cap)->toBeTrue();

    expect(post_type_supports(glsr()->post_type, 'title'))->toBeTrue();
    expect(post_type_supports(glsr()->post_type, 'editor'))->toBeTrue();
    expect(post_type_supports(glsr()->post_type, 'revisions'))->toBeTrue();
    expect(post_type_supports(glsr()->post_type, 'comments'))->toBeFalse(); // reviews are not commentable
});

test('the list table columns are worked out at registration', function () {
    // setColumns() rewrites the column list before it is stored, and the storage is
    // process-wide — it was filled during the boot, and wp_parse_args() means the
    // stored copy WINS over a fresh one. So it has to be dropped for the command to
    // have anything to do.
    $columns = glsr()->retrieveAs('array', 'columns', []);
    $hidden = glsr()->retrieveAs('array', 'columns_hidden', []);
    glsr()->discard('columns');
    glsr()->discard('columns_hidden');

    (new RegisterPostType())->handle();

    $registered = glsr()->retrieveAs('array', 'columns')[glsr()->post_type];

    // "category" is not a column WordPress knows about — the taxonomy is.
    expect($registered)->not->toHaveKey('category')
        ->and($registered)->toHaveKey('taxonomy-'.glsr()->taxonomy);

    // the two flag columns are icons, not words
    expect($registered['is_pinned'])->toContain('pinned-icon')
        ->and($registered['is_verified'])->toContain('verified-icon');

    // and the hidden-by-default columns are the noisy ones
    expect(glsr()->retrieveAs('array', 'columns_hidden')[glsr()->post_type])
        ->toContain('ip_address')
        ->toContain('author_email')
        ->toContain('response');

    glsr()->store('columns', $columns); // put the boot's copy back
    glsr()->store('columns_hidden', $hidden);
});

test('the review category taxonomy is registered against the review post type', function () {
    (new RegisterTaxonomy())->handle();

    expect(taxonomy_exists(glsr()->taxonomy))->toBeTrue();
    expect(get_object_taxonomies(glsr()->post_type))->toContain(glsr()->taxonomy);
});

test('the four shortcodes are registered', function () {
    (new RegisterShortcodes())->handle();

    expect(shortcode_exists('site_reviews'))->toBeTrue()
        ->and(shortcode_exists('site_review'))->toBeTrue()
        ->and(shortcode_exists('site_reviews_form'))->toBeTrue()
        ->and(shortcode_exists('site_reviews_summary'))->toBeTrue();

    // and each is resolvable by its tag, which is how the option manager, the REST
    // API and the TinyMCE popups all find one. NOT via glsr('site_reviews'): the
    // alias register() sets holds a CLOSURE as a container instance, and the
    // container hands the closure straight back rather than calling it.
    // Application::shortcode() reads the tag => class map that register() appends to,
    // and that is the accessor the plugin itself uses.
    expect(glsr()->shortcode('site_reviews'))->toBeInstanceOf(
        GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode::class
    );
    expect(glsr()->shortcode('site_reviews_summary'))->toBeInstanceOf(
        GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode::class
    );
    expect(glsr()->shortcode('not_a_shortcode'))->toBeNull();
});

test('the four widgets are registered', function () {
    global $wp_widget_factory;

    (new RegisterWidgets())->handle();

    $widgets = array_keys($wp_widget_factory->widgets);
    expect($widgets)->toContain(GeminiLabs\SiteReviews\Widgets\SiteReviewsWidget::class)
        ->toContain(GeminiLabs\SiteReviews\Widgets\SiteReviewWidget::class)
        ->toContain(GeminiLabs\SiteReviews\Widgets\SiteReviewsFormWidget::class)
        ->toContain(GeminiLabs\SiteReviews\Widgets\SiteReviewsSummaryWidget::class);
});

test('the rating meta is exposed to the rest api for every public post type', function () {
    // The counts are what a theme or a block reads to show "4.5 stars (12 reviews)"
    // on a page. They are meta on the ASSIGNED page, not on the review, so they have
    // to be registered against the post types a review can be assigned to.
    (new RegisterPostMeta())->handle();

    foreach (['page', 'post'] as $type) {
        $keys = get_registered_meta_keys('post', $type);
        expect($keys)->toHaveKey(CountManager::META_AVERAGE)
            ->and($keys)->toHaveKey(CountManager::META_RANKING)
            ->and($keys)->toHaveKey(CountManager::META_REVIEWS);
        expect($keys[CountManager::META_REVIEWS]['show_in_rest'])->toBeTrue();
        expect($keys[CountManager::META_REVIEWS]['single'])->toBeTrue();
    }
});

test('the tinymce popups are registered, one per shortcode', function () {
    // The classic editor's "Add Shortcode" button builds its dialogs from these.
    glsr()->discard('mce');

    (new RegisterTinymcePopups())->handle();

    $popups = glsr()->retrieveAs('array', 'mce');
    expect(array_keys($popups))->toContain('site_reviews')
        ->toContain('site_review')
        ->toContain('site_reviews_form')
        ->toContain('site_reviews_summary');

    // and each carries the fields the dialog is built from
    expect($popups['site_reviews'])->toHaveKey('fields')
        ->and($popups['site_reviews']['fields'])->not->toBeEmpty();
});
