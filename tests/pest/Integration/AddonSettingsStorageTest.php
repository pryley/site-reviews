<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Premium\Host\Application as HostAddon;
use GeminiLabs\SiteReviews\Premium\HostedThing\Application as HostedAddon;
use GeminiLabs\SiteReviews\TestAddon\Application as TestAddon;
use GeminiLabs\SiteReviews\TestAddon\Controller as TestAddonController;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

require_once glsr()->path('tests/pest/fixtures/site-reviews-premium-host/plugin/Application.php');
require_once glsr()->path('tests/pest/fixtures/site-reviews-premium-host/plugin/Hooks.php');
require_once glsr()->path('tests/pest/fixtures/site-reviews-hosted-addon/plugin/Application.php');
require_once glsr()->path('tests/pest/fixtures/site-reviews-hosted-addon/plugin/Hooks.php');
require_once glsr()->path('tests/pest/fixtures/site-reviews-hosted-addon/plugin/OutsideNamespaceApplication.php');

/*
 * Per-addon settings storage: compose on read, split on write.
 *
 * Since the storage rework, a registered addon's settings no longer live inside
 * the parent's options row — each addon has its own row (the snake_cased addon
 * id), and OptionManager stitches the two views together: all() MOUNTS every
 * registered addon's row at `settings.addons.{slug}` (compose), and every
 * persisted write STRIPS those subtrees back out to their own rows (split).
 * Nothing downstream knows: option paths, filters and the settings form are
 * unchanged, which is what most of these tests pin down.
 *
 * Hosted addons (the merged premium plugin's modules) take the same machinery
 * one step further: they have no main file and no row of their own — identity
 * comes from the $host passed at registration, paths remap into the host's
 * file tree, and settings store inside the HOST's row. The host mounts its
 * whole subtree in the composed view under its own slug (settings.{hostSlug}.
 * {slug}.*), with the reserved "is_enabled" toggle inside each subtree. An
 * addon reaches its own settings through Addon::option()/updateOption(),
 * which resolve against settingsPath() — no path is rewritten on the way in,
 * so the standalone-era spelling is not an alias for the hosted mount.
 */

beforeEach(function () {
    resetPluginState();
    glsr()->register(TestAddon::class);
});

function coreRow(): array
{
    return \GeminiLabs\SiteReviews\Helpers\Arr::consolidate(get_option(OptionManager::databaseKey()));
}

function seedLegacy(string $value): void
{
    // A pre-rework install: the addon's settings still inside the parent's row.
    $core = coreRow();
    $core['settings']['addons']['test-addon']['enabled'] = $value;
    update_option(OptionManager::databaseKey(), $core, true);
    glsr()->discard('settings'); // drop the composed cache so the next read recomposes
}

test('an addon option key is the snake_cased addon id', function () {
    expect(OptionManager::addonKey(TestAddon::ID))->toBe('site_reviews_test_addon')
        ->and(glsr(TestAddon::class)->storageKey())->toBe('site_reviews_test_addon');
});

/*
 * Split on write.
 */

test('a programmatic addon write survives the settings-form sanitize callback', function () {
    // register_setting() attaches the form's sanitize callback to EVERY
    // update_option() of the core key — including the one persist() makes
    // after splitting. Before the isPersisting() guard it re-processed that
    // write: merged the stale composed memo back over it and re-split,
    // clobbering the addon row just written. Found by the premium feature
    // toggles, which only ever failed on real admin requests — the only
    // place admin_init has registered the setting.
    glsr(GeminiLabs\SiteReviews\Controllers\SettingsController::class)->registerSettings();
    glsr(OptionManager::class)->set('settings.addons.test-addon.enabled', 'yes'); // the memo now holds "yes"
    glsr(OptionManager::class)->set('settings.addons.test-addon.enabled', 'no');

    $own = get_option('site_reviews_test_addon');
    expect($own['settings']['enabled'])->toBe('no');
});

test('writing an addon setting lands in the addon\'s own row, not the parent\'s', function () {
    glsr(OptionManager::class)->set('settings.addons.test-addon.enabled', 'yes');

    $own = get_option('site_reviews_test_addon');
    expect($own['settings']['enabled'])->toBe('yes')
        ->and($own['version'])->toBe('2.3.4') // stamped so a support export shows which build wrote it
        ->and(coreRow()['settings']['addons'] ?? [])->not->toHaveKey('test-addon');
});

