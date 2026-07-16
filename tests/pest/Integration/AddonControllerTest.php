<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\TestAddon\Application as TestAddon;
use GeminiLabs\SiteReviews\TestAddon\Controller as TestAddonController;
use GeminiLabs\SiteReviews\TestAddon\Hooks as TestAddonHooks;

use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Everything an addon inherits.
 *
 * Addons\Controller is abstract, and the six paid addons are thin subclasses — so this file IS the
 * test of their plumbing: assets, settings, views, capabilities, translations, activation.
 *
 * Tested against a real addon, not a mock (which would only prove it agreed with itself).
 * tests/pest/fixtures/site-reviews-test-addon is laid out exactly as a shipped addon — plugin file
 * with a header, plugin/, assets/, config/, views/, languages/ — and Plugin::__construct() finds its
 * way by the same reflection it uses on a real one: the addon's `file` is derived by taking its
 * Application class path and swapping `plugin/Application` for the addon's id. The layout is the
 * mechanism, not decoration.
 */

beforeEach(function () {
    resetPluginState();
    $this->addon = glsr(TestAddon::class);
    $this->controller = glsr(TestAddonController::class);
});

afterEach(function () {
    set_current_screen('front');
    unset($GLOBALS['post']);
    $GLOBALS['wp_scripts'] = null;
    $GLOBALS['wp_styles'] = null;
});

/*
 * The addon knows where it lives.
 */

test('an addon reads its version out of its own plugin header', function () {
    // get_file_data() against the file Plugin::__construct() worked out from the class. The
    // version is the one that matters: it is what cache-busts the addon's assets and what
    // the update check compares against.
    expect($this->addon->id)->toBe('site-reviews-test-addon')
        ->and($this->addon->version)->toBe('2.3.4')
        ->and($this->addon->languages)->toBe('/languages')
        ->and($this->addon->testedTo)->toBe('6.9');
});

test('the constants are readable as properties, and the name is one of them', function () {
    // Plugin::__get() falls back to the uppercased CONSTANT for any property the trait does
    // not declare. It declares $version, $testedTo and $languages — and NOT $name. So the
    // display name of an addon is its NAME constant, and the `Plugin Name` in its header is
    // never read. Worth knowing, because the two can drift apart and only one of them shows.
    expect($this->addon->name)->toBe('Test Addon')
        ->and($this->addon->slug)->toBe('test-addon')
        ->and($this->addon->post_type)->toBe('test-addon-thing')
        ->and($this->addon->licensed)->toBeTrue();
});

test('an addon path resolves inside the addon, not inside the plugin', function () {
    expect($this->addon->path('config/settings.php'))->toEndWith('site-reviews-test-addon/config/settings.php')
        ->and(file_exists($this->addon->path('config/settings.php')))->toBeTrue();
});

/*
 * Assets. An addon ships its own CSS and JS and hangs them off the plugin's, so that its
 * script cannot run before the thing it extends has loaded.
 */

test('an addon enqueues its public assets, and depends on the plugin\'s own', function () {
    $this->controller->enqueuePublicAssets();

    expect(wp_style_is('site-reviews-test-addon', 'enqueued'))->toBeTrue()
        ->and(wp_script_is('site-reviews-test-addon', 'enqueued'))->toBeTrue();

    $script = wp_scripts()->registered['site-reviews-test-addon'];
    expect($script->deps)->toBe(['site-reviews']) // it cannot run before the plugin has
        ->and($script->ver)->toBe('2.3.4') // cache-busted by the ADDON's version, not the plugin's
        ->and($script->src)->toContain('assets/site-reviews-test-addon.js');
});

test('an addon enqueues its admin assets only on a review admin page', function () {
    // Other people's admin pages are not the place for a review addon's stylesheet.
    //
    // isReviewAdminPage() asks two things: filter_input(INPUT_GET, 'post_type') — shadowed for
    // the Controllers namespace by Support/filter-input.php, so a test COULD plant $_GET — and
    // get_post_type(), which reads the global post. This test drives the second arm, the one
    // the review editor actually exercises: the post being edited IS a review.
    set_current_screen('dashboard');
    $this->controller->enqueueAdminAssets();
    expect(wp_style_is('site-reviews-test-addon/admin', 'enqueued'))->toBeFalse();

    set_current_screen('edit-'.glsr()->post_type);
    $GLOBALS['post'] = get_post(createReview()->ID);
    $this->controller->enqueueAdminAssets();

    expect(wp_style_is('site-reviews-test-addon/admin', 'enqueued'))->toBeTrue()
        ->and(wp_script_is('site-reviews-test-addon/admin', 'enqueued'))->toBeTrue();
    expect(wp_scripts()->registered['site-reviews-test-addon/admin']->deps)->toBe(['site-reviews/admin']);
});

