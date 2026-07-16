<?php

use GeminiLabs\SiteReviews\Addons\Addon;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Defaults\VideoDefaults;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\TestAddon\Application as TestAddon;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The base class every premium addon extends.
 *
 * An addon is a plugin that lives INSIDE Site Reviews: no settings page, options row or container of
 * its own — it borrows all three from the parent, and this class is the borrowing. So the
 * namespacing is the whole job, and it fails silently:
 *
 *   option()   an addon's settings live under `settings.addons.{slug}.` in the PARENT's options row.
 *              The wrong prefix reads an empty string, not an error — features act switched off.
 *   make()     resolves a class in the ADDONS FRAMEWORK namespace (Compat, Updater, shared bases),
 *              NOT the addon's own, whatever the name suggests (__NAMESPACE__ is fixed in Addon.php).
 *   posts()    the addon's own post type, for the settings-page dropdowns.
 *   init()     finds the addon's Hooks class BY NAME. No registry: rename it and the addon loads,
 *              registers nothing, does nothing.
 *
 * The fixture (tests/pest/fixtures/site-reviews-test-addon) is a real addon because every method
 * reaches into an addon's directory or namespace, and a mock would only prove it agreed with itself.
 */

beforeEach(function () {
    resetPluginState();
});

function testAddon(): TestAddon
{
    return glsr(TestAddon::class);
}

/**
 * NAMED classes, not anonymous ones. init() derives the Hooks class from its OWN class name by
 * reflection, and an anonymous class's name is `Addon@anonymous` followed by a NUL byte and the
 * file path it was declared in — which Str::replaceLast() cannot meaningfully rewrite. Every real
 * addon is a named class, so the fixture has to be one too, or the test is exercising a shape that
 * cannot occur.
 */
class AddonWithNoHooks extends Addon
{
    public const NAME = 'An Addon With No Hooks';
    public const SLUG = 'no-hooks';
}

class AddonWithNoPostType extends Addon
{
    public const POST_TYPE = '';
    public const SLUG = 'no-post-type';
}

/*
 * Settings, which live in the parent's options row.
 */

test('an addon reads its own settings out of the parent\'s options', function () {
    // There is ONE options row on the site — the parent's — and every addon's settings are a branch
    // of it. An addon reading the wrong branch does not error, it reads '' — so the feature it
    // controls silently behaves as though it were switched off.
    glsr(OptionManager::class)->set('settings.addons.'.TestAddon::SLUG.'.enabled', 'yes');

    expect(testAddon()->option('enabled'))->toBe('yes');
});

test('and it does not matter whether the path is written with the settings prefix or without', function () {
    // Both forms appear throughout the addons. `settings.` is stripped and the addon's own prefix
    // is put on, so the two are the same path.
    glsr(OptionManager::class)->set('settings.addons.'.TestAddon::SLUG.'.enabled', 'yes');

    expect(testAddon()->option('settings.enabled'))->toBe('yes')
        ->and(testAddon()->option('enabled'))->toBe('yes');
});

test('a setting that was never saved comes back as the fallback, not as an error', function () {
    expect(testAddon()->option('never_saved'))->toBe('')
        ->and(testAddon()->option('never_saved', 'a fallback'))->toBe('a fallback')
        ->and(testAddon()->option('never_saved', 0, 'int'))->toBe(0);
});

test('an addon can read all of its settings at once', function () {
    glsr(OptionManager::class)->set('settings.addons.'.TestAddon::SLUG, [
        'enabled' => 'yes',
        'style' => 'modern',
    ]);

    $options = testAddon()->options();

    expect($options->enabled)->toBe('yes')
        ->and($options->style)->toBe('modern');
});

test('and can insist they are the shape it expects', function () {
    // Options come out of a settings row that has been on the site for years, through however many
    // versions of the addon. Restricting them through a Defaults class is what stops a key that was
    // removed two releases ago from still being read.
    glsr(OptionManager::class)->set('settings.addons.'.TestAddon::SLUG, [
        'id' => 'abc123',
        'a_key_from_2019' => 'still here',
    ]);

    $options = testAddon()->options(VideoDefaults::class);

    expect($options->id)->toBe('abc123')
        ->and($options->toArray())->not->toHaveKey('a_key_from_2019');
});

test('a defaults class that does not exist is ignored rather than fatal', function () {
    // is_a(…, true) on a string that names no class. An addon passing a typo'd class name gets its
    // options unrestricted, rather than a white screen on the settings page.
    glsr(OptionManager::class)->set('settings.addons.'.TestAddon::SLUG, ['enabled' => 'yes']);

    expect(testAddon()->options('Some\Class\That\Does\Not\Exist')->enabled)->toBe('yes');
});

/*
 * The addon's own post type.
 */

test('an addon lists its own posts, and only the published ones', function () {
    // These are the dropdowns on an addon's settings page — pick an alert, pick a theme. A draft is
    // not something a site owner can point a setting at.
    $published = createPost(['post_title' => 'A published thing', 'post_type' => TestAddon::POST_TYPE]);
    createPost([
        'post_status' => 'draft',
        'post_title' => 'A draft thing',
        'post_type' => TestAddon::POST_TYPE,
    ]);

    $posts = testAddon()->posts();

    expect($posts)->toHaveKey($published)
        ->and(implode(' ', $posts))->not->toContain('A draft thing');
});

test('and can put a placeholder at the top of the list', function () {
    // "— Select —". It has to be FIRST, because it is the option a dropdown opens on.
    createPost(['post_type' => TestAddon::POST_TYPE]);

    $posts = testAddon()->posts(-1, '— Select —');

    expect(array_key_first($posts))->toBe('')
        ->and(current($posts))->toBe('— Select —');
});

test('an addon with no post type of its own lists nothing', function () {
    // Most addons have none. The guard is what stops posts() querying for a post type of '' — which
    // matches nothing in WordPress, but is a database query all the same, on every settings page.
    expect((new AddonWithNoPostType())->posts())->toBe([]);
});

/*
 * The container.
 */

test('make() resolves the addon FRAMEWORK classes, not the addon\'s own', function () {
    // Worth reading carefully, because the name suggests otherwise. make() prefixes with
    // __NAMESPACE__ — and __NAMESPACE__ inside Addon.php is `GeminiLabs\SiteReviews\Addons`, fixed
    // at compile time. So whatever an addon passes, it comes back from the Addons\ namespace:
    // Compat, Updater, the shared Controller and Hooks base classes.
    //
    // It CANNOT resolve a concrete addon's own Controller — `Addon::make('Controller')` returns
    // the abstract Addons\Controller (and therefore a BlackHole, since an abstract class cannot be
    // built). Whether that is the intent is an open question; nothing in the plugin or the fixture
    // calls it, so this test pins what it does rather than what its name implies.
    expect(testAddon()->make('Compat'))
        ->toBeInstanceOf(\GeminiLabs\SiteReviews\Addons\Compat::class);
});

/*
 * Loading.
 */

test('an addon whose Hooks class is missing says so, rather than failing silently', function () {
    // init() finds the Hooks class BY NAME, derived from the Application's own. There is no
    // registry and no list — so an addon whose Hooks class is renamed, or never written, loads
    // perfectly happily, registers nothing at all, and does nothing. For ever.
    //
    // The logged line is the only thing that would ever tell anybody.
    (new AddonWithNoHooks())->init();

    expect(glsr(Console::class)->get())->toContain('missing a Hooks class');
});
