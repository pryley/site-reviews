<?php

use GeminiLabs\SiteReviews\Controllers\MenuController;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Html\SettingForm;

use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The plugin's admin menu, and the pages behind it.
 *
 * Four submenu pages — Settings, Tools, Help & Support, Premium — each registered by NAME:
 * registerSubMenus() builds a method name from the slug and skips the page if the method is missing.
 * A page can silently stop existing, the only symptom a missing menu item.
 *
 * Permissions are enforced twice: add_submenu_page() gets the capability (stops reaching the page by
 * URL), and parseWithFilter() drops TABS a person may not see (stops the Licenses tab appearing to
 * an editor) — the settings page renders every tab it is given.
 *
 * Pages are rendered by calling the menu callbacks, as WordPress does. They are big (the settings
 * page builds every field of every tab), which is the point: a renamed template tag, a deleted view,
 * a malformed field config all land here.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
    set_current_screen('edit-'.glsr()->post_type);
    $GLOBALS['submenu'] = [];
    $GLOBALS['menu'] = [];
});

afterEach(function () {
    set_current_screen('front');
    unset($GLOBALS['submenu'], $GLOBALS['menu']);
});

function parentSlug(): string
{
    return 'edit.php?post_type='.glsr()->post_type;
}

/**
 * What a menu callback printed.
 */
function renderedPage(string $method): string
{
    ob_start();
    glsr(MenuController::class)->$method();

    return (string) ob_get_clean();
}

/*
 * The menu.
 */

test('the plugin adds its pages under its own menu', function () {
    global $submenu;

    glsr(MenuController::class)->registerSubMenus();

    $slugs = array_column($submenu[parentSlug()], 2);

    expect($slugs)->toContain('glsr-settings')
        ->toContain('glsr-tools')
        ->toContain('glsr-documentation')
        ->toContain('glsr-premium');
});

test('a page is only added for somebody who may open it', function () {
    // add_submenu_page() does not merely RECORD the capability — it refuses to add the
    // page at all if the current user has not got it (and puts the slug in
    // $_wp_submenu_nopriv instead, which is what makes wp-admin refuse the URL too). So an
    // editor does not get a Settings page in the menu, and cannot reach it by typing the
    // address either. The page callback never checks, and does not have to.
    global $submenu;
    wp_set_current_user(createUser(['role' => 'editor']));

    glsr(MenuController::class)->registerSubMenus();

    expect(array_column($submenu[parentSlug()] ?? [], 2))->not->toContain('glsr-settings');

    $GLOBALS['submenu'] = [];
    wp_set_current_user(createUser(['role' => 'administrator']));

    glsr(MenuController::class)->registerSubMenus();

    $capabilities = array_column($submenu[parentSlug()], 1, 2);
    expect($capabilities['glsr-settings'])->toBe('manage_options');
});

test('the menu says how many reviews are waiting to be approved', function () {
    // The bubble beside "Reviews" in the admin menu. It is the only way anybody knows
    // there is something to moderate without going and looking.
    global $menu;
    createReview(['is_approved' => false]);
    createReview(['is_approved' => false]);
    createReview(['is_approved' => true]);
    $menu = [10 => ['Reviews', 'edit_posts', parentSlug(), '', 'menu-top']];

    glsr(MenuController::class)->registerMenuCount();

    expect($menu[10][0])->toContain('awaiting-mod')
        ->toContain('>2<'); // and only the pending ones are counted
});

test('the submenu is reordered so that the reviews stay at the top', function () {
    // WordPress puts "All Reviews" and "Add New" first and then appends whatever a plugin
    // registers. Reordering keeps the post-type items above the plugin's own pages,
    // which is the order people expect from every other post type.
    global $submenu;
    $submenu[parentSlug()] = [
        5 => ['All Reviews', 'edit_posts', parentSlug()],
        15 => ['Settings', 'manage_options', 'glsr-settings'],
        10 => ['All Categories', 'manage_categories', 'edit-tags.php'],
    ];

    glsr(MenuController::class)->reorderSubMenu();

    // the two "All …" items keep their order, and everything else is appended after them
    expect(array_column($submenu[parentSlug()], 0))->toBe([
        'All Reviews', 'All Categories', 'Settings',
    ]);
});

