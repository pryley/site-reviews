<?php

use GeminiLabs\SiteReviews\Contracts\FieldContract;
use GeminiLabs\SiteReviews\Modules\Html\Field;

uses()->group('plugin');

test('build checkbox', function () {
    expect(buildField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'type' => 'checkbox',
        ]))->toEqual('<div>'.
            '<label for="site-reviews-foobar-1">'.
                '<input type="checkbox" id="site-reviews-foobar-1" name="site-reviews[foobar]" value="1" /> Foobar'.
            '</label>'.
        '</div>');
});

test('build checkboxes', function () {
    expect(buildField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'type' => 'checkbox',
            'options' => [
                'foo' => 'Foo',
                'bar' => 'Bar',
            ],
        ]))->toEqual('<div>'.
            '<label>Foobar</label>'.
            '<label for="site-reviews-foobar-1">'.
                '<input type="checkbox" id="site-reviews-foobar-1" name="site-reviews[foobar][]" value="foo" /> Foo'.
            '</label>'.
            '<label for="site-reviews-foobar-2">'.
                '<input type="checkbox" id="site-reviews-foobar-2" name="site-reviews[foobar][]" value="bar" /> Bar'.
            '</label>'.
        '</div>');
});

test('build code', function () {
    expect(buildField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'type' => 'code',
        ]))->toEqual('<div>'.
            '<label for="site-reviews-foobar">Foobar</label>'.
            '<textarea id="site-reviews-foobar" name="site-reviews[foobar]"></textarea>'.
        '</div>');
});

test('build color', function () {
    expect(buildField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'type' => 'color',
        ]))->toEqual('<div>'.
            '<label for="site-reviews-foobar">Foobar</label>'.
            '<input type="color" id="site-reviews-foobar" name="site-reviews[foobar]" value="" />'.
        '</div>');
});

test('build email', function () {
    expect(buildField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'type' => 'email',
        ]))->toEqual('<div>'.
            '<label for="site-reviews-foobar">Foobar</label>'.
            '<input type="email" id="site-reviews-foobar" name="site-reviews[foobar]" value="" />'.
        '</div>');
});

test('build hidden', function () {
    expect(buildField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'type' => 'hidden',
        ]))->toEqual('<input type="hidden" name="site-reviews[foobar]" value="" />');
});

test('build number', function () {
    expect(buildField([
            'label' => 'Foobar',
            'max' => 10,
            'min' => 0,
            'name' => 'foobar',
            'step' => 1,
            'type' => 'number',
        ]))->toEqual('<div>'.
            '<label for="site-reviews-foobar">Foobar</label>'.
            '<input type="number" id="site-reviews-foobar" name="site-reviews[foobar]" max="10" min="0" step="1" value="" />'.
        '</div>');
});

test('build password', function () {
    expect(buildField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'type' => 'password',
        ]))->toEqual('<div>'.
            '<label for="site-reviews-foobar">Foobar</label>'.
            '<input type="password" id="site-reviews-foobar" name="site-reviews[foobar]" autocomplete="off" value="" />'.
        '</div>');
});

test('build radio', function () {
    expect(buildField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'type' => 'radio',
        ]))->toEqual('<div>'.
            '<label for="site-reviews-foobar-1">'.
                '<input type="radio" id="site-reviews-foobar-1" name="site-reviews[foobar]" value="1" /> Foobar'.
            '</label>'.
        '</div>');
});

test('build radios', function () {
    expect(buildField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'type' => 'radio',
            'options' => [
                'foo' => 'Foo',
                'bar' => 'Bar',
            ],
        ]))->toEqual('<div>'.
            '<label>Foobar</label>'.
            '<label for="site-reviews-foobar-1">'.
                '<input type="radio" id="site-reviews-foobar-1" name="site-reviews[foobar]" value="foo" /> Foo'.
            '</label>'.
            '<label for="site-reviews-foobar-2">'.
                '<input type="radio" id="site-reviews-foobar-2" name="site-reviews[foobar]" value="bar" /> Bar'.
            '</label>'.
        '</div>');
});

test('build rating', function () {
    expect(buildField([
            'label' => 'Your overall rating',
            'name' => 'foobar',
            'type' => 'rating',
        ]))->toEqual('<div>'.
            '<label for="site-reviews-foobar">Your overall rating</label>'.
            '<select class="browser-default disable-select no-wrap no_wrap" id="site-reviews-foobar" name="site-reviews[foobar]">'.
                '<option value="">Select a Rating</option>'.
                '<option value="5">5 Stars</option>'.
                '<option value="4">4 Stars</option>'.
                '<option value="3">3 Stars</option>'.
                '<option value="2">2 Stars</option>'.
                '<option value="1">1 Star</option>'.
            '</select>'.
        '</div>');
});

