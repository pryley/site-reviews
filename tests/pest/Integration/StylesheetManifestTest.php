<?php

/*
 * The built stylesheets, per style and component.
 *
 * rollup.config.mjs writes assets/styles/manifest.php from the import lists in +/styles/{style}.css:
 * for each style, the file that holds each component, either the shared base's or the style's own
 * (the base with the style's changes after it). The renderer prints a root's components from it,
 * so a style the settings offer without a manifest entry, or an entry without its file, is a page
 * without its styles.
 */

function stylesheetManifest(): array
{
    return include glsr()->path('assets/styles/manifest.php');
}

test('every style the settings offer has a manifest entry, and every entry names a built file', function () {
    $manifest = stylesheetManifest();
    $groups = glsr()->config('settings')['settings.general.style']['options']; // grouped: Styles, Plugins, Themes
    $styles = array_keys(array_merge(...array_values($groups)));

    expect(array_keys($manifest['styles']))->toEqualCanonicalizing($styles);
    foreach ($manifest['styles'] as $style => $files) {
        foreach ($files as $component => $file) {
            expect(file_exists(glsr()->path("assets/styles/{$file}")))->toBeTrue("{$style}: {$component} => {$file}");
            expect($file)->toMatch("#^(base|{$style})/{$component}\.css$#");
        }
    }
});

test('the components are the base partials in the order a page needs them, and every base file exists', function () {
    $manifest = stylesheetManifest();

    expect($manifest['components'])->toBe([
        'variables', 'keyframes', 'base', 'loaders', 'pagination', 'star-rating', 'review', 'flags', 'summary', 'form',
        'field-label', 'field-input', 'field-choice', 'field-toggle', 'field-range', 'field-rating', 'button', 'modal', 'debug',
    ]);
    foreach ($manifest['components'] as $component) {
        expect(file_exists(glsr()->path("assets/styles/base/{$component}.css")))->toBeTrue($component);
    }
    // the minimal style is the base and nothing else
    expect(array_values($manifest['styles']['minimal']))->toBe(array_map(fn ($c) => "base/{$c}.css", $manifest['components']));
});

test('a style leaves out the field components its framework styles, and adds its own where it has them', function () {
    $manifest = stylesheetManifest();

    // Bootstrap's inputs are Bootstrap's: no base field-input, field-choice or field-toggle, but its own label rules
    expect($manifest['styles']['bootstrap'])->not->toHaveKey('field-input');
    expect($manifest['styles']['bootstrap'])->not->toHaveKey('field-choice');
    expect($manifest['styles']['bootstrap'])->not->toHaveKey('field-toggle');
    expect($manifest['styles']['bootstrap']['field-label'])->toBe('bootstrap/field-label.css');
    expect($manifest['styles']['bootstrap']['review'])->toBe('base/review.css');
    // Divi compiles every component itself: its base rules select with #page-container like Divi's theme CSS
    expect(array_values($manifest['styles']['divi']))->toBe(array_map(fn ($c) => "divi/{$c}.css", $manifest['components']));
});

test('a base component carries the shared namespace and a style component its own after the base', function () {
    $base = file_get_contents(glsr()->path('assets/styles/base/form.css'));
    $default = file_get_contents(glsr()->path('assets/styles/default/form.css'));

    expect($base)->toStartWith('.glsr form.glsr-hide-form')
        ->not->toContain('.glsr-default');
    expect($default)->toStartWith('.glsr form.glsr-hide-form') // the base first
        ->toContain('.glsr-default form.glsr-form'); // then Default's changes
    expect(strpos($default, '.glsr-default'))->toBeGreaterThan(strpos($default, '.glsr form.glsr-form{'));
});
