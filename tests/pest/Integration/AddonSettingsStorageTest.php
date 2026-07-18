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
 * file tree, and settings store inside the HOST's row, the host's own settings
 * occupying the reserved "features" subtree.
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
        ->and(TestAddon::databaseKey())->toBe('site_reviews_test_addon');
});

/*
 * Split on write.
 */

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
        ->and($settings)->not->toHaveKey('settings.short_key');
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

test('a hosted addon\'s paths remap into the host\'s merged file tree', function () {
    [$host, $hosted] = registerHostedFixture();
    $base = trailingslashit(dirname($host->file));

    expect($hosted->path('plugin/Integrations'))->toBe($base.'plugin/HostedThing/Integrations')
        ->and($hosted->path('config/settings.php'))->toBe($base.'config/settings/hosted-thing.php')
        ->and($hosted->path('config/forms/anything.php'))->toBe($base.'config/forms/anything.php') // type-first, addon-authored: identity
        ->and($hosted->path('views/settings.php'))->toBe($base.'views/hosted-thing/settings.php')
        ->and($hosted->path('templates/alert.php'))->toBe($base.'templates/hosted-thing/alert.php')
        ->and($hosted->path('assets/site-reviews-hosted-addon-admin.css'))->toBe($base.'assets/hosted-thing/site-reviews-hosted-addon-admin.css')
        ->and($hosted->path('assets/anything/nested.js'))->toBe($base.'assets/hosted-thing/anything/nested.js')
        ->and($hosted->path('languages/x.mo'))->toBe($base.'languages/x.mo') // keyed by text domain, unmapped
        ->and($hosted->url('assets/site-reviews-hosted-addon.js'))->toContain('assets/hosted-thing/site-reviews-hosted-addon.js');
});

test('but themePath never remaps — theme overrides survive the standalone-to-premium switch', function () {
    // The one path that MUST stay keyed to the addon id in every shape: customers' theme override
    // folders ({stylesheet}/{addon-id}/) would all silently stop applying otherwise. This was the
    // abandoned first premium attempt's bug.
    [, $hosted] = registerHostedFixture();

    expect($hosted->themePath('alert.php'))
        ->toBe(get_stylesheet_directory().'/site-reviews-hosted-addon/alert.php');
});

test('hosted settings store inside the host\'s row; the host\'s own settings in "features"', function () {
    registerHostedFixture();

    glsr(OptionManager::class)->set('settings.addons.hosted-thing.color', 'red');
    glsr(OptionManager::class)->set('settings.addons.premium-host.hosted-thing', 'yes');

    $row = get_option('site_reviews_premium_host');
    expect($row['settings']['hosted-thing']['color'])->toBe('red')
        ->and($row['settings']['features']['hosted-thing'])->toBe('yes') // host auto-marked as host
        ->and($row['settings'])->not->toHaveKey('color') // host settings never clobber module subtrees
        ->and(coreRow()['settings']['addons'] ?? [])->not->toHaveKey('hosted-thing')
        ->and(coreRow()['settings']['addons'] ?? [])->not->toHaveKey('premium-host');

    // And both read back through the composed view.
    expect(glsr_get_option('addons.hosted-thing.color'))->toBe('red')
        ->and(glsr_get_option('addons.premium-host.hosted-thing'))->toBe('yes');
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