test('the old singular create capability is stripped from every role, and admins can still add reviews', function () {
    // The post type maps create_posts to the plural create_site-reviews (Role::capability()).
    // setCustomPermissions() removes the singular create_site-review, which nothing checks, from
    // every role. WP_Roles::add_cap() and remove_cap() change the stored roles array, not the
    // WP_Role objects that get_role() returns, so the array is what the test seeds and reads.
    $singular = 'create_'.glsr()->post_type;
    $plural = glsr(\GeminiLabs\SiteReviews\Role::class)->capability('create_posts');
    foreach (array_keys(wp_roles()->roles) as $role) {
        wp_roles()->add_cap($role, $singular);
    }

    glsr(MenuController::class)->setCustomPermissions();

    foreach (wp_roles()->roles as $role) {
        expect($role['capabilities'])->not->toHaveKey($singular);
    }
    expect(wp_roles()->roles['administrator']['capabilities'][$plural] ?? false)->toBeTrue();
});

/*
 * The settings page, which is the biggest thing the plugin renders.
 */

test('the settings page renders every tab, with its fields', function () {
    $html = renderedPage('renderSettingsMenuCallback');

    // the tabs
    foreach (['general', 'reviews', 'forms', 'schema', 'strings', 'integrations', 'licenses'] as $tab) {
        expect($html)->toContain('id="'.$tab.'"');
    }
    // and a field out of three of them, named the way the form posts it — the name is the
    // setting's own path, which is what SettingsController reads back out of $_POST
    expect($html)->toContain('name="site_reviews[settings][general][notifications]')
        ->toContain('name="site_reviews[settings][reviews][assignment]')
        ->toContain('name="site_reviews[settings][forms][required]');
});

test('the premium settings tab needs the premium plugin, the addons tab addon settings', function () {
    expect(renderedPage('renderSettingsMenuCallback'))->not->toContain('data-id="premium"');

    // The installed premium plugin keeps its tab even when every feature is
    // toggled off (no settings at all). Registration lands on the Application
    // singleton's $addons property, which no teardown resets — so it is backed
    // up and restored by hand.
    require_once glsr()->path('tests/pest/fixtures/site-reviews-premium/plugin/Application.php');
    require_once glsr()->path('tests/pest/fixtures/site-reviews-premium/plugin/Hooks.php');
    $registry = new ReflectionProperty(get_class(glsr()), 'addons');
    $registry->setAccessible(true);
    $registered = $registry->getValue(glsr());
    try {
        glsr()->register(GeminiLabs\SiteReviews\Premium\Shell\Application::class);
        $html = renderedPage('renderSettingsMenuCallback');
    } finally {
        $registry->setValue(glsr(), $registered);
    }

    expect($html)->toContain('data-id="premium"')->toContain('>Premium</a>')
        ->not->toContain('data-id="addons"');
});

test('a settings tab is not rendered for somebody who may not see it', function () {
    // parseWithFilter() drops the tab before the form is built. Rendering it and hiding
    // it with CSS would be putting the licence keys in the page for anybody to read.
    wp_set_current_user(createUser(['role' => 'editor']));

    $html = renderedPage('renderSettingsMenuCallback');

    expect($html)->not->toContain('id="licenses"');
});

test('a setting that depends on another one is hidden until the other one is set', function () {
    // `depends_on` in config/settings.php. The Discord webhook field is pointless unless
    // Discord notifications are switched on, and SettingForm works out at RENDER time
    // whether it should start hidden — the JS only handles it changing after that.
    glsr(OptionManager::class)->set('settings.general.notifications', []);
    $hidden = (string) glsr(SettingForm::class, ['groups' => ['general' => 'General']])->build();

    glsr(OptionManager::class)->set('settings.general.notifications', ['discord']);
    $shown = (string) glsr(SettingForm::class, ['groups' => ['general' => 'General']])->build();

    // the field carries what it depends on, so the JS can show and hide it as the boxes
    // are ticked
    expect($shown)->toContain('data-depends');

    // and it starts out hidden, or not, depending on where the setting started
    // (SettingField::classAttrField adds `hidden` to a field whose dependency is unmet)
    expect(substr_count($hidden, 'glsr-setting-field hidden'))
        ->toBeGreaterThan(substr_count($shown, 'glsr-setting-field hidden'));
});

/*
 * The other three pages.
 */

test('the tools page renders', function () {
    // The Rollback tool asks wordpress.org which versions there are to go back to. That is
    // the one thing on the page that leaves the site, and `plugins_api` is WordPress's own
    // short-circuit for it — without this the call reaches blockHttpRequests(), fails, and
    // plugins_api() raises a warning about not being able to reach wordpress.org.
    add_filter('plugins_api', fn () => (object) [
        'versions' => ['7.2.0' => '', '8.0.0' => '', 'trunk' => ''],
    ], 10, 3);

    $html = renderedPage('renderToolsMenuCallback');

    expect($html)->toContain('8.0.0'); // a version to roll back to

    expect($html)->toContain('id="general"')
        ->toContain('id="console"')
        ->toContain('id="system-info"')
        ->toContain('id="scheduled"');
});

