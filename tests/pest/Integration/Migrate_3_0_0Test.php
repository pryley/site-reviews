<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Migrations\Migrate_3_0_0;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * v2 -> v3. The two versions keep their settings under their own option keys,
 * and everything this migration does is decided by what the v2 option holds:
 * the v2 values are flattened, mapped onto their v3 names, and written to the
 * v3 key. It touches no table but the options one.
 */

beforeEach(fn () => resetPluginState());

test('a site with no v2 settings is left alone', function () {
    update_option(OptionManager::databaseKey(3), ['settings' => ['general' => ['style' => 'twentysixteen']]]);

    expect((new Migrate_3_0_0())->run())->toBeTrue();

    expect(get_option(OptionManager::databaseKey(3)))
        ->toBe(['settings' => ['general' => ['style' => 'twentysixteen']]]);
});

test('a site with no v3 settings to migrate into is left alone', function () {
    // The v3 option is written by the v3 plugin on activation; without it there
    // is nothing to merge into, and the v2 option is not consumed.
    update_option(OptionManager::databaseKey(2), ['settings' => ['general' => ['notification' => 'default']]]);

    expect((new Migrate_3_0_0())->run())->toBeTrue();

    expect(get_option(OptionManager::databaseKey(3)))->toBeFalse();
});

test('the v2 settings are renamed onto their v3 keys', function () {
    update_option(OptionManager::databaseKey(2), oldSettings());
    update_option(OptionManager::databaseKey(3), [
        'settings' => [
            'general' => [
                'notification_email' => 'stale@example.org', // a mapped key, replaced
                'style' => 'twentysixteen', // an unmapped key, kept
            ],
        ],
    ]);

    expect((new Migrate_3_0_0())->run())->toBeTrue();

    $settings = get_option(OptionManager::databaseKey(3));
    expect(Arr::get($settings, 'settings.general.style'))->toBe('twentysixteen')
        ->and(Arr::get($settings, 'settings.general.notification_email'))->toBe('admin@example.org')
        ->and(Arr::get($settings, 'settings.general.notification_slack'))->toBe('https://hooks.example.org/abc')
        ->and(Arr::get($settings, 'settings.general.require.approval'))->toBe('yes')
        ->and(Arr::get($settings, 'settings.reviews.avatars'))->toBe('yes')
        ->and(Arr::get($settings, 'version_upgraded_from'))->toBe('2.9.0');
    // the empty v2 value is not carried over, and the v2 key itself is gone
    expect($settings['settings']['general'])->not->toHaveKey('require.login')
        ->and($settings['settings'])->not->toHaveKey('reviews-form');
});

test('the one notification setting becomes the list of notifications', function () {
    // v2 chose one of three; v3 keeps a list, so the chosen one is the only member.
    update_option(OptionManager::databaseKey(2), oldSettings([
        'settings.general.notification' => 'default',
    ]));
    update_option(OptionManager::databaseKey(3), ['settings' => ['general' => []]]);

    expect((new Migrate_3_0_0())->run())->toBeTrue();

    expect(Arr::get(get_option(OptionManager::databaseKey(3)), 'settings.general.notifications'))
        ->toBe(['admin']);
});

test('the required fields keep the two v3 always requires', function () {
    update_option(OptionManager::databaseKey(2), oldSettings([
        'settings.reviews-form.required' => ['content', '', 'email'],
    ]));
    update_option(OptionManager::databaseKey(3), ['settings' => ['general' => []]]);

    expect((new Migrate_3_0_0())->run())->toBeTrue();

    // array_filter() drops the empty value and keeps the keys, so the discarded
    // one leaves a gap; the two appended fields carry on from the last index.
    expect(Arr::get(get_option(OptionManager::databaseKey(3)), 'settings.submissions.required'))
        ->toBe([0 => 'content', 2 => 'email', 3 => 'rating', 4 => 'terms']);
});

test('a custom recaptcha is used everywhere, with its own keys', function () {
    update_option(OptionManager::databaseKey(2), oldSettings());
    update_option(OptionManager::databaseKey(3), ['settings' => ['general' => []]]);

    expect((new Migrate_3_0_0())->run())->toBeTrue();

    $settings = get_option(OptionManager::databaseKey(3));
    expect(Arr::get($settings, 'settings.submissions.recaptcha.integration'))->toBe('all')
        ->and(Arr::get($settings, 'settings.submissions.recaptcha.key'))->toBe('v2-site-key')
        ->and(Arr::get($settings, 'settings.submissions.recaptcha.secret'))->toBe('v2-secret-key')
        ->and(Arr::get($settings, 'settings.submissions.recaptcha.position'))->toBe('bottomright');
});

test('an invisible recaptcha takes its keys from that plugin instead', function () {
    // The Invisible reCaptcha plugin stores its own keys, and they win over the
    // ones Site Reviews was carrying: it was the integration doing the work.
    update_site_option('ic-settings', [
        'BadgePosition' => 'inline',
        'SecretKey' => 'ic-secret-key',
        'SiteKey' => 'ic-site-key',
    ]);
    update_option(OptionManager::databaseKey(2), oldSettings([
        'settings.reviews-form.recaptcha.integration' => 'invisible-recaptcha',
    ]));
    update_option(OptionManager::databaseKey(3), ['settings' => ['general' => []]]);

    expect((new Migrate_3_0_0())->run())->toBeTrue();

    $settings = get_option(OptionManager::databaseKey(3));
    expect(Arr::get($settings, 'settings.submissions.recaptcha.integration'))->toBe('all')
        ->and(Arr::get($settings, 'settings.submissions.recaptcha.key'))->toBe('ic-site-key')
        ->and(Arr::get($settings, 'settings.submissions.recaptcha.secret'))->toBe('ic-secret-key')
        ->and(Arr::get($settings, 'settings.submissions.recaptcha.position'))->toBe('inline');
});

test('the translated strings are carried over from v2', function () {
    update_option(OptionManager::databaseKey(2), oldSettings([
        'settings.strings' => [
            ['id' => 'abc123', 's1' => 'Write a Review', 'p1' => 'Schrijf een recensie'],
        ],
    ]));
    update_option(OptionManager::databaseKey(3), ['settings' => ['general' => []]]);

    expect((new Migrate_3_0_0())->run())->toBeTrue();

    expect(Arr::get(get_option(OptionManager::databaseKey(3)), 'settings.strings'))
        ->toBe([['id' => 'abc123', 's1' => 'Write a Review', 'p1' => 'Schrijf een recensie']]);
});

/**
 * A representative v2 option, given in flat notation and unflattened the way the
 * migration reads it back. Overrides are applied flat, so a test names the one
 * key it is about.
 */
function oldSettings(array $overrides = []): array
{
    return Arr::unflatten(array_merge([
        'settings.general.notification' => 'webhook',
        'settings.general.notification_email' => 'admin@example.org',
        'settings.general.require.approval' => 'yes',
        'settings.general.require.login' => '',
        'settings.general.webhook_url' => 'https://hooks.example.org/abc',
        'settings.reviews-form.recaptcha.integration' => 'custom',
        'settings.reviews-form.recaptcha.key' => 'v2-site-key',
        'settings.reviews-form.recaptcha.position' => 'bottomright',
        'settings.reviews-form.recaptcha.secret' => 'v2-secret-key',
        'settings.reviews-form.required' => ['content', 'email'],
        'settings.reviews.avatars.enabled' => 'yes',
        'version' => '2.9.0',
    ], $overrides));
}
