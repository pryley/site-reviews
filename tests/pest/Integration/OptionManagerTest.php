<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Premium\Host\Application as PremiumHostAddon;
use GeminiLabs\SiteReviews\Premium\HostedThing\Application as PremiumHostedAddon;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;
use function GeminiLabs\SiteReviews\Tests\unregisterAddons;

require_once glsr()->path('tests/pest/fixtures/site-reviews-premium-host/plugin/Application.php');
require_once glsr()->path('tests/pest/fixtures/site-reviews-premium-host/plugin/Hooks.php');
require_once glsr()->path('tests/pest/fixtures/site-reviews-hosted-addon/plugin/Application.php');
require_once glsr()->path('tests/pest/fixtures/site-reviews-hosted-addon/plugin/Hooks.php');

/*
 * The plugin's settings store. Everything the site owner configures lives in ONE option, read
 * through an in-memory copy on the Application. This pins the parts the ordinary get()/set() paths
 * do not reach: the magic getX() casts, the version-key lookups, and the two routines that keep a
 * site's own data intact across a plugin update — mergeDefaults (folds in settings a new version
 * introduced) and clean/restoreOrphanedSettings (does not throw away settings whose addon is, for
 * now, deactivated).
 */

beforeEach(fn () => resetPluginState());

test('the magic getX methods reject a name that is not a real cast', function () {
    $options = glsr(OptionManager::class);

    expect(fn () => $options->fetchSomething('general.x'))->toThrow(\BadMethodCallException::class) // not get*
        ->and(fn () => $options->getBanana('general.x'))->toThrow(\BadMethodCallException::class);   // get, but no such cast
});

test('a version with no settings key returns an empty string', function () {
    // version 0 never existed, so it maps to no option name.
    expect(OptionManager::databaseKey(0))->toBe('');
});

test('replacing the settings with nothing is refused', function () {
    expect(glsr(OptionManager::class)->replace([]))->toBeFalse();
});

test('all() rebuilds the settings when the in-memory copy has been emptied', function () {
    glsr()->store('settings', []); // as if nothing were loaded yet

    expect(glsr(OptionManager::class)->all())
        ->toBeArray()
        ->not->toBeEmpty(); // reset() repopulated it from the saved option / defaults
});

test('reset() clears an empty settings option rather than leaving a blank row behind', function () {
    delete_option(OptionManager::databaseKey());

    $settings = glsr(OptionManager::class)->reset();

    expect($settings)->toBeArray();
    // the blank option is removed rather than left as an empty row for every query to read
    expect(get_option(OptionManager::databaseKey()))->toBeFalse();
});

test('previous() finds the settings a site was running before this version', function () {
    // It walks the older version keys; the v7+ key is the same option as the current one, so a site
    // that has any settings at all has a "previous" to hand back.
    $previous = glsr(OptionManager::class)->previous();

    expect($previous)->toHaveKey('settings');
});

test('mergeDefaults folds a newly introduced setting into what is already saved', function () {
    // The update path: a new plugin version ships a setting the saved options have never seen, and it
    // has to appear with its default rather than read as empty forever.
    glsr(OptionManager::class)->mergeDefaults([
        'settings' => ['general' => ['a_brand_new_setting' => 'a-default-value']],
    ]);

    expect(glsr_get_option('general.a_brand_new_setting'))->toBe('a-default-value');
});

test('clean() keeps the settings of an addon whose defaults are not currently loaded', function () {
    // clean() flattens the submitted settings against the defaults, which would drop anything the
    // defaults do not know about — so restoreOrphanedSettings puts the addon/integration/licence
    // settings back, because a deactivated addon must not lose its configuration.
    $cleaned = glsr(OptionManager::class)->clean([
        'settings' => [
            'general' => ['require' => ['approval' => 'no']],           // a real setting, kept as-is
            'addons' => ['a-deactivated-addon' => ['enabled' => 'yes']], // orphaned addon
            'integrations' => ['a-dead-integration' => ['on' => 'yes']], // orphaned integration
            'licenses' => ['a-dead-addon' => 'a-licence-key'],           // orphaned licence
            'strings' => [['id' => 'x', 's1' => 'a', 's2' => 'b']],
        ],
    ]);

    expect($cleaned['settings']['addons'])->toHaveKey('a-deactivated-addon')          // each orphan survived
        ->and($cleaned['settings']['integrations'])->toHaveKey('a-dead-integration')
        ->and($cleaned['settings']['licenses'])->toHaveKey('a-dead-addon')
        ->and($cleaned['settings']['general']['require']['approval'])->toBe('no');
});

test('clean() keeps a host\'s stored values when their feature is disabled', function () {
    // A disabled premium feature never registers, so its settings config is not loaded and its
    // keys are absent from the defaults — clean() would drop them. The host loop in
    // restoreOrphanedSettings puts them back key-by-key.
    glsr()->register(PremiumHostAddon::class);
    glsr()->register(PremiumHostedAddon::class, glsr(PremiumHostAddon::class));
    try {
        $cleaned = glsr(OptionManager::class)->clean([
            'settings' => [
                'premium-host' => ['hosted-thing' => ['color' => 'red', 'is_enabled' => 'no']],
            ],
        ]);

        expect($cleaned['settings']['premium-host']['hosted-thing']['color'])->toBe('red')
            ->and($cleaned['settings']['premium-host']['hosted-thing']['is_enabled'])->toBe('no');
    } finally {
        unregisterAddons(PremiumHostAddon::ID, PremiumHostedAddon::ID);
    }
});