test('the help page renders', function () {
    $html = renderedPage('renderDocumentationMenuCallback');

    expect($html)->toContain('id="support"')
        ->toContain('id="faq"')
        ->toContain('id="shortcodes"')
        ->toContain('id="hooks"');
});

test('the premium page renders for a site that has not bought it', function () {
    // It lists what premium would add, and the list comes from an API call that
    // blockHttpRequests() refuses. So this is the page as somebody sees it when the API
    // is unreachable — which must still be a page, not a blank screen.
    $html = renderedPage('renderPremiumMenuCallback');

    expect($html)->not->toBeEmpty();
});

test('the addons tab is only offered when there is an addon to configure', function () {
    // An empty tab is worse than no tab.
    expect(renderedPage('renderSettingsMenuCallback'))->not->toContain('id="addons"')
        ->and(renderedPage('renderSettingsMenuCallback'))->not->toContain('id="premium"');
});

test('the help page puts hosted sections on the premium tab under one support notice', function () {
    require_once glsr()->path('tests/pest/fixtures/site-reviews-premium-host/plugin/Application.php');
    require_once glsr()->path('tests/pest/fixtures/site-reviews-premium-host/plugin/Hooks.php');
    require_once glsr()->path('tests/pest/fixtures/site-reviews-hosted-addon/plugin/Application.php');
    require_once glsr()->path('tests/pest/fixtures/site-reviews-hosted-addon/plugin/Hooks.php');
    glsr()->register(GeminiLabs\SiteReviews\TestAddon\Application::class);
    glsr()->register(GeminiLabs\SiteReviews\Premium\Host\Application::class);
    glsr()->register(
        GeminiLabs\SiteReviews\Premium\HostedThing\Application::class,
        glsr(GeminiLabs\SiteReviews\Premium\Host\Application::class)
    );
    $sections = fn (array $documentation) => array_merge($documentation, [
        'site-reviews-hosted-addon' => '<p>Hosted documentation.</p>',
        'site-reviews-test-addon' => '<p>Standalone documentation.</p>',
    ]);
    add_filter('site-reviews/addon/documentation', $sections, 99);
    try {
        $html = renderedPage('renderDocumentationMenuCallback');
        $addonsTab = strpos($html, 'class="glsr-nav-view ui-tabs-hide" id="addons"');
        $premiumTab = strpos($html, 'class="glsr-nav-view ui-tabs-hide" id="premium"');

        expect($addonsTab)->toBeInt()
            ->and($premiumTab)->toBeInt()
            ->and(strpos($html, 'Standalone documentation.'))->toBeGreaterThan($addonsTab)->toBeLessThan($premiumTab)
            ->and(strpos($html, 'Hosted documentation.'))->toBeGreaterThan($premiumTab)
            ->and(substr_count(substr($html, $premiumTab), 'To receive support for Site Reviews Premium'))->toBe(1);
    } finally {
        remove_filter('site-reviews/addon/documentation', $sections, 99);
        GeminiLabs\SiteReviews\Tests\unregisterAddons(
            GeminiLabs\SiteReviews\TestAddon\Application::ID,
            GeminiLabs\SiteReviews\Premium\Host\Application::ID,
            GeminiLabs\SiteReviews\Premium\HostedThing\Application::ID
        );
    }
});

test('the menu count walks past everybody else\'s menu entries', function () {
    global $menu;
    createReview(['is_approved' => false]);
    $menu = [
        5 => ['Posts', 'edit_posts', 'edit.php', '', 'menu-top'],
        10 => ['Reviews', 'edit_posts', parentSlug(), '', 'menu-top'],
    ];

    glsr(MenuController::class)->registerMenuCount();

    expect($menu[5][0])->toBe('Posts'); // untouched
    expect($menu[10][0])->toContain('awaiting-mod');
});

