<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Migrations\Migrate_5_25_0\MigrateSettings;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * v4 -> v5 settings. reCAPTCHA stops being the only captcha and becomes one
 * integration among several, Trustalyze is removed, and the cached review
 * counts stop living in the settings.
 */

beforeEach(fn () => resetPluginState());

test('a site with no v4 settings is left alone', function () {
    expect(glsr(MigrateSettings::class)->run())->toBeTrue();

    expect(get_option(OptionManager::databaseKey(5)))->toBeFalse();
});

test('the v4 settings become the v5 settings when there are none yet', function () {
    update_option(OptionManager::databaseKey(4), [
        'settings' => ['general' => ['style' => 'twentysixteen']],
    ]);

    expect(glsr(MigrateSettings::class)->run())->toBeTrue();

    expect(Arr::get(get_option(OptionManager::databaseKey(5)), 'settings.general.style'))
        ->toBe('twentysixteen');
});

test('requiring registration is renamed, without overwriting an answer already given', function () {
    update_option(OptionManager::databaseKey(4), ['settings' => ['general' => []]]);
    update_option(OptionManager::databaseKey(5), [
        'settings' => ['general' => ['require' => ['login_register' => 'yes']]],
    ]);

    expect(glsr(MigrateSettings::class)->run())->toBeTrue();

    expect(Arr::get(get_option(OptionManager::databaseKey(5)), 'settings.general.require.register'))
        ->toBe('yes');
});

test('the new name wins when both are set', function () {
    update_option(OptionManager::databaseKey(4), ['settings' => ['general' => []]]);
    update_option(OptionManager::databaseKey(5), [
        'settings' => ['general' => ['require' => ['login_register' => 'yes', 'register' => 'no']]],
    ]);

    expect(glsr(MigrateSettings::class)->run())->toBeTrue();

    expect(Arr::get(get_option(OptionManager::databaseKey(5)), 'settings.general.require.register'))
        ->toBe('no');
});

test('a site using recaptcha gets the invisible v2 captcha integration', function () {
    update_option(OptionManager::databaseKey(4), ['settings' => ['general' => []]]);
    update_option(OptionManager::databaseKey(5), [
        'settings' => [
            'submissions' => [
                'recaptcha' => [
                    'integration' => 'custom',
                    'position' => 'bottomright',
                ],
            ],
        ],
    ]);

    expect(glsr(MigrateSettings::class)->run())->toBeTrue();

    $captcha = Arr::get(get_option(OptionManager::databaseKey(5)), 'settings.submissions.captcha');
    expect($captcha)->toBe([
        'integration' => 'recaptcha_v2_invisible',
        'position' => 'bottomright',
        'theme' => 'light',
        'usage' => 'custom',
    ]);
    // the reCAPTCHA-only keys are gone, and the v3 threshold has a default
    expect(Arr::get(get_option(OptionManager::databaseKey(5)), 'settings.submissions.recaptcha'))
        ->toBe('')
        ->and(Arr::get(get_option(OptionManager::databaseKey(5)), 'settings.submissions.recaptcha_v3.threshold'))
        ->toBe(0.5);
});

test('a site not using recaptcha gets no captcha, with the default position and usage', function () {
    update_option(OptionManager::databaseKey(4), ['settings' => ['general' => []]]);
    update_option(OptionManager::databaseKey(5), ['settings' => ['submissions' => []]]);

    expect(glsr(MigrateSettings::class)->run())->toBeTrue();

    expect(Arr::get(get_option(OptionManager::databaseKey(5)), 'settings.submissions.captcha'))
        ->toBe([
            'integration' => '',
            'position' => 'bottomleft',
            'theme' => 'light',
            'usage' => 'all',
        ]);
});

test('trustalyze and the cached review count are dropped', function () {
    update_option(OptionManager::databaseKey(4), ['settings' => ['general' => []]]);
    update_option(OptionManager::databaseKey(5), [
        'last_review_count' => 12,
        'settings' => [
            'addons' => ['trustalyze' => 'yes'],
            'general' => [
                'style' => 'default',
                'trustalyze' => 'yes',
                'trustalyze_email' => 'someone@example.org',
                'trustalyze_serial' => 'ABC-123',
            ],
        ],
    ]);

    expect(glsr(MigrateSettings::class)->run())->toBeTrue();

    $settings = get_option(OptionManager::databaseKey(5));
    expect($settings)->not->toHaveKey('last_review_count')
        ->and($settings['settings'])->not->toHaveKey('addons')
        ->and($settings['settings']['general'])->toBe(['style' => 'default']);
});
