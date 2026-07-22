<?php

use GeminiLabs\SiteReviews\Migrations\Migrate_8_0_0\MigrateElementor;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\protectedMethod;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The Elementor widget settings, renamed for v8. Two halves, and only one of
 * them belongs to Site Reviews:
 *
 *   - the ELEMENT TRANSFORMERS, which take an element array and give one back.
 *     They are the migration, and they are tested here, element by element.
 *   - the WALK, which asks Elementor for each document, its element data and
 *     its data iterator, and hands the result back to Elementor to save. Every
 *     value in it comes from Elementor, and the stubs have empty bodies (see
 *     tests/pest/README.md — a test that asserts against a body we invented
 *     proves nothing about the real plugin), so it is left uncovered.
 */

beforeEach(fn () => resetPluginState());

test('a site with no elementor widgets migrates nothing', function () {
    createPost(); // no _elementor_data anywhere

    expect(glsr(MigrateElementor::class)->run())->toBeFalse();
});

test('only a site reviews widget with settings is migrated', function () {
    $shouldMigrate = protectedMethod(MigrateElementor::class, 'shouldMigrate');
    $migration = glsr(MigrateElementor::class);

    expect($shouldMigrate->invoke($migration, [
        'elType' => 'widget',
        'widgetType' => 'site_reviews',
        'settings' => ['alignment' => 'left'],
    ]))->toBeTrue();

    expect($shouldMigrate->invoke($migration, ['elType' => 'section']))->toBeFalse()
        ->and($shouldMigrate->invoke($migration, [
            'elType' => 'widget',
            'widgetType' => 'heading',
            'settings' => ['alignment' => 'left'],
        ]))->toBeFalse()
        ->and($shouldMigrate->invoke($migration, [
            'elType' => 'widget',
            'widgetType' => 'site_reviews',
            'settings' => [],
        ]))->toBeFalse();
});

test('an element that is not ours is handed back exactly as it came', function () {
    $element = ['elType' => 'widget', 'widgetType' => 'heading', 'settings' => ['alignment' => 'left']];

    expect(migrateElement($element))->toBe($element);
});

test('the custom assignment fields are folded into the fields they belonged to', function () {
    $element = migrateElement([
        'elType' => 'widget',
        'widgetType' => 'site_reviews',
        'settings' => [
            'assigned_posts' => 'custom',
            'assigned_posts_custom' => '12,13',
            'assigned_users' => 'author',
            'assigned_users_custom' => '99', // not chosen, so discarded
        ],
    ]);

    expect($element['settings'])->toBe([
        'assigned_posts' => '12,13',
        'assigned_users' => 'author',
    ]);
});

test('the checkbox fields become one comma separated value each', function () {
    $element = migrateElement([
        'elType' => 'widget',
        'widgetType' => 'site_reviews',
        'settings' => [
            'hide-rating' => 'yes',
            'hide-title' => 'yes',
            'hide-date' => '', // not ticked
            'filter-rating' => 'yes', // the Review Filters addon
        ],
    ]);

    // Only a ticked box is collected and removed; an unticked one is left where
    // it is, which costs nothing — v8 reads the list, not the individual keys.
    expect($element['settings'])->toBe([
        'hide-date' => '',
        'hide' => 'rating,title',
        'filters' => 'rating',
    ]);
});

test('the style keys are renamed, per breakpoint', function () {
    $element = migrateElement([
        'elType' => 'widget',
        'widgetType' => 'site_reviews',
        'settings' => [
            'alignment' => 'center',
            'rating_size' => 24,
            'rating_size_tablet' => 20,
            'rating_size_mobile' => 16,
            'something_else' => 'kept',
        ],
    ]);

    expect($element['settings'])->toBe([
        'style_text_align' => 'center',
        'style_rating_size' => 24,
        'style_rating_size_tablet' => 20,
        'style_rating_size_mobile' => 16,
        'something_else' => 'kept',
    ]);
});

test('the summary widget keeps its bar colour alongside its rating colour', function () {
    $element = migrateElement([
        'elType' => 'widget',
        'widgetType' => 'site_reviews_summary',
        'settings' => [
            'rating_color' => '#ff0000',
            'rating_color_mobile' => '#00ff00',
        ],
    ]);

    expect($element['settings'])->toBe([
        'style_rating_color' => '#ff0000',
        'style_bar_color' => '#ff0000',
        'style_rating_color_mobile' => '#00ff00',
        'style_bar_color_mobile' => '#00ff00',
    ]);
});

test('another widget keeps only its rating colour', function () {
    $element = migrateElement([
        'elType' => 'widget',
        'widgetType' => 'site_reviews',
        'settings' => ['rating_color' => '#ff0000'],
    ]);

    expect($element['settings'])->toBe(['style_rating_color' => '#ff0000']);
});

test('the global values are renamed by the same rules', function () {
    // __globals__ is Elementor's parallel map of the same setting keys, so it
    // is walked with the same renaming and not flattened into the settings.
    $element = migrateElement([
        'elType' => 'widget',
        'widgetType' => 'site_reviews',
        'settings' => [
            '__globals__' => ['rating_color' => 'globals?id=primary'],
            'alignment' => 'left',
        ],
    ]);

    expect($element['settings'])->toBe([
        '__globals__' => ['style_rating_color' => 'globals?id=primary'],
        'style_text_align' => 'left',
    ]);
});

// NOTE (ceiling): the run() loop body (lines 44-58) reads a document, its element data
// and its data iterator back from Elementor, which the signature-only stub cannot
// answer — \Elementor\Plugin::$instance is null. Line 28's `!class_exists` return is
// unreachable for the opposite reason: the stub is loaded, so the class always exists.

function migrateElement(array $element): array
{
    return protectedMethod(MigrateElementor::class, 'migrateElement')
        ->invoke(glsr(MigrateElementor::class), $element);
}
