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

test('nobody may add a review from the menu', function () {
    // Reviews are written by visitors, not by administrators using "Add New" — and the
    // capability is stripped from every role, not just hidden from the menu.
    glsr(MenuController::class)->setCustomPermissions();

    foreach (array_keys(wp_roles()->roles) as $role) {
        expect(get_role($role)->has_cap('create_'.glsr()->post_type))->toBeFalse();
    }
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
    expect(renderedPage('renderSettingsMenuCallback'))->not->toContain('id="addons"');
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

test('reordering an empty submenu is a no-op', function () {
    global $submenu;
    unset($submenu[parentSlug()]);

    glsr(MenuController::class)->reorderSubMenu();

    expect($submenu)->not->toHaveKey(parentSlug());
});

test('the premium page lists the addons for a premium member', function () {
    // isPremium through the suite's FakeLicense; the addons list from a faked API response.
    glsr()->bind(GeminiLabs\SiteReviews\License::class, GeminiLabs\SiteReviews\Tests\FakeLicense::class, true);
    GeminiLabs\SiteReviews\Tests\FakeLicense::$isPremium = true;
    $http = fn () => [
        'body' => (string) wp_json_encode(['data' => [[
            'description' => 'Does premium things.',
            'id' => 'addon-x',
            'slug' => 'addon-x',
            'title' => 'Addon X',
            'url' => 'https://niftyplugins.com/plugins/addon-x',
        ]]]),
        'cookies' => [], 'filename' => null, 'headers' => [],
        'response' => ['code' => 200, 'message' => 'OK'],
    ];
    add_filter('pre_http_request', $http);
    try {
        $html = renderedPage('renderPremiumMenuCallback');

        // and the submenu entry is relabelled: a member has addons, not an upgrade pitch
        global $submenu;
        $submenu[parentSlug()] = [];
        glsr(MenuController::class)->registerSubMenus();
        $titles = array_column($submenu[parentSlug()], 0);
    } finally {
        remove_filter('pre_http_request', $http);
        GeminiLabs\SiteReviews\Tests\FakeLicense::$isPremium = false;
    }

    expect($html)->toContain('Addon X');
    expect($titles)->toContain('Addons')->not->toContain('Upgrade to Premium');
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