test('build select', function () {
    expect(buildField([
            'label' => 'Color',
            'name' => 'foobar',
            'options' => [
                'red' => 'Red',
                'green' => 'Green',
                'blue' => 'Blue',
            ],
            'placeholder' => 'Select a color',
            'type' => 'select',
        ]))->toEqual('<div>'.
            '<label for="site-reviews-foobar">Color</label>'.
            '<select id="site-reviews-foobar" name="site-reviews[foobar]">'.
                '<option value="">Select a color</option>'.
                '<option value="red">Red</option>'.
                '<option value="green">Green</option>'.
                '<option value="blue">Blue</option>'.
            '</select>'.
        '</div>');
});

test('build text', function () {
    expect(buildField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'type' => 'text',
        ]))->toEqual('<div>'.
            '<label for="site-reviews-foobar">Foobar</label>'.
            '<input type="text" id="site-reviews-foobar" name="site-reviews[foobar]" value="" />'.
        '</div>');
});

test('build textarea', function () {
    expect(buildField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'placeholder' => 'Foobar...',
            'type' => 'textarea',
        ]))->toEqual('<div>'.
            '<label for="site-reviews-foobar">Foobar</label>'.
            '<textarea id="site-reviews-foobar" name="site-reviews[foobar]" placeholder="Foobar..."></textarea>'.
        '</div>');
});

test('build toggle', function () {
    expect(buildField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'required' => true,
            'type' => 'toggle',
        ]))->toEqual('<div>'.
            '<label for="site-reviews-foobar-1">'.
                '<input type="checkbox" id="site-reviews-foobar-1" name="site-reviews[foobar]" required value="1" /> Foobar'.
            '</label>'.
        '</div>');
});

test('build unknown', function () {
    expect(buildField([ 'label' => 'Foobar', 'name' => 'foobar', 'type' => 'unknown', ]))->toEqual('');
});

test('build url', function () {
    expect(buildField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'type' => 'url',
        ]))->toEqual('<div>'.
            '<label for="site-reviews-foobar">Foobar</label>'.
            '<input type="url" id="site-reviews-foobar" name="site-reviews[foobar]" value="" />'.
        '</div>');
});

test('build yes no', function () {
    expect(buildField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'type' => 'yes_no',
            'options' => [ // this should be ignored in a yes_no field
                'x' => 'X',
                'y' => 'Y',
                'z' => 'Z',
            ],
        ]))->toEqual('<div>'.
            '<label>Foobar</label>'.
            '<label for="site-reviews-foobar-1">'.
                '<input type="radio" id="site-reviews-foobar-1" name="site-reviews[foobar]" value="no" /> No'.
            '</label>'.
            '<label for="site-reviews-foobar-2">'.
                '<input type="radio" id="site-reviews-foobar-2" name="site-reviews[foobar]" value="yes" /> Yes'.
            '</label>'.
        '</div>');
});

function buildField(array $args = []): string
{
    $html = makeField($args)->build();
    $parts = preg_split('/\R/', $html);
    $parts = array_map('trim', $parts);
    return implode('', $parts);
}

function makeField(array $args = []): FieldContract
{
    return new Field($args);
}

test('a field missing its name or type is invalid, logged, and builds nothing', function () {
    $field = makeField(['type' => 'text']); // no name

    expect($field->isValid())->toBeFalse()
        ->and($field->build())->toBe('')
        ->and((string) $field)->toBe('');
});

test('the validation rules parse into rule and parameters', function () {
    $field = makeField(['name' => 'x', 'type' => 'text', 'validation' => 'required|max:20|between:1:5']);

    // FieldRuleDefaults casts the parameters to an int array
    expect($field->rules())->toBe([
        ['parameters' => [], 'rule' => 'required'],
        ['parameters' => [20], 'rule' => 'max'],
        ['parameters' => [1], 'rule' => 'between'], // only the first parameter survives the pad
    ]);
});

test('a field element that cannot be built falls back to the unknown element', function () {
    // The field/element filter is the seam an addon replaces an element through;
    // an abstract class or one that ignores the contract must not fatal the form.
    add_filter('site-reviews/field/element/text',
        fn () => \GeminiLabs\SiteReviews\Modules\Html\FieldElements\AbstractFieldElement::class);
    try {
        $field = makeField(['name' => 'x', 'type' => 'text']);
        expect($field->fieldElement())
            ->toBeInstanceOf(\GeminiLabs\SiteReviews\Modules\Html\FieldElements\UnknownElement::class);
    } finally {
        remove_all_filters('site-reviews/field/element/text');
    }

    add_filter('site-reviews/field/element/text', fn () => \GeminiLabs\SiteReviews\Helper::class);
    try {
        $field = makeField(['name' => 'x', 'type' => 'text']);
        expect($field->fieldElement()) // logged: contract not implemented
            ->toBeInstanceOf(\GeminiLabs\SiteReviews\Modules\Html\FieldElements\UnknownElement::class);
    } finally {
        remove_all_filters('site-reviews/field/element/text');
    }
});

test('an empty multi-select with selected on takes every option', function () {
    $field = makeField([
        'multiple' => true,
        'name' => 'x',
        'options' => ['a' => 'A', 'b' => 'B'],
        'selected' => true,
        'type' => 'select',
        'value' => '',
    ]);

    expect($field->value)->toBe(['a', 'b']);
});
