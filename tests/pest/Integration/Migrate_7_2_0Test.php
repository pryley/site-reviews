<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Migrations\Migrate_7_2_0;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * WooCommerce stopped being an addon and became an integration, so its settings
 * move from one branch of the current settings option to the other.
 */

beforeEach(fn () => resetPluginState());

test('the woocommerce addon settings become integration settings', function () {
    $settings = get_option(OptionManager::databaseKey());
    $settings['settings']['addons'] = [
        'woocommerce' => ['enabled' => 'yes', 'wp_comments' => 'yes'],
        'filters' => ['enabled' => 'yes'],
    ];
    update_option(OptionManager::databaseKey(), $settings);

    expect((new Migrate_7_2_0())->run())->toBeTrue();

    $migrated = get_option(OptionManager::databaseKey());
    expect(Arr::get($migrated, 'settings.integrations.woocommerce'))
        ->toBe(['enabled' => 'yes', 'wp_comments' => 'yes'])
        ->and(Arr::get($migrated, 'settings.addons'))
        ->toBe(['filters' => ['enabled' => 'yes']]); // the other addons stay addons
});

test('a site without the woocommerce addon settings is written back unchanged', function () {
    // The integration settings already have defaults, and a site that never had
    // the addon must not have them replaced by an empty addon branch.
    $before = get_option(OptionManager::databaseKey());
    expect($before['settings'])->not->toHaveKey('addons');

    expect((new Migrate_7_2_0())->run())->toBeTrue();

    expect(get_option(OptionManager::databaseKey()))->toBe($before);
});