test('an asset the addon does not ship is not enqueued', function () {
    // buildAssetArgs() checks the file is there first. An addon with no stylesheet of its
    // own must not enqueue a 404 — a missing asset is a console error on every page.
    $args = (fn () => $this->buildAssetArgs('css', ['suffix' => 'nonexistent']))
        ->call($this->controller);

    expect($args)->toBe([]);
});

/*
 * Settings, and the links that lead to them.
 */

test('an addon merges its settings into the plugin\'s', function () {
    $settings = $this->controller->filterSettings(['settings.general.style' => []]);

    expect($settings)->toHaveKey('settings.general.style') // the plugin's are still there
        ->and($settings)->toHaveKey('settings.addons.test-addon.enabled'); // and so are the addon's
});

test('the plugins screen offers a settings link, to anybody allowed to see it', function () {
    // On an ADMIN screen, which is the only place the question is asked: hasPermission() is
    // `!$isAdminScreen || can(…)`, so off one it always says yes. That is not laxity — the
    // capability check exists to decide what to draw in the admin, and there is nothing to
    // draw anywhere else.
    set_current_screen('plugins');
    wp_set_current_user(createUser(['role' => 'administrator']));
    $links = $this->controller->filterActionLinks(['deactivate' => '<a>Deactivate</a>']);

    expect($links['settings'])->toContain('addons')
        ->and($links['settings'])->toContain('test-addon')
        ->and($links['settings'])->toContain('Settings</a>')
        ->and(array_key_first($links))->toBe('settings'); // and it comes first

    // Somebody who cannot reach the settings page is not shown a link to it.
    wp_set_current_user(createUser(['role' => 'subscriber']));
    expect($this->controller->filterActionLinks([]))->toBe([]);
});

test('the plugins screen offers documentation and support, on the addon\'s own row', function () {
    wp_set_current_user(createUser(['role' => 'administrator']));

    $links = $this->controller->filterRowMeta(['1.0'], $this->addon->basename);
    expect($links)->toHaveCount(3)
        ->and($links['documentation'])->toContain('addon-site-reviews-test-addon') // expands its own section
        ->and($links['support'])->toContain('Support');

    // and NOT on anybody else's row
    expect($this->controller->filterRowMeta(['1.0'], 'akismet/akismet.php'))->toBe(['1.0']);
});

/*
 * Capabilities and roles. An addon with a post type of its own has to teach WordPress who
 * may edit it, or the answer is nobody — including the administrator.
 */

test('an addon with a post type adds the capabilities for it', function () {
    $capabilities = $this->controller->filterCapabilities([]);

    expect($capabilities)->toContain('edit_test-addon-things')
        ->and($capabilities)->toContain('delete_others_test-addon-things')
        ->and($capabilities)->toContain('read_private_test-addon-things');
});

test('an addon with a post type gives the roles the capabilities for it', function () {
    $roles = $this->controller->filterRoles([
        'administrator' => [],
        'author' => [],
        'contributor' => [],
        'editor' => [],
    ]);

    expect($roles['administrator'])->toContain('delete_others_test-addon-things')
        ->and($roles['editor'])->toContain('delete_others_test-addon-things')
        ->and($roles['author'])->not->toContain('delete_others_test-addon-things') // an author deletes their own
        ->and($roles['author'])->toContain('delete_test-addon-things')
        ->and($roles['contributor'])->toBe(['delete_test-addon-things', 'edit_test-addon-things']);
});

test('a role the site does not have is not invented', function () {
    // filterRoles is handed whatever roles exist. A site that has deleted its `author` role
    // must not have it put back by an addon.
    $roles = $this->controller->filterRoles(['administrator' => []]);

    expect(array_keys($roles))->toBe(['administrator']);
});

/*
 * Activation. This runs on admin_init, on EVERY admin request, so what it does the second
 * time matters as much as what it does the first.
 */

