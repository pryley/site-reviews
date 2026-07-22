<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Migrations\Migrate_6_2_0;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The WooCommerce addon's experiment settings become real ones. Four guards
 * decide whether anything happens at all, and the addon settings are rewritten
 * in place — the move to `integrations` is the v7.2.0 migration's job.
 */

beforeEach(fn () => resetPluginState());

test('a site with no v6 settings is left alone', function () {
    expect((new Migrate_6_2_0())->run())->toBeTrue();

    expect(get_option(OptionManager::databaseKey(6)))->toBeFalse();
});

test('a site without the woocommerce addon settings is left alone', function () {
    update_option(OptionManager::databaseKey(6), ['settings' => ['general' => ['style' => 'default']]]);

    expect((new Migrate_6_2_0())->run())->toBeTrue();

    expect(get_option(OptionManager::databaseKey(6)))
        ->toBe(['settings' => ['general' => ['style' => 'default']]]);
});

test('a site that already has woocommerce integration settings is left alone', function () {
    // The integration settings are where this migration is heading; a site that
    // has them has already been through it, or past it.
    $settings = [
        'settings' => [
            'addons' => ['woocommerce' => ['experiments' => 'yes']],
            'integrations' => ['woocommerce' => ['wp_comments' => 'no']],
        ],
    ];
    update_option(OptionManager::databaseKey(6), $settings);

    expect((new Migrate_6_2_0())->run())->toBeTrue();

    expect(get_option(OptionManager::databaseKey(6)))->toBe($settings);
});

test('an active wp_comments experiment becomes the setting, and the experiment goes', function () {
    update_option(OptionManager::databaseKey(6), [
        'settings' => [
            'addons' => [
                'woocommerce' => [
                    'enabled' => 'yes',
                    'experiment' => ['wp_comments' => 'active'],
                    'experiments' => 'yes',
                ],
            ],
        ],
    ]);

    expect((new Migrate_6_2_0())->run())->toBeTrue();

    expect(Arr::get(get_option(OptionManager::databaseKey(6)), 'settings.addons.woocommerce'))
        ->toBe(['enabled' => 'yes', 'wp_comments' => 'yes']);
});

test('an experiment that was never switched on leaves no setting behind', function () {
    update_option(OptionManager::databaseKey(6), [
        'settings' => [
            'addons' => [
                'woocommerce' => [
                    'enabled' => 'yes',
                    'experiment' => ['wp_comments' => 'active'],
                    'experiments' => '', // the experiments themselves were off
                ],
            ],
        ],
    ]);

    expect((new Migrate_6_2_0())->run())->toBeTrue();

    expect(Arr::get(get_option(OptionManager::databaseKey(6)), 'settings.addons.woocommerce'))
        ->toBe(['enabled' => 'yes']);
});
