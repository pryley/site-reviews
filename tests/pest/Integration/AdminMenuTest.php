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
 * Four submenu pages — Settings, Tools, Help & Support, Premium — each of which is
 * registered by NAME: registerSubMenus() builds a method name from the slug and skips
 * the page if no such method exists. So a page can silently stop existing, and the only
 * symptom is a menu item that is not there.
 *
 * Permissions are enforced twice, and both matter. add_submenu_page() is given the
 * capability, which is what stops somebody reaching the page by typing the URL; and
 * parseWithFilter() drops the TABS a person may not see, which is what stops the
 * Licenses tab appearing to an editor. The second is not decoration — the settings page
 * renders every tab it is given.
 *
 * The pages are rendered by calling the menu callbacks, which is exactly what WordPress
 * does. They are big — the settings page builds every field of every tab — and that is
 * the point: a template tag renamed, a view deleted, a field whose config is malformed,
 * all of it lands here.
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