test('activating an addon happens once, and grants the roles their capabilities', function () {
    $option = glsr()->prefix.'activated_site-reviews-test-addon';
    delete_option($option);
    $activated = new ArrayObject();
    add_action('site-reviews-test-addon/activated', fn () => $activated->append(true));

    $this->controller->onActivation();

    expect(get_option($option))->toBeTruthy()
        ->and($activated)->toHaveCount(1)
        ->and(get_role('administrator')->has_cap('edit_test-addon-things'))->toBeTrue();

    // admin_init fires on every admin request, and the addon is not activated again on any
    // of them — the action is what an addon hangs its install routine on.
    $this->controller->onActivation();
    expect($activated)->toHaveCount(1);
});

test('deactivating an addon forgets it, so that it activates again if it comes back', function () {
    $option = glsr()->prefix.'activated_site-reviews-test-addon';
    update_option($option, true);
    $deactivated = new ArrayObject();
    add_action('site-reviews-test-addon/deactivated', fn () => $deactivated->append(true));

    $this->controller->onDeactivation($isNetworkDeactivation = false);

    expect(get_option($option))->toBeFalse()
        ->and($deactivated)->toHaveCount(1);
});

/*
 * Views and paths. An addon's view is looked up in the addon, and the plugin's style
 * setting decides which one.
 */

test('a view is looked for in the addon before the plugin', function () {
    // The `path` filter is how an addon's own files are found: the plugin asks for
    // `site-reviews-test-addon/views/documentation` and gets the addon's copy.
    $path = $this->controller->filterFilePaths(
        glsr()->path('views/documentation.php'),
        'site-reviews-test-addon/views/documentation.php'
    );

    expect($path)->toContain('site-reviews-test-addon/views/documentation.php')
        ->and($path)->not->toContain('/plugin/');

    // and a file that is not the addon's is left where it was
    $unchanged = glsr()->path('views/pages/tools.php');
    expect($this->controller->filterFilePaths($unchanged, 'views/pages/tools.php'))->toBe($unchanged);
});

test('an addon falls back to its default view when it has none for the site\'s style', function () {
    // A site using a CSS framework the addon has never shipped a view for gets the addon's
    // ordinary view, not a missing file.
    glsr(OptionManager::class)->set('settings.general.style', 'bootstrap_5');

    expect($this->controller->filterRenderView('views/documentation'))->toBe('views/documentation');
});

test('the addon documentation is added under the addon\'s own id', function () {
    $documentation = $this->controller->filterDocumentation([]);

    expect($documentation)->toHaveKey('site-reviews-test-addon')
        ->and($documentation['site-reviews-test-addon'])->toContain('Test addon documentation.');
});

/*
 * Translations. An addon has its own text domain, and its own .pot.
 */

test('the addon adds its text domain to the translator', function () {
    expect($this->controller->filterTranslatorDomains(['site-reviews']))
        ->toBe(['site-reviews', 'site-reviews-test-addon']);
});

test('the addon adds the strings from its own pot file', function () {
    $entries = $this->controller->filterTranslationEntries([]);

    expect($entries)->not->toBeEmpty();
});

test('the addon announces itself to the public javascript', function () {
    // The frontend script branches on which addons are present.
    expect($this->controller->filterLocalizedPublicVariables([]))
        ->toBe(['addons' => ['site-reviews-test-addon' => null]]);
});

/*
 * The wiring. Addon::init() finds the Hooks class by name, and every hook above is
 * registered from it — so a rename that misses one is a feature that silently stops
 * existing.
 */

test('an addon registers every one of its base hooks', function () {
    remove_all_filters('site-reviews/capabilities');
    remove_all_filters('site-reviews/settings');
    remove_all_actions('admin_enqueue_scripts');

    glsr(TestAddonHooks::class)->run();

    expect(has_filter('site-reviews/capabilities'))->not->toBeFalse()
        ->and(has_filter('site-reviews/settings'))->not->toBeFalse()
        ->and(has_action('admin_enqueue_scripts'))->not->toBeFalse()
        ->and(has_filter('gettext_site-reviews-test-addon'))->not->toBeFalse()
        ->and(has_filter('plugin_action_links_'.$this->addon->basename))->not->toBeFalse()
        ->and(has_action('deactivate_'.$this->addon->basename))->not->toBeFalse();
});

test('an addon wires itself up by finding its own hooks class', function () {
    // init() is the whole of an addon's bootstrap, and it finds the Hooks class BY NAME:
    // it takes the Application class and swaps its short name for "Hooks". There is no
    // registry and nothing is passed in, so an addon whose classes have been renamed
    // carelessly registers nothing at all and merely logs about it.
    remove_all_filters('site-reviews/settings');

    glsr(TestAddon::class)->init();

    expect(has_filter('site-reviews/settings'))->not->toBeFalse();
});

