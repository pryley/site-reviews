<?php

use GeminiLabs\SiteReviews\Addons\Compat;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Addons\Compat sorts old addons at boot: retired ones are shelved, ones
 * without an Update URI header go into compatibility mode, licensed ones are
 * recorded for the licensing screen. The fixtures in tests/pest/fixtures are
 * class + main-file pairs shaped exactly as Compat::register() walks them.
 */

beforeEach(function () {
    resetPluginState();
    require_once glsr()->path('tests/pest/fixtures/site-reviews-gamipress/plugin/Application.php');
    require_once glsr()->path('tests/pest/fixtures/site-reviews-compat-addon/plugin/Application.php');
});

test('a retired addon is shelved and never registered', function () {
    try {
        glsr(Compat::class)->register(\GeminiLabs\SiteReviews\Tests\Fixtures\Gamipress\Application::class);

        expect(glsr()->retrieveAs('array', 'retired'))
            ->toContain(\GeminiLabs\SiteReviews\Tests\Fixtures\Gamipress\Application::class);
        expect(glsr()->retrieveAs('array', 'compat'))->toBe([])
            ->and(glsr()->retrieveAs('array', 'licensed'))->toBe([]);
    } finally {
        glsr()->discard('retired');
    }
});

test('an addon without an Update URI is put into compatibility mode', function () {
    try {
        glsr(Compat::class)->register(\GeminiLabs\SiteReviews\Tests\Fixtures\CompatAddon\Application::class);

        $compat = glsr()->retrieveAs('array', 'compat');
        expect($compat)->toHaveKey('site-reviews-compat-addon')
            ->and($compat['site-reviews-compat-addon'])->toEndWith('site-reviews-compat-addon.php');
        expect(glsr()->retrieveAs('array', 'licensed'))->toBe([]); // LICENSED is not true
    } finally {
        glsr()->discard('compat');
    }
});

test('a class that does not exist, or has no main file, is silently skipped', function () {
    glsr(Compat::class)->register('GeminiLabs\NoSuchAddon\Application');
    glsr(Compat::class)->register(Compat::class); // real class, no addon layout around it

    expect(glsr()->retrieveAs('array', 'retired'))->toBe([])
        ->and(glsr()->retrieveAs('array', 'compat'))->toBe([]);
});
