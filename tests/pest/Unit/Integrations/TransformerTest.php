<?php

use GeminiLabs\SiteReviews\Integrations\Avada\Transformer as AvadaTransformer;
use GeminiLabs\SiteReviews\Integrations\Flatsome\Transformer as FlatsomeTransformer;
use GeminiLabs\SiteReviews\Integrations\WPBakery\Transformer as WPBakeryTransformer;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

/*
 * The page-builder control transformers: Avada, Flatsome and WPBakery.
 *
 * Each builder wants the shortcode's controls in its own vocabulary, so each
 * integration takes one entry of SiteReviewsShortcode::settings() and rewrites
 * it. That is all a transformer is — array in, array out — which is why these
 * three are testable while their integrations are dormant: Avada's and WPBakery's
 * stubs declare a version below the one the integration requires, and Flatsome is
 * a theme, so none of the three registers a hook here. The transformers are
 * called directly instead, with the same real control arrays their callers pass:
 *
 *   FusionElement::elementParameters()  -> new Avada\Transformer($name, $control, $tag)
 *   FlatsomeShortcode::options()        -> new Flatsome\Transformer($name, $control, $tag)
 *   VcShortcode::params()               -> new WPBakery\Transformer($name, $control, $tag)
 *
 * The five controls used below are one of each shape the config produces: a
 * select with no options (ajax-searched), a single-value select, a number, a
 * checkbox with options, a checkbox without, and a text field.
 */

function shortcodeControl(string $name): array
{
    $settings = glsr(SiteReviewsShortcode::class)->settings();

    expect($settings)->toHaveKey($name); // the input is real, so prove it exists

    return $settings[$name];
}

function shortcodeTag(): string
{
    return glsr(SiteReviewsShortcode::class)->tag;
}

function avadaControl(string $name): array
{
    return (new AvadaTransformer($name, shortcodeControl($name), shortcodeTag()))->control();
}

function flatsomeControl(string $name): array
{
    return (new FlatsomeTransformer($name, shortcodeControl($name), shortcodeTag()))->control();
}

function wpbakeryControl(string $name): array
{
    return (new WPBakeryTransformer($name, shortcodeControl($name), shortcodeTag()))->control();
}

/*
 * Avada.
 *
 * ControlDefaults maps label => heading and name => param_name, and its finalize()
 * turns the group slug into the tab label Avada shows. Its group enum only allows
 * design and general, so every other group (display, hide, schema, advanced) is
 * reset to the default and lands in General.
 */

test('avada turns an options-less select into an ajax select', function () {
    $control = avadaControl('assigned_posts');

    expect($control['type'])->toBe('ajax_select')
        ->and($control['ajax'])->toBe(glsr()->prefix.'fusion_search_query')
        ->and($control['ajax_params'])->toBe([
            'option' => 'assigned_posts',
            'shortcode' => shortcodeTag(),
        ])
        ->and($control['placeholder'])->toBe('Search Pages...')
        ->and($control['param_name'])->toBe('assigned_posts')
        ->and($control['heading'])->toBe(shortcodeControl('assigned_posts')['label'])
        ->and($control['group'])->toBe('General');

    // assigned_posts is multiple, so the control is not capped at one selection.
    expect($control)->not->toHaveKey('max_input');
});

test('avada caps a single-value ajax select at one selection', function () {
    $control = avadaControl('author');

    expect($control['type'])->toBe('ajax_select')
        ->and($control['placeholder'])->toBe('Search User...')
        ->and($control['max_input'])->toBe(1);
});

test('avada turns a select with options into a value list with a placeholder first', function () {
    // The config passes its options in the "options" key; Avada reads them from
    // "value", and the placeholder is the empty-keyed first entry.
    $control = new AvadaTransformer('type', [
        'label' => 'Limit Reviews by Type',
        'options' => ['local' => 'Local Reviews'],
        'placeholder' => 'Select a Review Type...',
        'type' => 'select',
    ], shortcodeTag());

    expect($control->control()['value'])->toBe([
        '' => 'Select a Review Type...',
        'local' => 'Local Reviews',
    ]);
});

test('avada turns a number into a range', function () {
    expect(avadaControl('display')['type'])->toBe('range');
});

test('avada turns a checkbox with options into a multiple select', function () {
    $control = avadaControl('hide');

    expect($control['type'])->toBe('multiple_select')
        ->and($control['value'])->toBe(shortcodeControl('hide')['options'])
        ->and($control['heading'])->toBe('Hide') // set by the transformer, not the config
        ->and($control['placeholder_text'])->toBe('Select...');
});

test('avada turns a checkbox without options into a yes/no radio set', function () {
    $control = avadaControl('schema');

    expect($control['type'])->toBe('radio_button_set')
        ->and($control['default'])->toBe(0)
        ->and($control['value'])->toBe([
            0 => 'No',
            'yes' => 'Yes', // a string key: Avada's dependency option mishandles numeric ones
        ]);
});