test('and reading it back goes through the composed view, so nothing downstream changed', function () {
    glsr(OptionManager::class)->set('settings.addons.test-addon.enabled', 'yes');

    expect(glsr_get_option('addons.test-addon.enabled'))->toBe('yes')
        ->and(glsr(TestAddon::class)->option('enabled'))->toBe('yes');
});

/*
 * Compose on read.
 */

test('an addon\'s own row is mounted into the composed view', function () {
    update_option('site_reviews_test_addon', ['settings' => ['enabled' => 'maybe'], 'version' => '2.3.4'], true);
    glsr()->discard('settings');

    expect(glsr_get_option('addons.test-addon.enabled'))->toBe('maybe');
});

test('the own row shadows a stale legacy subtree left in the parent\'s row', function () {
    // The legacy subtree is deliberately NOT deleted at migration (it protects a downgrade to an
    // older addon build); the composed view must therefore always prefer the addon's own row.
    seedLegacy('legacy');
    update_option('site_reviews_test_addon', ['settings' => ['enabled' => 'own'], 'version' => '2.3.4'], true);
    glsr()->discard('settings');

    expect(glsr_get_option('addons.test-addon.enabled'))->toBe('own');
});

test('before migration the legacy subtree is still what the addon reads', function () {
    // No own row yet (a site that updated the parent before the addon): compose must NOT mount an
    // empty row over the legacy values — the addon would silently lose all its settings.
    delete_option('site_reviews_test_addon');
    seedLegacy('legacy');

    expect(glsr_get_option('addons.test-addon.enabled'))->toBe('legacy');
});

/*
 * Migration.
 */

test('migrateOptions copies the legacy subtree into the addon\'s own row, once', function () {
    delete_option('site_reviews_test_addon');
    seedLegacy('from-legacy');

    glsr(TestAddonController::class)->migrateOptions();
    expect(get_option('site_reviews_test_addon')['settings']['enabled'])->toBe('from-legacy');

    // One-shot: a second run must not overwrite — the row now belongs to the addon.
    seedLegacy('changed-later');
    glsr(TestAddonController::class)->migrateOptions();
    expect(get_option('site_reviews_test_addon')['settings']['enabled'])->toBe('from-legacy');
});

test('the legacy subtree is garbage-collected on the next settings save', function () {
    seedLegacy('legacy');
    glsr(OptionManager::class)->set('settings.addons.test-addon.enabled', 'yes');

    // split() rebuilt the parent's row without the registered addon's subtree.
    expect(coreRow()['settings']['addons'] ?? [])->not->toHaveKey('test-addon');
});

/*
 * Short config keys.
 */

test('short config keys are namespaced to the addon; fully-prefixed keys pass through', function () {
    $settings = glsr(TestAddonController::class)->filterSettings([]);

    expect($settings)->toHaveKey('settings.addons.test-addon.enabled')   // full prefix, untouched
        ->and($settings)->toHaveKey('settings.addons.test-addon.short_key') // short key, namespaced
        ->and($settings)->toHaveKey('settings.addons.test-addon.bare_key')  // bare key, namespaced
        ->and($settings)->not->toHaveKey('settings.short_key')
        ->and($settings)->not->toHaveKey('bare_key');
});

test('an addon written for 8.1.x keeps its fully-prefixed keys and depends_on', function () {
    // A standalone addon's mount IS settings.addons.{slug}, so mounting is an
    // identity for the shape every released addon uses. Nothing about the
    // hosted-addon work may change what these resolve to.
    $settings = glsr(TestAddonController::class)->filterSettings([]);

    expect($settings)->toHaveKey('settings.addons.test-addon.enabled')
        ->and($settings['settings.addons.test-addon.short_key']['depends_on'])
        ->toBe(['settings.addons.test-addon.enabled' => ['yes']]);
});

test('a depends_on key is mounted the same way the setting key is', function () {
    // Without this a bare depends_on passes through unmounted, and the field it
    // gates renders unconditionally instead: no error, just a form that ignores
    // its own conditions.
    $settings = glsr(TestAddonController::class)->filterSettings([]);

    expect($settings['settings.addons.test-addon.bare_key']['depends_on'])
        ->toBe(['settings.addons.test-addon.short_key' => ['short']]);
});

