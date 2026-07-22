<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Migrations\Migrate_6_10_2;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * One removal from the v6 settings: the schema integration's list of types,
 * which stopped being a setting. The v6 option is the whole of its input.
 */

beforeEach(fn () => resetPluginState());

test('a site with no v6 settings is left alone', function () {
    expect((new Migrate_6_10_2())->run())->toBeTrue();

    expect(get_option(OptionManager::databaseKey(6)))->toBeFalse();
});

test('the schema integration types are removed, and nothing else is', function () {
    update_option(OptionManager::databaseKey(6), [
        'settings' => [
            'schema' => [
                'integration' => [
                    'types' => ['Product', 'LocalBusiness'],
                    'woocommerce' => 'yes',
                ],
                'type' => ['default' => 'LocalBusiness'],
            ],
        ],
    ]);

    expect((new Migrate_6_10_2())->run())->toBeTrue();

    $settings = get_option(OptionManager::databaseKey(6));
    expect($settings['settings']['schema']['integration'])->toBe(['woocommerce' => 'yes'])
        ->and(Arr::get($settings, 'settings.schema.type.default'))->toBe('LocalBusiness');
});
