<?php

use GeminiLabs\SiteReviews\Defaults\EditorPaletteDefaults;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

beforeEach(function () {
    resetPluginState();
});

afterEach(function () {
    remove_all_filters('site-reviews/defaults/editor-palette/defaults');
});

test('the plugin offers no palette group of its own', function () {
    expect(glsr(EditorPaletteDefaults::class)->defaults())->toBe([]);
});

test('a filter adds a group every addon editor reads', function () {
    add_filter('site-reviews/defaults/editor-palette/defaults', fn (array $groups) => [...$groups, [
        'colors' => [['color' => '#3c79f0', 'name' => 'Blue', 'slug' => 'premium-blue']],
        'name' => 'Site Reviews Premium',
    ]]);

    expect(glsr(EditorPaletteDefaults::class)->defaults())->toBe([[
        'colors' => [['color' => '#3c79f0', 'name' => 'Blue', 'slug' => 'premium-blue']],
        'name' => 'Site Reviews Premium',
    ]]);
});

test('a group without a name or a usable colour is left out, and so is a colour missing a part', function () {
    add_filter('site-reviews/defaults/editor-palette/defaults', fn () => [
        ['colors' => [['color' => '#000', 'name' => 'Black', 'slug' => 'black']], 'name' => ''],
        ['colors' => [['color' => '', 'name' => 'Blank', 'slug' => 'blank']], 'name' => 'Empty'],
        ['colors' => [['color' => '#fff', 'name' => 'White', 'slug' => 'White Shade'], ['color' => '#f00', 'slug' => 'red']], 'name' => 'Kept'],
        'not a group',
    ]);

    expect(glsr(EditorPaletteDefaults::class)->defaults())->toBe([[
        'colors' => [['color' => '#fff', 'name' => 'White', 'slug' => 'whiteshade']],
        'name' => 'Kept',
    ]]);
});
