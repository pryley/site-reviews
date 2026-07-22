<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Migrations\Migrate_7_3_0;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The CAPTCHA badge position: one setting renamed from `position` to `badge`,
 * and its `inline` value split into two, of which the old one becomes the lower.
 */

beforeEach(fn () => resetPluginState());

test('the badge position is taken from the position setting', function () {
    update_option(OptionManager::databaseKey(), captchaSettings('bottomright'));

    expect((new Migrate_7_3_0())->run())->toBeTrue();

    expect(Arr::get(get_option(OptionManager::databaseKey()), 'settings.forms.captcha.badge'))
        ->toBe('bottomright');
});

test('inline becomes the lower of the two inline placements', function () {
    update_option(OptionManager::databaseKey(), captchaSettings('inline'));

    expect((new Migrate_7_3_0())->run())->toBeTrue();

    expect(Arr::get(get_option(OptionManager::databaseKey()), 'settings.forms.captcha.badge'))
        ->toBe('inline_below');
});

test('a site that never chose a position keeps the badge it has', function () {
    expect((new Migrate_7_3_0())->run())->toBeTrue();

    expect(Arr::get(get_option(OptionManager::databaseKey()), 'settings.forms.captcha.badge'))
        ->toBe('bottomleft'); // the default, untouched
});

/**
 * The current settings with the pre-7.3.0 CAPTCHA position in them.
 */
function captchaSettings(string $position): array
{
    $settings = get_option(OptionManager::databaseKey());
    $settings['settings']['forms']['captcha']['position'] = $position;
    return $settings;
}
