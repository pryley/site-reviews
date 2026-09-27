<?php

use GeminiLabs\SiteReviews\Defaults\StylesFeaturesDefaults;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

beforeEach(function () {
    resetPluginState();
});

afterEach(function () {
    remove_all_filters('site-reviews/defaults/styles-features/defaults');
});

test('the plugin keeps no styles record of its own', function () {
    expect(glsr(StylesFeaturesDefaults::class)->defaults())->toBe([]);
});

test('a filter declares a record by its slug and the class that reads it', function () {
    add_filter('site-reviews/defaults/styles-features/defaults', fn (array $features) => $features + ['emails' => StylesFeaturesDefaults::class]);

    expect(glsr(StylesFeaturesDefaults::class)->defaults())->toBe(['emails' => StylesFeaturesDefaults::class]);
});

test('a slug that is not a key or a class that does not exist is left out', function () {
    add_filter('site-reviews/defaults/styles-features/defaults', fn () => [
        'Emails' => StylesFeaturesDefaults::class,
        'forms' => 'No\\Such\\ClassName',
        'themes' => StylesFeaturesDefaults::class,
        0 => StylesFeaturesDefaults::class,
    ]);

    expect(glsr(StylesFeaturesDefaults::class)->defaults())->toBe(['themes' => StylesFeaturesDefaults::class]);
});