test('a page with no renderer, or whose callback an addon broke, is skipped', function () {
    global $submenu;
    $submenu[parentSlug()] = [5 => ['All Reviews', 'edit_posts', parentSlug()]];
    add_filter('site-reviews/addon/submenu/pages', fn ($pages) => $pages + ['bogus' => 'Bogus']);
    add_filter('site-reviews/addon/submenu/callback',
        fn ($callback, $slug) => 'tools' === $slug ? 'not-a-callable-thing' : $callback, 10, 2);

    glsr(MenuController::class)->registerSubMenus();

    $titles = array_column($submenu[parentSlug()], 0);
    expect($titles)->toContain('Settings')
        ->not->toContain('Bogus')  // no renderBogusMenuCallback method exists
        ->not->toContain('Tools'); // its callback was filtered into garbage
    expect($submenu[parentSlug()][5][0])->toBe('All Reviews'); // core's entry, not reclassed
});

test('the add-new submenu entry is removed', function () {
    global $submenu;
    $addNew = 'post-new.php?post_type='.glsr()->post_type;
    $submenu[parentSlug()] = [10 => ['Add New', 'edit_posts', $addNew]];

    glsr(MenuController::class)->removeSubMenu();

    expect(array_column($submenu[parentSlug()] ?? [], 2))->not->toContain($addNew);
});

test('the add-new entry is removed even before the admin includes are loaded', function () {
    // admin_init can fire before wp-admin/includes/plugin.php on some request shapes, so the
    // controller requires it on demand. The armed function_exists shadow makes this process
    // look like one of those; the require_once is idempotent, so the removal still happens.
    global $submenu;
    $addNew = 'post-new.php?post_type='.glsr()->post_type;
    $submenu[parentSlug()] = [10 => ['Add New', 'edit_posts', $addNew]];

    \GeminiLabs\SiteReviews\Tests\armFailingFunction('function_exists');
    try {
        glsr(MenuController::class)->removeSubMenu();
    } finally {
        \GeminiLabs\SiteReviews\Tests\disarmFailingFunctions();
    }

    expect(array_column($submenu[parentSlug()] ?? [], 2))->not->toContain($addNew);
});

test('reordering an empty submenu is a no-op', function () {
    global $submenu;
    unset($submenu[parentSlug()]);

    glsr(MenuController::class)->reorderSubMenu();

    expect($submenu)->not->toHaveKey(parentSlug());
});

test('a premium licence alone does not relabel the upgrade pitch', function () {
    // The submenu title stays "Upgrade to Premium" for everybody — even a
    // premium licence holder with standalone addons. Only the installed
    // premium plugin renames it, through the addon/submenu/pages filter,
    // because only then is the page a control panel instead of a pitch.
    glsr()->bind(GeminiLabs\SiteReviews\License::class, GeminiLabs\SiteReviews\Tests\FakeLicense::class, true);
    GeminiLabs\SiteReviews\Tests\FakeLicense::$isPremium = true;
    try {
        global $submenu;
        $submenu[parentSlug()] = [];
        glsr(MenuController::class)->registerSubMenus();
        $titles = array_column($submenu[parentSlug()], 0);
    } finally {
        GeminiLabs\SiteReviews\Tests\FakeLicense::$isPremium = false;
    }

    expect($titles)->toContain('Upgrade to Premium')->not->toContain('Addons');
});

test('the installed premium plugin relabels the submenu through the pages filter', function () {
    $relabel = function (array $pages) {
        $pages['premium'] = 'Premium';

        return $pages;
    };
    add_filter('site-reviews/addon/submenu/pages', $relabel);
    try {
        global $submenu;
        $submenu[parentSlug()] = [];
        glsr(MenuController::class)->registerSubMenus();
        $titles = array_column($submenu[parentSlug()], 0);
    } finally {
        remove_filter('site-reviews/addon/submenu/pages', $relabel);
    }

    expect($titles)->toContain('Premium')->not->toContain('Upgrade to Premium');
});

test('the features pitch is fetched and sorted, premium first', function () {
    $http = fn () => [
        'body' => (string) wp_json_encode(['data' => [
            ['feature' => 'Ordinary thing', 'premium' => false],
            ['feature' => 'Premium thing', 'premium' => true],
        ]]),
        'cookies' => [], 'filename' => null, 'headers' => [],
        'response' => ['code' => 200, 'message' => 'OK'],
    ];
    add_filter('pre_http_request', $http);
    try {
        $html = renderedPage('renderPremiumMenuCallback');
    } finally {
        remove_filter('pre_http_request', $http);
    }

    expect($html)->toContain('Premium thing')
        ->toContain('Ordinary thing');
    expect(strpos($html, 'Premium thing'))->toBeLessThan(strpos($html, 'Ordinary thing'));
});
