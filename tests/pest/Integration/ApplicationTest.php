<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Role;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;
use GeminiLabs\SiteReviews\TestAddon\Application as TestAddon;

use function GeminiLabs\SiteReviews\Tests\commitsTransaction;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The Application: how an addon gets in, and what it may do once in.
 *
 * register() is the front door for all nine premium addons, called from the addon's plugin file
 * before anything else. Everything it does is a refusal or a registration, and the refusals are the
 * interesting half — a half-registered addon is worse than an unregistered one, because the plugin
 * calls into it. It refuses on four grounds, each a real support ticket:
 *
 *   the class does not exist       deleted from disk but still in active_plugins
 *   the plugin FILE does not exist the directory was renamed, so nothing (assets, views, languages)
 *                                  can be read from it
 *   Site Reviews is too old        the addon needs a newer parent than installed
 *   Site Reviews is too new        the parent moved on and the addon has not
 *
 * The last two are gated from the addon's plugin header, which is why the fixture carries GLSR
 * headers. Registration binds the addon as a singleton, aliases it by id, and calls init() (its post
 * types and hooks) — doing that to an incompatible addon is the white screen the refusals prevent.
 */

beforeEach(function () {
    resetPluginState();
});

/**
 * Somebody's addon, whose plugin file is not where the layout says it should be — because this
 * class lives in a test file, and there is no `nowhere-at-all.php` next to it.
 */
class AddonWithNoPluginFile
{
    public const ID = 'nowhere-at-all';
    public const LICENSED = true;
    public const NAME = 'An Addon That Is Not There';
}

/*
 * Getting in.
 */

test('an addon is registered, bound as a singleton, and reachable by its id', function () {
    // All three matter. The singleton is what makes glsr(Addon::class) the SAME object everywhere,
    // which is the only reason an addon can hold state; the alias is what lets the parent look an
    // addon up by the id in its settings, where the class name is not known.
    glsr()->register(TestAddon::class);

    expect(glsr()->addon(TestAddon::ID))->toBe(TestAddon::class)
        ->and(glsr(TestAddon::ID))->toBeInstanceOf(TestAddon::class)
        ->and(glsr(TestAddon::class))->toBe(glsr(TestAddon::class)); // a singleton, not a new one each time
});

test('a registered addon reports its version, which is what the update check reads', function () {
    glsr()->register(TestAddon::class);

    expect(glsr()->retrieveAs('array', 'addons'))
        ->toHaveKey(TestAddon::ID)
        ->and(glsr()->retrieveAs('array', 'addons')[TestAddon::ID])->toBe('2.3.4');
});

test('a licensed addon is remembered as one, which is what the licence banners count', function () {
    // `const LICENSED = true` is the whole of it. License::status() walks this list, and the two
    // licence banners walk that — so an addon that failed to land here would never be nagged about.
    glsr()->register(TestAddon::class);

    expect(glsr()->retrieveAs('array', 'licensed'))->toHaveKey(TestAddon::ID);
});

/*
 * Being kept out.
 */

test('a class that does not exist is logged, not fatal', function () {
    // What a half-deleted addon looks like: WordPress still has it in active_plugins, the file is
    // gone, and something still calls register(). A fatal here is a white screen on somebody's
    // site; a logged line is a support ticket with an answer in it.
    glsr()->register('Some\Addon\That\Was\Deleted');

    expect(glsr(Console::class)->get())->toContain('invalid addon');
    expect(glsr()->addon('some-addon'))->toBeNull();
});

test('an addon whose plugin file is not where it should be is refused', function () {
    // Everything the parent reads FROM an addon — its assets, its config, its views, its
    // translations — is found relative to that file. An addon registered without one would be
    // asked for all of it and answer with nothing.
    glsr()->register(AddonWithNoPluginFile::class);

    expect(glsr(Console::class)->get())->toContain('invalid addon');
    expect(glsr()->addon(AddonWithNoPluginFile::ID))->toBeNull();
    expect(glsr()->retrieveAs('array', 'licensed'))->not->toHaveKey(AddonWithNoPluginFile::ID);
});

test('asking for an addon that was never registered gets nothing, not an error', function () {
    expect(glsr()->addon('an-addon-nobody-installed'))->toBeNull();
});

