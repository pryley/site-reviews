<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Defaults\StyleClassesDefaults;
use GeminiLabs\SiteReviews\Defaults\StyleValidationDefaults;
use GeminiLabs\SiteReviews\Modules\Style;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * What each plugin style gives a form, pinned in tests/pest/fixtures/style-matrix.php.
 * A change to the fixture is a change to the markup of that style.
 */

const STYLE_MATRIX_VIEWS = [
    'templates/form/field',
    'templates/form/field-description',
    'templates/form/field-errors',
    'templates/form/field-label',
    'templates/form/field_checkbox',
    'templates/form/field_radio',
    'templates/form/field_range',
    'templates/form/field_select',
    'templates/form/field_text',
    'templates/form/field_toggle',
    'templates/form/response',
    'templates/form/submit-button',
    'templates/form/type-checkbox',
    'templates/form/type-checkbox_rating',
    'templates/form/type-radio',
    'templates/form/type-range',
    'templates/form/type-toggle',
    'templates/load-more-button',
    'templates/pagination',
    'templates/review',
    'templates/reviews',
    'templates/reviews-form',
    'templates/reviews-summary',
];

function styleMatrixRow(string $name): array
{
    glsr(OptionManager::class)->set('settings.general.style', $name);
    $style = new Style();
    return [
        'block_button' => $style->usesBlockButton(),
        'classes' => $style->classes,
        'root' => $style->styleClasses(),
        'validation' => $style->validation,
        'views' => array_combine(STYLE_MATRIX_VIEWS, array_map([$style, 'view'], STYLE_MATRIX_VIEWS)),
    ];
}

function styleMatrix(): array
{
    return require dirname(__DIR__).'/fixtures/style-matrix.php';
}

beforeEach(fn () => resetPluginState());

afterEach(function () {
    remove_all_filters('site-reviews/defaults/style-classes');
    remove_all_filters('site-reviews/defaults/style-classes/defaults');
});

test('every style the setting offers is pinned', function () {
    $groups = glsr()->config('settings')['settings.general.style']['options'];
    $offered = array_keys(array_merge(...array_values($groups)));
    expect(array_keys(styleMatrix()))->toEqualCanonicalizing($offered);
});

test('a style gives a form what the fixture pins', function (string $name) {
    expect(styleMatrixRow($name))->toBe(styleMatrix()[$name]);
})->with(array_keys(styleMatrix()));

test('a config only holds what changes a style', function (string $file) {
    $config = require $file;
    $slots = [
        'classes' => glsr(StyleClassesDefaults::class)->defaults(),
        'validation' => glsr(StyleValidationDefaults::class)->defaults(),
    ];
    foreach ($slots as $group => $bases) {
        foreach ($config[$group] ?? [] as $key => $value) {
            expect($value)->not->toBe('');
            if ('classes' === $group && preg_match('/^(input|label)_/', $key) && !isset($bases[$key])) {
                continue; // a field type's classes have no base
            }
            expect($bases)->toHaveKey($key)
                ->and(explode(' ', $value))->not->toContain(strtok($bases[$key], ' ')); // the base's first class
        }
    }
    $button = $config['classes']['button'] ?? '';
    expect(str_contains($button, 'wp-block-button__link'))->toBe(str_contains($button, 'wp-element-button'));
})->with(glob(dirname(__DIR__, 3).'/config/styles/*.php'));

test('a filter that adds a class to the button keeps the block button', function () {
    add_filter('site-reviews/defaults/style-classes', function (array $values) {
        $values['button'] .= ' my-button';
        return $values;
    });
    $row = styleMatrixRow('default');
    expect($row['classes']['button'])->toBe('glsr-button wp-block-button__link wp-element-button my-button')
        ->and($row['block_button'])->toBeTrue();
});

test('a filter that changes the base button takes the block button off', function () {
    add_filter('site-reviews/defaults/style-classes/defaults', function (array $defaults) {
        $defaults['button'] = 'glsr-button my-button';
        return $defaults;
    });
    $row = styleMatrixRow('default');
    expect($row['classes']['button'])->toBe('glsr-button my-button')
        ->and($row['block_button'])->toBeFalse();
});