/*
 * Hosted addons.
 */

function registerHostedFixture(): array
{
    glsr()->register(HostAddon::class);
    $host = glsr(HostAddon::class);
    glsr()->register(HostedAddon::class, $host);
    return [$host, glsr(HostedAddon::class)];
}

test('an addon without a main file is refused unless a host vouches for it', function () {
    glsr()->register(HostedAddon::class);

    expect(glsr(Console::class)->get())->toContain('invalid addon')
        ->and(glsr()->addon(HostedAddon::ID))->toBeNull();
});

test('a hosted addon takes its identity and version gates from its host\'s main file', function () {
    [$host, $hosted] = registerHostedFixture();

    expect(glsr()->addon(HostedAddon::ID))->toBe(HostedAddon::class)
        ->and($hosted->file)->toBe($host->file)
        ->and($hosted->basename)->toBe($host->basename)
        ->and($hosted->version)->toBe('9.9.9'); // the host's Version header; no stamped VERSION const
});

test('a hosted addon resolves settings paths and its storage key against its host', function () {
    // The standalone branch of both is covered above; this is the hosted one,
    // which only exists when an addon runs as a module of another plugin.
    [$host, $hosted] = registerHostedFixture();

    expect($hosted->settingsPath())->toBe('premium-host.hosted-thing')
        ->and($hosted->settingPath('color'))->toBe('settings.premium-host.hosted-thing.color')
        ->and($hosted->settingPath('settings.addons.hosted-thing.color'))->toBe('settings.premium-host.hosted-thing.color')
        ->and($hosted->settingPath())->toBe('settings.premium-host.hosted-thing')
        ->and($hosted->storageKey())->toBe('site_reviews_premium_host') // the HOST's row
        ->and($host->storageKey())->toBe('site_reviews_premium_host');
});

test('a hosted addon\'s paths remap into the host\'s merged file tree', function () {
    [$host, $hosted] = registerHostedFixture();
    $base = trailingslashit(dirname($host->file));

    expect($hosted->path('plugin/Integrations'))->toBe($base.'plugin/HostedThing/Integrations')
        ->and($hosted->path('config/settings.php'))->toBe($base.'config/settings/hosted-thing.php')
        ->and($hosted->path('config/forms/anything.php'))->toBe($base.'config/forms/anything.php') // type-first, addon-authored: identity
        ->and($hosted->path('views/settings.php'))->toBe($base.'views/hosted-thing/settings.php')
        ->and($hosted->path('templates/alert.php'))->toBe($base.'templates/hosted-thing/alert.php')
        ->and($hosted->path('assets/site-reviews-hosted-addon-admin.css'))->toBe($base.'assets/standalone/hosted-thing/site-reviews-hosted-addon-admin.css')
        ->and($hosted->path('assets/anything/nested.js'))->toBe($base.'assets/standalone/hosted-thing/anything/nested.js')
        ->and($hosted->path('assets/js/anything.js'))->toBe($base.'assets/js/anything.js') // shared tree, unmapped
        ->and($hosted->path('assets/blocks/some_block'))->toBe($base.'assets/blocks/hosted-thing/some_block') // block metadata IS slug-mapped
        ->and($hosted->path('assets/blocks'))->toBe($base.'assets/blocks/hosted-thing') // the metadata-collection root maps too
        ->and($hosted->path('languages/x.mo'))->toBe($base.'languages/x.mo') // keyed by text domain, unmapped
        ->and($hosted->url('assets/site-reviews-hosted-addon.js'))->toContain('assets/standalone/hosted-thing/site-reviews-hosted-addon.js');
});

test('a hosted addon\'s storage path is its slug inside the host\'s row', function () {
    // storagePath() is the split()/compose() counterpart of settingsPath(): the host owns the
    // whole row, and each module's values sit under settings.{slug} inside it.
    [$host, $hosted] = registerHostedFixture();

    expect($hosted->storagePath())->toBe('settings.hosted-thing')
        ->and($host->storagePath())->toBe('settings');
});

test('an empty hosted path answers the host\'s base directory', function () {
    [$host, $hosted] = registerHostedFixture();

    expect($hosted->path(''))->toBe(trailingslashit(dirname($host->file)));
});

