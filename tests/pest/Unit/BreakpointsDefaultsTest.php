<?php

use GeminiLabs\SiteReviews\Defaults\BreakpointsDefaults;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

beforeEach(function () {
    resetPluginState();
});

afterEach(function () {
    remove_all_filters('site-reviews/defaults/breakpoints/defaults');
});

test('the scale is the width each device starts at', function () {
    expect(glsr(BreakpointsDefaults::class)->defaults())->toBe([
        'mobile' => 0,
        'tablet' => 600,
        'desktop' => 960,
    ]);
});

test('a filter changes the scale for everything that reads it', function () {
    add_filter('site-reviews/defaults/breakpoints/defaults', fn (array $widths) => array_merge($widths, ['tablet' => '700']));

    expect(glsr(BreakpointsDefaults::class)->defaults()['tablet'])->toBe(700);
});

test('a filtered scale is repaired, not rejected', function () {
    // An extra key, a missing key, a smallest device that does not start
    // at zero, and the widths in the wrong order.
    add_filter('site-reviews/defaults/breakpoints/defaults', fn () => [
        'desktop' => 500,
        'mobile' => 320,
        'phablet' => 480,
    ]);

    expect(glsr(BreakpointsDefaults::class)->defaults())->toBe([
        'mobile' => 0,
        'tablet' => 500,
        'desktop' => 600,
    ]);
});

test('restrict() gives the scale whatever it is handed', function () {
    expect(glsr(BreakpointsDefaults::class)->restrict(['tablet' => 640, 'wide' => 1600]))->toBe([
        'mobile' => 0,
        'tablet' => 640,
        'desktop' => 960,
    ]);
});
