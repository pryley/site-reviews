<?php

use GeminiLabs\SiteReviews\Controllers\MainController;
use GeminiLabs\SiteReviews\Database\CountManager;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Console;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The boot sequence: what must happen before anything else can.
 *
 * Almost nothing here has a visible effect: it registers the post type, taxonomy, shortcodes and
 * widgets, loads translations, and merges any new settings an update introduced. Each is silent
 * working or not — an unregistered shortcode prints its own name, an unmerged setting reads empty
 * forever.
 *
 * The ORDER is the easy-to-miss part:
 *
 *   after_setup_theme   translations, because the settings config is built from translated strings
 *                       and would otherwise be built in English and cached that way.
 *   init:5              defaults merged and review types registered, before anything reads them.
 *   init                the post type, taxonomy, shortcodes and post meta.
 *
 * The multisite paths (installOnNewSite, filterDropTables) run when a site is created or deleted in
 * a network — the one moment a plugin sets itself up on someone's behalf.
 */

beforeEach(function () {
    resetPluginState();
});

// `review_types` needs no cleanup here: the Application's whole storage is snapshotted after boot
// and restored after every test (see snapshotStorage() in Support/helpers.php).

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
    // site-reviews/migration/end. clean() drops keys the config no longer knows, so a migration's
    // leavings are not written back on every save for ever. Its guard reads glsr()->settings —
    // empty() on an inaccessible property consults __isset (never __get), so the Plugin trait
    // must answer it: without __isset the guard was permanently false and this branch never ran.
    $settings = get_option(OptionManager::databaseKey());
    $settings['settings']['general']['a_stale_key'] = 'left behind by a migration';
    update_option(OptionManager::databaseKey(), $settings);
    glsr()->settings(); // built, as init:5 leaves it in production

    glsr(MainController::class)->onMigrationEnd();

    $cleaned = get_option(OptionManager::databaseKey());
    expect($cleaned['settings']['general'])->toBeArray()
        ->not->toHaveKey('a_stale_key');
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
    // carry four widgets it will never use in the block editor's legacy-widget list. The observable
    // is the widget factory itself — has_action('widgets_init') is truthy from the boot snapshot
    // whatever this method does.
    global $wp_widget_factory;
    $original = $wp_widget_factory->widgets;
    try {
        // The fusion-builder stub passes Avada's version gate (tests/bin/stubs-manifest.php), and
        // an awake Avada refuses the legacy widgets outside its own editor. That is shipped
        // behaviour, but it is not the machinery under test here — force the filter true so the
        // registration itself is observable.
        add_filter('site-reviews/register/widgets', '__return_true', PHP_INT_MAX);
        $wp_widget_factory->widgets = [];
        glsr(MainController::class)->registerWidgets();
        $registered = array_keys($wp_widget_factory->widgets);
        sort($registered); // DirectoryIterator order is the filesystem's business
        expect($registered)->toBe([
            GeminiLabs\SiteReviews\Widgets\SiteReviewWidget::class,
            GeminiLabs\SiteReviews\Widgets\SiteReviewsFormWidget::class,
            GeminiLabs\SiteReviews\Widgets\SiteReviewsSummaryWidget::class,
            GeminiLabs\SiteReviews\Widgets\SiteReviewsWidget::class,
        ]);

        remove_filter('site-reviews/register/widgets', '__return_true', PHP_INT_MAX);
        $wp_widget_factory->widgets = [];
        add_filter('site-reviews/register/widgets', '__return_false');
        glsr(MainController::class)->registerWidgets();
        expect($wp_widget_factory->widgets)->toBe([]); // refused means none registered
    } finally {
        $wp_widget_factory->widgets = $original;
    }
});

/*
 * Multisite: deleting a site in a network.
 */

test('the plugin\'s tables are dropped BEFORE the tables they point at', function () {
    // wpmu_drop_tables, at priority 999, when somebody deletes a site from a network. The plugin's
    // tables carry foreign keys into wp_posts — so if WordPress dropped its own tables first, the
    // constraint would refuse and the site would be left half-deleted.
    //
    // Arr::prepend, not append. The order of this array IS the order of the DROPs — each of the
    // six is prepended in registry order, so they come out reversed, all before WordPress's own.
    global $wpdb;
    $tables = glsr(MainController::class)->filterDropTables(['wp_1_posts']);

    expect(array_values($tables))->toBe([
        "{$wpdb->prefix}glsr_tmp",
        "{$wpdb->prefix}glsr_stats",
        "{$wpdb->prefix}glsr_ratings",
        "{$wpdb->prefix}glsr_assigned_users",
        "{$wpdb->prefix}glsr_assigned_terms",
        "{$wpdb->prefix}glsr_assigned_posts",
        'wp_1_posts',
    ]);
});