test('an addon finds, registers and runs each integration it ships', function () {
    // runIntegrations scans the addon's plugin/Integrations directory: for each integration it makes
    // the Hooks class a singleton and hangs it on plugins_loaded to run late. The test addon ships
    // two directories — Example, a real integration, and Broken, which has no Hooks class and must be
    // skipped with a log rather than fatal the addon.
    remove_all_filters('site-reviews-test-addon/example/loaded');
    remove_all_actions('plugins_loaded');

    glsr(TestAddonHooks::class)->runIntegrations();

    expect(has_action('plugins_loaded'))->not->toBeFalse(); // Example was hung to run late

    do_action('plugins_loaded'); // and when it fires, the integration's run() executes
    expect(has_filter('site-reviews-test-addon/example/loaded'))->not->toBeFalse();
});

/*
 * The translation filters. An addon gets its own gettext_{id} filters so a site owner can rename
 * its visitor-facing strings; with nothing customised, every one hands the string straight back.
 */

test('the addon translation filters return the string untouched when nothing overrides it', function () {
    $c = $this->controller;

    expect($c->filterGettext('A string the addon never customised', 'A string the addon never customised'))
        ->toBe('A string the addon never customised')
        ->and($c->filterGettextWithContext('Anonymous', 'Anonymous', 'a context'))
        ->toBe('Anonymous')
        ->and($c->filterNgettext('1 thing here', '1 thing here', '%s things here', 1))
        ->toBe('1 thing here')
        ->and($c->filterNgettextWithContext('1 thing here', '1 thing here', '%s things here', 1, 'a context'))
        ->toBe('1 thing here');
});

/*
 * Paths, the subsubsub links, and the no-op lifecycle hooks.
 */

test('the addon config path is normalized only when it points inside the addon', function () {
    $c = $this->controller;

    expect($c->filterConfigPath('site-reviews-test-addon/config/settings.php'))
        ->toBe('site-reviews-test-addon/config/settings.php') // carries the prefix, comes back carrying it
        ->and($c->filterConfigPath('config/settings.php'))
        ->toBe('config/settings.php'); // not the addon's, left alone
});

test('the addon leaves the review status links untouched by default', function () {
    // filterSubsubsub is a seam an addon can override; the base returns what it was given.
    expect($this->controller->filterSubsubsub(['all' => '<a>All</a>']))
        ->toBe(['all' => '<a>All</a>']);
});

test('the base lifecycle hooks are safe no-ops', function () {
    // install/registerShortcodes/registerTinymcePopups/registerWidgets are empty in the base — an
    // addon overrides the ones it needs — but the base wires every one to a hook, so each must be
    // callable without doing anything.
    expect(function () {
        $this->controller->install();
        $this->controller->registerShortcodes();
        $this->controller->registerTinymcePopups();
        $this->controller->registerWidgets();
    })->not->toThrow(\Throwable::class);
});

test('registering the addon languages loads its text domain without error', function () {
    // load_plugin_textdomain from the addon's own path; its effect is not observable offline (no
    // .mo ships), so what is pinned is that the path it builds is well-formed and the call runs clean.
    expect(fn () => $this->controller->registerLanguages())->not->toThrow(\Throwable::class);
});

test('the addon settings view wraps the rows it is handed', function () {
    // renderSettings resolves the addon's OWN view through the site-reviews/path filter, which the
    // addon's hooks register — so wire them up first, exactly as boot does.
    glsr(TestAddonHooks::class)->run();

    ob_start();
    $this->controller->renderSettings('<tr><td>a setting row</td></tr>');
    $html = (string) ob_get_clean();

    expect($html)->toContain('form-table')
        ->and($html)->toContain('a setting row');
});

test('an addon can register an asset without enqueuing it', function () {
    // registerAsset is the register-only twin of enqueueAsset — an addon that wants its script
    // available as a dependency but not loaded on the page reaches for it.
    (fn () => $this->registerAsset('css'))->call($this->controller);

    expect(wp_style_is('site-reviews-test-addon', 'registered'))->toBeTrue()
        ->and(wp_style_is('site-reviews-test-addon', 'enqueued'))->toBeFalse();
});

test('the addon hooks expose the addon\'s own post type', function () {
    // postType()/type() feed the hook table; type() is a @compat alias kept for older addons.
    $hooks = glsr(TestAddonHooks::class);

    expect((fn () => $this->postType())->call($hooks))->toBe('test-addon-thing')
        ->and((fn () => $this->type())->call($hooks))->toBe('test-addon-thing');
});
