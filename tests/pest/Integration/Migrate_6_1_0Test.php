<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Migrations\Migrate_6_1_0;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * A one-off cleanup of the v6 settings: anything that is not a setting the
 * running plugin declares is dropped, so that settings left behind by removed
 * features stop being written back.
 *
 * It is gated on the plugin being a v6, which the running plugin is not — it
 * reads its own version out of the plugin header. `atVersion6()` fakes that
 * for the duration of a test, the way SettingFormTest and AddonHookPrefixTest
 * reach the same singleton's memoised properties.
 */

beforeEach(fn () => resetPluginState());

test('the cleanup does not run on a plugin that is no longer a v6', function () {
    $settings = ['settings' => ['general' => ['gone_in_v7' => 'kept']]];
    update_option(OptionManager::databaseKey(6), $settings);

    expect((new Migrate_6_1_0())->run())->toBeTrue();

    expect(get_option(OptionManager::databaseKey(6)))->toBe($settings);
});

test('a site with no v6 settings has nothing to clean', function () {
    atVersion6(function () {
        expect((new Migrate_6_1_0())->run())->toBeTrue();

        expect(get_option(OptionManager::databaseKey(6)))->toBeFalse();
    });
});

test('a setting the plugin no longer declares is dropped', function () {
    atVersion6(function () {
        update_option(OptionManager::databaseKey(6), [
            'settings' => [
                'general' => [
                    'style' => 'twentysixteen', // declared in config/settings.php
                    'rebusify' => 'yes',        // removed two versions ago
                ],
            ],
            'version' => '6.0.0', // outside `settings`, so untouched
        ]);

        expect((new Migrate_6_1_0())->run())->toBeTrue();

        $settings = get_option(OptionManager::databaseKey(6));
        expect($settings['settings']['general'])->toBe(['style' => 'twentysixteen'])
            ->and($settings['version'])->toBe('6.0.0');
    });
});

test('addon, licence and translation settings are kept without being declared', function () {
    // They belong to an addon or to the translator, so the plugin's own defaults
    // cannot list them, and dropping them would be dropping somebody else's data.
    atVersion6(function () {
        update_option(OptionManager::databaseKey(6), [
            'settings' => [
                'addons' => ['woocommerce' => ['enabled' => 'yes']],
                'licenses' => ['site-reviews-filters' => 'a-licence-key'],
                'strings' => [['id' => 'abc123', 's1' => 'Write a Review']],
            ],
        ]);

        expect((new Migrate_6_1_0())->run())->toBeTrue();

        expect(get_option(OptionManager::databaseKey(6))['settings'])->toBe([
            'addons' => ['woocommerce' => ['enabled' => 'yes']],
            'licenses' => ['site-reviews-filters' => 'a-licence-key'],
            'strings' => [['id' => 'abc123', 's1' => 'Write a Review']],
        ]);
    });
});

test('settings that are all unrecognised are left alone rather than emptied', function () {
    // Nothing survives the filter, and rather than write an empty settings array
    // the migration leaves the option as it found it.
    atVersion6(function () {
        $settings = ['settings' => ['general' => ['rebusify' => 'yes']]];
        update_option(OptionManager::databaseKey(6), $settings);

        expect((new Migrate_6_1_0())->run())->toBeTrue();

        expect(get_option(OptionManager::databaseKey(6)))->toBe($settings);
    });
});

/**
 * Runs the callback with the plugin reporting itself as a v6. The version is
 * read from the plugin header into a protected property once per request, so
 * the property is what a test has to reach; it is put back afterwards.
 */
function atVersion6(callable $callback): void
{
    $property = new ReflectionProperty(glsr(), 'version');
    $version = $property->getValue(glsr());
    $property->setValue(glsr(), '6.1.0');
    try {
        $callback();
    } finally {
        $property->setValue(glsr(), $version);
    }
}