test('an addon that needs a newer Site Reviews is refused, and one the parent outgrew too', function () {
    // The two version gates, driven from the GLSR headers in each fixture's plugin file —
    // exactly where a real addon carries them. A refused addon is NOT initialized: calling
    // init() on an incompatible addon is the white screen these gates prevent.
    require_once glsr()->path('tests/pest/fixtures/site-reviews-gate-too-old/plugin/Application.php');
    require_once glsr()->path('tests/pest/fixtures/site-reviews-gate-too-new/plugin/Application.php');

    glsr()->register(GeminiLabs\SiteReviews\GateTooOld\Application::class);
    expect(glsr()->addon(GeminiLabs\SiteReviews\GateTooOld\Application::ID))->toBeNull();

    glsr()->register(GeminiLabs\SiteReviews\GateTooNew\Application::class);
    expect(glsr()->addon(GeminiLabs\SiteReviews\GateTooNew\Application::ID))->toBeNull();
});

test('an addon claimed by the premium bundle is parked, not registered twice', function () {
    // When Site Reviews Premium is installed it declares the addons it bundles; a standalone
    // copy of one of those must stand down and let the bundle's (Premium-namespaced) copy
    // register instead — two live copies of one addon is a fatal on the second class load.
    // ($this->addons survives between tests on the singleton, so the parked list — which is
    // per-test storage — is the assertion, not addon())
    add_filter('site-reviews/site-reviews-premium', fn () => [TestAddon::ID]);

    glsr()->register(TestAddon::class);

    expect(glsr()->retrieveAs('array', 'site-reviews-premium'))->toContain(TestAddon::class);
});

/*
 * The licence field a licensed addon adds to the settings page.
 */

test('a licensed addon puts its own licence key field on the settings page', function () {
    // The field does not exist in config/settings.php — it cannot, because the plugin does not
    // know which addons will be installed. Each licensed addon adds its own, labelled with its
    // own name, when the settings are first built.
    glsr()->license(TestAddon::class);

    $licenses = glsr()->settings('settings.licenses');

    expect($licenses)->toHaveKey(TestAddon::ID)
        ->and($licenses[TestAddon::ID]['label'])->toBe(TestAddon::NAME)
        ->and($licenses[TestAddon::ID]['type'])->toBe('secret'); // never rendered back as plain text
});

test('an addon that needs no licence adds no licence field', function () {
    // Not every addon is paid for. A free addon that grew an empty "Licence Key" box on the
    // settings page would be asking people to buy something that does not exist.
    glsr()->license(AddonWithNoPluginFile::class); // LICENSED, but…
    $before = glsr()->settings('settings.licenses');

    glsr()->license(SiteReviewsShortcode::class); // …this one has no ID/NAME/LICENSED at all

    expect(glsr()->settings('settings.licenses'))->toBe($before);
});

test('a class that does not exist does not take the settings page down with it', function () {
    // license() runs while the settings are being built, on every admin page load. A
    // ReflectionException here would be a fatal on the settings screen — so it is caught, and the
    // addon is simply skipped.
    glsr()->license('Some\Addon\That\Was\Deleted');

    expect(glsr()->settings())->not->toBeEmpty();          // the settings still built…
    expect(glsr()->settings('settings.licenses'))->toBe(''); // …and grew no licence field
});

/*
 * The rest of the container's public surface, which everything else leans on.
 */

test('a page nobody declared a permission for falls back, rather than being left open', function () {
    // getPermission() is what every menu page, metabox and ajax route asks before it does
    // anything. An unknown page must NOT come back empty — an empty capability is a page anybody
    // can open — so it falls back to edit_posts, mapped through the plugin's own capability map.
    $fallback = glsr(Role::class)->capability('edit_posts');

    expect($fallback)->not->toBeEmpty();
    expect(glsr()->getPermission('settings'))->not->toBeEmpty()
        ->and(glsr()->getPermission('a-page-that-does-not-exist'))->toBe($fallback);
});

test('an administrator has permission in the admin, and a subscriber does not', function () {
    // hasPermission() is deliberately true off an admin screen — the front end has no permissions
    // to check — so the screen has to be set, or this asserts nothing at all.
    set_current_screen(glsr()->post_type);

    wp_set_current_user(createUser(['role' => 'administrator']));
    expect(glsr()->hasPermission('settings'))->toBeTrue();

    wp_set_current_user(createUser(['role' => 'subscriber']));
    expect(glsr()->hasPermission('settings'))->toBeFalse();

    set_current_screen('front');
});