test('a module outside the premium namespace maps by its last namespace segment', function () {
    // The premium prefix is stripped when present; anything else falls back to the final
    // segment, so a module packaged under another vendor prefix still finds its own tree.
    [$host] = registerHostedFixture();
    $module = new \GeminiLabs\OutsideVendor\HostedThing\Application($host);

    expect($module->path('plugin/File.php'))
        ->toBe(trailingslashit(dirname($host->file)).'plugin/HostedThing/File.php');
});

test('but themePath never remaps — theme overrides survive the standalone-to-premium switch', function () {
    // The one path that MUST stay keyed to the addon id in every shape: customers' theme override
    // folders ({stylesheet}/{addon-id}/) would all silently stop applying otherwise. This was the
    // abandoned first premium attempt's bug.
    [, $hosted] = registerHostedFixture();

    expect($hosted->themePath('alert.php'))
        ->toBe(get_stylesheet_directory().'/site-reviews-hosted-addon/alert.php');
});

test('hosted settings store inside the host\'s row; the host mounts under its own slug', function () {
    registerHostedFixture();

    glsr(OptionManager::class)->set('settings.premium-host.hosted-thing.color', 'red');
    glsr(OptionManager::class)->set('settings.premium-host.hosted-thing.is_enabled', 'yes');

    $row = get_option('site_reviews_premium_host');
    expect($row['settings']['hosted-thing']['color'])->toBe('red')
        ->and($row['settings']['hosted-thing']['is_enabled'])->toBe('yes') // the reserved toggle key lives inside the subtree it gates
        ->and($row)->not->toHaveKey('features') // the old sibling key is gone
        ->and(coreRow()['settings'] ?? [])->not->toHaveKey('premium-host')
        ->and(coreRow()['settings']['addons'] ?? [])->not->toHaveKey('hosted-thing');

    // And it reads back through the composed view at its mount. The
    // standalone-era path is NOT an alias: a hosted addon's settings live
    // where they are mounted, and nothing rewrites a path on the way in.
    expect(glsr_get_option('premium-host.hosted-thing.color'))->toBe('red')
        ->and(glsr_get_option('addons.hosted-thing.color', 'MISS'))->toBe('MISS');
});

test('a hosted addon writes through its own mount', function () {
    registerHostedFixture();

    glsr(HostedAddon::class)->updateOption('color', 'blue');

    $row = get_option('site_reviews_premium_host');
    expect($row['settings']['hosted-thing']['color'])->toBe('blue')
        ->and(coreRow()['settings']['addons'] ?? [])->not->toHaveKey('hosted-thing');
});

test('the suppression guard admits a hosted addon in the Premium namespace', function () {
    // The premium bundle lists the addon ids it owns via this filter; standalone addons with those
    // ids are shelved, but the bundle's own modules pass because of their namespace.
    $filter = fn (array $ids): array => array_merge($ids, [HostedAddon::ID]);
    add_filter('site-reviews/site-reviews-premium', $filter);
    try {
        registerHostedFixture();
        expect(glsr()->addon(HostedAddon::ID))->toBe(HostedAddon::class)
            ->and(glsr()->retrieveAs('array', 'site-reviews-premium', []))->not->toContain(HostedAddon::class);
    } finally {
        remove_filter('site-reviews/site-reviews-premium', $filter);
    }
});

test('settingPath resolves a standalone-authored path against the addon mount', function () {
    // Standalone here: the fixture addon has no host, so the mount is the
    // standalone-era one and every accepted spelling answers it unchanged.
    // The hosted branch is exercised by the premium suite, which is the only
    // place an addon runs as a module.
    $addon = glsr(TestAddon::class);

    expect($addon->settingPath('settings.addons.test-addon.enabled'))
        ->toBe('settings.addons.test-addon.enabled')
        ->and($addon->settingPath('addons.test-addon.enabled'))
        ->toBe('settings.addons.test-addon.enabled')
        ->and($addon->settingPath('enabled'))
        ->toBe('settings.addons.test-addon.enabled')
        ->and($addon->settingPath())
        ->toBe('settings.addons.test-addon');
});

test('the core plugin resolves a settings path against the root mount', function () {
    expect(glsr()->settingPath('reviews.style'))->toBe('settings.reviews.style')
        ->and(glsr()->settingPath('settings.reviews.style'))->toBe('settings.reviews.style')
        ->and(glsr()->settingPath())->toBe('settings');
});
