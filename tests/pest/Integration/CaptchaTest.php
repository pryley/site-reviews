<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Captcha;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The Captcha facade: which service is on, what config the frontend gets, and
 * where the widget goes. The validators themselves are covered in
 * CaptchaValidatorTest; this is the wiring around them.
 */

beforeEach(fn () => resetPluginState());

function captchaOn(string $integration): void
{
    glsr(OptionManager::class)->set('settings.forms.captcha.integration', $integration);
    glsr(OptionManager::class)->set('settings.forms.captcha.usage', 'all');
}

test('an enabled captcha hands the frontend its validator config and a container to mount in', function () {
    captchaOn('procaptcha');

    $config = glsr(Captcha::class)->config();
    expect($config)->toHaveKey('sitekey')
        ->and($config['type'])->toBe('procaptcha')
        ->and($config['token_field'])->toBe('procaptcha-response');

    expect(glsr(Captcha::class)->container())->toContain('glsr-captcha-holder');
});

test('a captcha integration with no validator class is no captcha at all', function () {
    // A stale setting from a removed addon must not fatal the review form.
    captchaOn('bogus-service');

    expect(glsr(Captcha::class)->config())->toBe([])
        ->and(glsr(Captcha::class)->isEnabled())->toBeTrue(); // enabled, but unusable — config() is the guard
});

test('a disabled captcha has no config and no container', function () {
    expect(glsr(Captcha::class)->config())->toBe([])
        ->and(glsr(Captcha::class)->container())->toBe('');
});

test('recaptcha positions by badge, everything else by placement', function () {
    captchaOn('recaptcha_v3');
    expect(glsr(Captcha::class)->position())->toBe('bottomleft');

    captchaOn('procaptcha');
    expect(glsr(Captcha::class)->position())->toBe('below');
});
