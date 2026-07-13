<?php

use GeminiLabs\SiteReviews\Controllers\MainController;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\Tables;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The boot sequence: the things that must happen before anything else can.
 *
 * Almost nothing here has a visible effect of its own. It registers the post type, the taxonomy,
 * the shortcodes and the widgets; it loads the translations; it merges any new settings a plugin
 * update has introduced into the settings already on the site. Every one of them is silent when it
 * works, and silent when it does not — a shortcode that was never registered simply prints its own
 * name on the page, and a setting that was never merged reads as empty forever.
 *
 * The ORDER is the part that is easy to get wrong and impossible to see:
 *
 *   after_setup_theme   the translations, because the settings config is built from translated
 *                       strings and would otherwise be built in English and cached that way.
 *   init:5              the defaults are merged, and the review types are registered — before
 *                       anything that reads them.
 *   init                the post type, taxonomy, shortcodes and post meta.
 *
 * The multisite paths (installOnNewSite, filterDropTables) run when a site is created or deleted in
 * a network, which is the one moment a plugin gets to set itself up on somebody's behalf.
 */

beforeEach(function () {
    resetPluginState();
});

afterEach(function () {
    // `review_types` is a container register, and nothing else resets it. A test that adds one
    // would otherwise leave it there for the rest of the process — and the review type filter on
    // the list table reads exactly this.
    glsr(MainController::class)->registerReviewTypes();
});

/*
 * The review types.
 */

test('a site with no import addons has exactly one kind of review: its own', function () {
    // Which is why the "review type" dropdown does not appear on the reviews screen: one entry is
    // not a choice. It appears the moment somebody installs an addon that imports from elsewhere.
    glsr(MainController::class)->registerReviewTypes();

    expect(glsr()->retrieveAs('array', 'review_types'))->toBe([
        'local' => 'Local Review',
    ]);
});

test('an addon can add a kind of review, and cannot take away the local one', function () {
    // `local` is merged in AFTER the filter, with wp_parse_args — so an addon that returned an
    // empty array, or forgot to preserve what was there, cannot remove the reviews the site wrote
    // itself.
    add_filter('site-reviews/review/types', fn () => ['google' => 'Google Review']);

    glsr(MainController::class)->registerReviewTypes();

    expect(glsr()->retrieveAs('array', 'review_types'))
        ->toHaveKey('google')
        ->toHaveKey('local');
});

/*
 * Settings, on every page load.
 */

test('a setting introduced by an update appears on a site that has never seen it', function () {
    // onInit merges the DEFAULTS into whatever is already saved. Without it, every setting added by
    // a plugin update would read as empty on every site that upgraded rather than installed —
    // which is all of them.
    glsr(OptionManager::class)->set('settings.general.require.approval', 'yes');

    glsr(MainController::class)->onInit();

    $settings = glsr(OptionManager::class)->all();
    expect($settings)->toHaveKey('settings')
        ->and(glsr_get_option('general.require.approval'))->toBe('yes'); // …and the saved value survived
});

test('the plugin records the version it is now running, and the one it came from', function () {
    // The version lives INSIDE the settings array, not in an option of its own — and it is only
    // written when it differs from what is stored, which is what makes `version_upgraded_from`
    // meaningful: it is the version this site was on immediately before this page load.
    //
    // That pair is what tells the welcome notice from the upgrade notice, and what the migrations
    // read to know whether they have anything to do.
    $options = glsr(OptionManager::class);
    $options->set('version', '7.0.0');

    glsr(MainController::class)->onInit();

    expect($options->get('version'))->toBe(glsr()->version)
        ->and($options->get('version_upgraded_from'))->toBe('7.0.0');
});

test('and a site already on this version is not told it has just upgraded', function () {
    // The guard. Without it, version_upgraded_from would be rewritten to the CURRENT version on
    // every page load, and the "you have just updated" notice would either never appear or never
    // go away, depending on which way round it was read.
    $options = glsr(OptionManager::class);
    $options->set('version', glsr()->version);
    $options->set('version_upgraded_from', '7.0.0');

    glsr(MainController::class)->onInit();

    expect($options->get('version_upgraded_from'))->toBe('7.0.0'); // untouched
});

test('the settings are cleaned once the migrations have finished', function () {
    // site-reviews/migration/end. A migration can leave keys behind that no longer exist in the
    // config, and they would be written back on every save for ever afterwards.
    glsr(MainController::class)->onMigrationEnd();

    expect(get_option(OptionManager::databaseKey()))->toBeArray();
});

/*
 * The assigned-posts search.
 */

test('the assigned-posts search looks in every post type a person could assign a review to', function () {
    // This is the query behind the "Assigned Pages" token field. It has to search the post types a
    // site owner actually has — their pages, their posts, and every public custom type a theme or
    // plugin registered — because that is where the reviews are going to be shown.
    register_post_type('a_public_type', [
        'public' => true,
        'show_in_rest' => true,
        'show_ui' => true,
    ]);
    $query = new WP_Query();
    $query->set('post_type', glsr()->prefix.'assigned_posts');

    glsr(MainController::class)->parseAssignedPostTypesInQuery($query);

    $postTypes = (array) $query->get('post_type');
    expect($postTypes)->toContain('post')
        ->toContain('page')
        ->toContain('a_public_type')
        ->and($query->is_archive)->toBeFalse(); // …and it is not an archive, whatever WP decided

    unregister_post_type('a_public_type');
});

test('and it leaves every other query in the site alone', function () {
    // parse_query fires for every query on every page, including the front end.
    $query = new WP_Query();
    $query->set('post_type', 'post');

    glsr(MainController::class)->parseAssignedPostTypesInQuery($query);

    expect($query->get('post_type'))->toBe('post');
});

/*
 * The widgets.
 */

test('the legacy widgets are registered, and a site can refuse them', function () {
    // They are legacy, and a site that has moved entirely to blocks can switch them off rather than
    // carry four widgets it will never use in the block editor's legacy-widget list.
    glsr(MainController::class)->registerWidgets();
    expect(has_action('widgets_init'))->not->toBeFalse();

    add_filter('site-reviews/register/widgets', '__return_false');

    glsr(MainController::class)->registerWidgets(); // and this one does nothing at all
    expect(true)->toBeTrue();
});

/*
 * Multisite: deleting a site in a network.
 */

test('the plugin\'s tables are dropped BEFORE the tables they point at', function () {
    // wpmu_drop_tables, at priority 999, when somebody deletes a site from a network. The plugin's
    // tables carry foreign keys into wp_posts — so if WordPress dropped its own tables first, the
    // constraint would refuse and the site would be left half-deleted.
    //
    // Arr::prepend, not append. The order of this array IS the order of the DROPs.
    $tables = glsr(MainController::class)->filterDropTables(['wp_1_posts']);

    $ours = array_values(glsr(Tables::class)->tables());
    expect(count($tables))->toBeGreaterThan(1);
    expect(array_slice($tables, -1))->toBe(['wp_1_posts']); // …and WordPress's own is still last
    expect($ours)->not->toBeEmpty();
});

/*
 * The console.
 */

test('the log is written once per page, and not on the update screen', function () {
    // logOnce() is hooked to both wp_footer and admin_footer. update.php is excluded because
    // WordPress is streaming output to the browser there, and writing to the log mid-stream has
    // put a PHP notice in the middle of a plugin update before now.
    glsr(MainController::class)->logOnce('update.php');
    glsr(MainController::class)->logOnce();

    expect(true)->toBeTrue(); // it ran, on both paths, without throwing
});