/*
 * The console.
 */

test('the log is written once per page, and not on the update screen', function () {
    // logOnce() is hooked to both wp_footer and admin_footer. update.php is excluded because
    // WordPress is streaming output to the browser there, and writing to the log mid-stream has
    // put a PHP notice in the middle of a plugin update before now.
    glsr_log()->once('error', 'm5-probe', 'a recurring failure');

    glsr(MainController::class)->logOnce('update.php');
    expect(glsr(Console::class)->get())->not->toContain('a recurring failure'); // survived, unwritten

    glsr(MainController::class)->logOnce();
    expect(glsr(Console::class)->get())->toContain('a recurring failure')
        ->and(glsr()->retrieveAs('array', Console::LOG_ONCE_KEY))->toBe([]); // written, and flushed
});

/*
 * The registrars.
 *
 * These run once, on `init`, during boot — which is BEFORE the suite starts collecting coverage, so
 * although the post type, taxonomy and shortcodes are plainly there, the methods that registered
 * them read as never called. Calling each here re-runs it (register_* is idempotent — WordPress
 * overwrites the existing registration) and pins that its effect is in place.
 */

test('the review post type is registered', function () {
    glsr(MainController::class)->registerPostType();

    expect(post_type_exists(glsr()->post_type))->toBeTrue();
});

test('the review-category taxonomy is registered', function () {
    glsr(MainController::class)->registerTaxonomy();

    expect(taxonomy_exists(glsr()->taxonomy))->toBeTrue();
});

test('the shortcodes are registered', function () {
    glsr(MainController::class)->registerShortcodes();

    expect(shortcode_exists('site_reviews'))->toBeTrue();
});

test('the rating count meta is registered on every post type a review can be shown on', function () {
    // register_post_meta so the counts (average, ranking, total) are readable through the REST API
    // and get_post_meta on posts, pages and any public custom type.
    glsr(MainController::class)->registerPostMeta();

    expect(registered_meta_key_exists('post', CountManager::META_AVERAGE, 'post'))->toBeTrue();
});

/*
 * The rest of the boot sequence.
 */

test('registering addons fires the hooks an addon listens on to announce itself', function () {
    // The two doors an addon comes in through — the compat shim and the premium registry. A missing
    // do_action here is an installed addon that never gets registered and silently does nothing.
    $announced = [];
    add_action('site-reviews/addon/register', function () use (&$announced) {
        $announced[] = 'addon';
    });
    add_action('site-reviews/premium/register', function () use (&$announced) {
        $announced[] = 'premium';
    });

    glsr(MainController::class)->registerAddons();

    expect($announced)->toBe(['addon', 'premium']);
});

test('registering the languages loads this plugin\'s text domain without error', function () {
    // load_plugin_textdomain, built from the plugin's own path and languages dir. Its effect is not
    // observable offline — the suite ships no .mo, so nothing is actually loaded and neither the
    // load_textdomain action nor is_textdomain_loaded() report anything. What is pinned is that the
    // path it builds is well-formed and the call runs clean rather than warning (failOnWarning).
    expect(fn () => glsr(MainController::class)->registerLanguages())
        ->not->toThrow(\Throwable::class);
});

/*
 * Multisite: creating a site in a network.
 */

test('a newly created network site is set up only when the plugin is network-active', function () {
    // wp_initialize_site, at priority 999. On a single-site install is_plugin_active_for_network is
    // false, so nothing runs — the guard that stops the plugin installing its tables on a site that
    // is never going to load it.
    require_once ABSPATH.'wp-admin/includes/plugin.php'; // where is_plugin_active_for_network lives
    require_once ABSPATH.'wp-includes/class-wp-site.php'; // not autoloaded on a single-site install
    $site = new WP_Site((object) ['blog_id' => '1', 'domain' => 'localhost', 'path' => '/']);

    expect(fn () => glsr(MainController::class)->installOnNewSite($site))
        ->not->toThrow(\Throwable::class);
});