test('can() asks the role, and passes the arguments through', function () {
    // The ...$args are what make a per-post capability work — `edit_post` means nothing without
    // the post it is being asked about.
    wp_set_current_user(createUser(['role' => 'administrator']));

    expect(glsr()->can('edit_others_posts'))->toBeTrue();
    expect(glsr()->can('a_capability_nobody_has'))->toBeFalse();
});

test('an ajax request is not an admin screen, even though it is in wp-admin', function () {
    // isAdmin() is asked before anything decides to enqueue admin assets or render admin markup.
    // Every ajax request the FRONT END makes runs through wp-admin/admin-ajax.php, so is_admin()
    // is true for all of them — and treating one as an admin screen would put admin CSS into a
    // visitor's review submission.
    add_filter('wp_doing_ajax', '__return_true');

    expect(glsr()->isAdmin())->toBeFalse();
});

test('a shortcode is looked up by tag, and an unknown one is nothing', function () {
    expect(glsr()->shortcode('site_reviews'))->toBeInstanceOf(SiteReviewsShortcode::class)
        ->and(glsr()->shortcode('not_a_shortcode'))->toBeNull()
        ->and(glsr()->shortcode(''))->toBeNull()
        ->and(glsr()->shortcode(null))->toBeNull();
});

test('a registered shortcode class that is not a shortcode is refused', function () {
    // The registry is writable storage; a broken registration (an addon mapping a tag to some
    // other class) must come back as nothing rather than an object the caller then renders.
    glsr()->append('shortcodes', stdClass::class, 'not_really_a_shortcode');

    expect(glsr()->shortcode('not_really_a_shortcode'))->toBeNull();
});

test('a permission that is not a capability string falls back too', function () {
    // A page's permission entry can be an array of tabs; a tab whose value is not a string
    // (a broken filter, a typo'd config) must fall back to edit_posts, not become an
    // is-this-user-allowed check against garbage.
    add_filter('site-reviews/defaults/permission/defaults',
        fn ($defaults) => $defaults + ['broken-page' => ['broken-tab' => 123]]);

    expect(glsr()->getPermission('broken-page', 'broken-tab'))
        ->toBe(glsr(Role::class)->capability('edit_posts'));
});

test('init installs, adopts the previous version\'s settings, and schedules the migration', function () {
    // What a major-version update looks like on first load: no db_version option (the tables
    // may be missing), no settings under the new key, the old version's settings still under
    // the old one. Install::run() is DDL, so the transaction commits — declared, and the
    // options this test destroys are put back by hand and COMMITted at the end.
    commitsTransaction();
    global $wpdb;
    $dbVersionKey = glsr()->prefix.'db_version';
    $originalDbVersion = get_option($dbVersionKey);
    $originalSettings = get_option(OptionManager::databaseKey());
    try {
        // while installing, init does nothing at all
        wp_installing(true);
        glsr()->init();
        wp_installing(false);

        delete_option($dbVersionKey);
        delete_option(OptionManager::databaseKey());
        update_option(OptionManager::databaseKey(6), [
            'settings' => ['general' => ['require' => ['approval' => 'yes']]],
        ]);
        OptionManager::flushSettingsCache();

        glsr()->init();

        expect(get_option($dbVersionKey))->not->toBeEmpty(); // Install ran
        expect(get_option(OptionManager::databaseKey()))->toBe([
            'settings' => ['general' => ['require' => ['approval' => 'yes']]],
        ]); // the previous version's settings were adopted

        // and a database version from an older plugin forces a migration on the next init
        update_option($dbVersionKey, '1.0');
        glsr()->init();
    } finally {
        wp_installing(false);
        update_option(OptionManager::databaseKey(), $originalSettings);
        update_option($dbVersionKey, $originalDbVersion);
        delete_option(OptionManager::databaseKey(6));
        OptionManager::flushSettingsCache();
        $wpdb->query('COMMIT'); // the restore must outlive the teardown rollback
    }
});

test('the defaults are the settings config flattened to values', function () {
    // Everything downstream — OptionManager::normalize(), the settings page, every glsr_get_option
    // fallback — is built on this. An empty defaults array would mean every setting on every site
    // silently reading as ''.
    $defaults = glsr()->defaults();

    expect($defaults)->not->toBeEmpty()
        ->and($defaults)->toHaveKey('settings');
});