test('avada labels the text fields it cannot otherwise name', function () {
    $control = avadaControl('id');

    expect($control['type'])->toBe('textfield')
        ->and($control['heading'])->toBe('Custom CSS ID')
        ->and($control['description'])->toBe('Add an ID to the wrapping HTML element.');
});

/*
 * WPBakery.
 *
 * ControlDefaults maps default => std, label => heading, name => param_name. Its
 * group enum allows advanced, so — unlike Avada — the id and class controls keep
 * their own tab. WPBakery reads its choices from "value", keyed the other way
 * round from the config, hence the array_flip.
 */

test('wpbakery turns an options-less select into an autocomplete', function () {
    $control = wpbakeryControl('assigned_posts');

    expect($control['type'])->toBe('autocomplete')
        ->and($control['settings'])->toBe(['multiple' => true, 'sortable' => true])
        ->and($control['param_name'])->toBe('assigned_posts')
        ->and($control['heading'])->toBe(shortcodeControl('assigned_posts')['label']);
});

test('wpbakery flips a select with options into a dropdown value list', function () {
    $control = (new WPBakeryTransformer('type', [
        'label' => 'Limit Reviews by Type',
        'options' => ['local' => 'Local Reviews'],
        'placeholder' => 'Select a Review Type...',
        'type' => 'select',
    ], shortcodeTag()))->control();

    expect($control['type'])->toBe('dropdown')
        ->and($control['value'])->toBe([
            'Select a Review Type...' => '',
            'Local Reviews' => 'local',
        ])
        ->and($control)->not->toHaveKey('options');
});

test('wpbakery turns a number into its own range control', function () {
    expect(wpbakeryControl('display')['type'])->toBe('glsr_type_range');
});

test('wpbakery flips a checkbox with options and drops the options key', function () {
    $control = wpbakeryControl('hide');

    expect($control['value'])->toBe(array_flip(shortcodeControl('hide')['options']))
        ->and($control['heading'])->toBe('Hide')
        ->and($control)->not->toHaveKey('options');
});

test('wpbakery turns a checkbox without options into a single yes value', function () {
    expect(wpbakeryControl('schema')['value'])->toBe(['Yes' => 'true']);
});

test('wpbakery keeps the advanced group that avada discards', function () {
    // The group enum is [advanced, design, general] here and [design, general]
    // there, which is the whole difference: the same control lands in a different
    // tab in each builder.
    expect(wpbakeryControl('id')['group'])->toBe('advanced')
        ->and(avadaControl('id')['group'])->toBe('General');
});

/*
 * Flatsome.
 *
 * ControlDefaults maps label => heading and sanitizes the type as a slug. Flatsome
 * carries the builder-specific settings in a nested "config" key rather than
 * alongside the control, and its post/term pickers are declared there.
 */

test('flatsome declares a post picker for an options-less select', function () {
    $control = flatsomeControl('assigned_posts');

    expect($control['type'])->toBe('select')
        ->and($control['config'])->toBe([
            'sortable' => false,
            'postSelect' => glsr()->prefix.'assigned_posts',
            'multiple' => true, // forced on for every assigned_* control
            'placeholder' => 'Select a Page...',
        ])
        // the two keys the config carried were moved into config and removed
        ->and($control)->not->toHaveKey('multiple')
        ->and($control)->not->toHaveKey('placeholder');
});

test('flatsome declares a term picker for the assigned categories', function () {
    expect(flatsomeControl('assigned_terms')['config']['termSelect'])
        ->toBe(['taxonomies' => glsr()->taxonomy]);
});

test('flatsome leaves a single-value picker unmultiplied', function () {
    $config = flatsomeControl('author')['config'];

    expect($config['postSelect'])->toBe(glsr()->prefix.'author')
        ->and($config['multiple'])->toBeFalse();
});

test('flatsome turns a number into a slider', function () {
    expect(flatsomeControl('display')['type'])->toBe('slider');
});

test('flatsome turns a checkbox with options into a multiple select', function () {
    $control = flatsomeControl('hide');

    expect($control['type'])->toBe('select')
        ->and($control['config'])->toBe([
            'multiple' => true,
            'options' => shortcodeControl('hide')['options'],
            'placeholder' => 'Select...',
            'sortable' => false,
        ])
        ->and($control)->not->toHaveKey('options');
});

test('flatsome turns a checkbox without options into radio buttons', function () {
    $control = flatsomeControl('schema');

    expect($control['type'])->toBe('radio-buttons')
        ->and($control['options'])->toBe([
            '' => ['title' => 'No'],
            'true' => ['title' => 'Yes'],
        ]);
});
