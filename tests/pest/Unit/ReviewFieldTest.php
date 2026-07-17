<?php

use GeminiLabs\SiteReviews\Contracts\FieldContract;
use GeminiLabs\SiteReviews\Modules\Html\ReviewField;

uses()->group('plugin');

test('build checkbox', function () {
    expect(buildReviewField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'type' => 'checkbox',
        ]))->toEqual('<div class="glsr-field glsr-field-choice" data-field="foobar" data-type="checkbox">'.
            '<div class="glsr-field-subgroup">'.
                '<span class="glsr-field-checkbox">'.
                    '<span>'.
                        '<span class="glsr-checkbox">'.
                            '<input type="checkbox" class="glsr-input-checkbox" id="site-reviews-foobar-1" name="site-reviews[foobar]" value="1" />'.
                        '</span>'.
                    '</span>'.
                    '<label for="site-reviews-foobar-1">Foobar</label>'.
                '</span>'.
            '</div>'.
            '<div class="glsr-field-error"></div>'.
        '</div>');
});

test('build checkboxes', function () {
    expect(buildReviewField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'type' => 'checkbox',
            'options' => [
                'foo' => 'Foo',
                'bar' => 'Bar',
            ],
        ]))->toEqual('<div class="glsr-field glsr-field-choice" data-field="foobar" data-type="checkbox">'.
            '<label class="glsr-label" for="">'.
                '<span>Foobar</span>'.
            '</label>'.
            '<div class="glsr-field-subgroup">'.
                '<span class="glsr-field-checkbox">'.
                    '<span>'.
                        '<span class="glsr-checkbox">'.
                            '<input type="checkbox" class="glsr-input-checkbox" id="site-reviews-foobar-1" name="site-reviews[foobar][]" value="foo" />'.
                        '</span>'.
                    '</span>'.
                    '<label for="site-reviews-foobar-1">Foo</label>'.
                '</span>'.
                '<span class="glsr-field-checkbox">'.
                    '<span>'.
                        '<span class="glsr-checkbox">'.
                            '<input type="checkbox" class="glsr-input-checkbox" id="site-reviews-foobar-2" name="site-reviews[foobar][]" value="bar" />'.
                        '</span>'.
                    '</span>'.
                    '<label for="site-reviews-foobar-2">Bar</label>'.
                '</span>'.
            '</div>'.
            '<div class="glsr-field-error"></div>'.
        '</div>');
});

test('build checkboxes with descriptions', function () {
    expect(buildReviewField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'type' => 'checkbox',
            'options' => [
                'foo' => ['Foo', 'this is foo'],
                'bar' => ['Bar', 'this is bar'],
            ],
        ]))->toEqual('<div class="glsr-field glsr-field-choice" data-field="foobar" data-type="checkbox">'.
            '<label class="glsr-label" for="">'.
                '<span>Foobar</span>'.
            '</label>'.
            '<div class="glsr-field-subgroup">'.
                '<span class="glsr-field-checkbox">'.
                    '<span>'.
                        '<span class="glsr-checkbox">'.
                            '<input type="checkbox" class="glsr-input-checkbox" id="site-reviews-foobar-1" name="site-reviews[foobar][]" value="foo" />'.
                        '</span>'.
                    '</span>'.
                    '<label for="site-reviews-foobar-1">'.
                        '<span data-type="label">Foo</span>'.
                        '<span data-type="description">this is foo</span>'.
                    '</label>'.
                '</span>'.
                '<span class="glsr-field-checkbox">'.
                    '<span>'.
                        '<span class="glsr-checkbox">'.
                            '<input type="checkbox" class="glsr-input-checkbox" id="site-reviews-foobar-2" name="site-reviews[foobar][]" value="bar" />'.
                        '</span>'.
                    '</span>'.
                    '<label for="site-reviews-foobar-2">'.
                        '<span data-type="label">Bar</span>'.
                        '<span data-type="description">this is bar</span>'.
                    '</label>'.
                '</span>'.
            '</div>'.
            '<div class="glsr-field-error"></div>'.
        '</div>');
});

test('build code', function () {
    expect(buildReviewField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'type' => 'code',
        ]))->toEqual('<div class="glsr-field glsr-field-code" data-field="foobar" data-type="code">'.
            '<label class="glsr-label" for="site-reviews-foobar">'.
                '<span>Foobar</span>'.
            '</label>'.
            '<textarea class="glsr-textarea" id="site-reviews-foobar" name="site-reviews[foobar]"></textarea>'.
            '<div class="glsr-field-error"></div>'.
        '</div>');
});

test('build color', function () {
    expect(buildReviewField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'type' => 'color',
        ]))->toEqual('<div class="glsr-field glsr-field-color" data-field="foobar" data-type="color">'.
            '<label class="glsr-label" for="site-reviews-foobar">'.
                '<span>Foobar</span>'.
            '</label>'.
            '<input type="color" class="glsr-input glsr-input-color" id="site-reviews-foobar" name="site-reviews[foobar]" value="" />'.
            '<div class="glsr-field-error"></div>'.
        '</div>');
});

test('build email', function () {
    expect(buildReviewField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'type' => 'email',
        ]))->toEqual('<div class="glsr-field glsr-field-email" data-field="foobar" data-type="email">'.
            '<label class="glsr-label" for="site-reviews-foobar">'.
                '<span>Foobar</span>'.
            '</label>'.
            '<input type="email" class="glsr-input glsr-input-email" id="site-reviews-foobar" name="site-reviews[foobar]" value="" />'.
            '<div class="glsr-field-error"></div>'.
        '</div>');
});

test('build hidden', function () {
    expect(buildReviewField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'type' => 'hidden',
        ]))->toEqual('<input type="hidden" name="site-reviews[foobar]" value="" />');
});

test('build number', function () {
    expect(buildReviewField([
            'label' => 'Foobar',
            'max' => 10,
            'min' => 0,
            'name' => 'foobar',
            'step' => 1,
            'type' => 'number',
        ]))->toEqual('<div class="glsr-field glsr-field-number" data-field="foobar" data-type="number">'.
            '<label class="glsr-label" for="site-reviews-foobar">'.
                '<span>Foobar</span>'.
            '</label>'.
            '<input type="number" class="glsr-input glsr-input-number" id="site-reviews-foobar" name="site-reviews[foobar]" max="10" min="0" step="1" value="" />'.
            '<div class="glsr-field-error"></div>'.
        '</div>');
});

test('build password', function () {
    expect(buildReviewField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'type' => 'password',
        ]))->toEqual('<div class="glsr-field glsr-field-password" data-field="foobar" data-type="password">'.
            '<label class="glsr-label" for="site-reviews-foobar">'.
                '<span>Foobar</span>'.
            '</label>'.
            '<input type="password" class="glsr-input glsr-input-password" id="site-reviews-foobar" name="site-reviews[foobar]" autocomplete="off" value="" />'.
            '<div class="glsr-field-error"></div>'.
        '</div>');
});

test('build radio', function () {
    expect(buildReviewField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'type' => 'radio',
        ]))->toEqual('<div class="glsr-field glsr-field-choice" data-field="foobar" data-type="radio">'.
            '<div class="glsr-field-subgroup">'.
                '<span class="glsr-field-radio">'.
                    '<span>'.
                        '<span class="glsr-radio">'.
                            '<input type="radio" class="glsr-input-radio" id="site-reviews-foobar-1" name="site-reviews[foobar]" value="1" />'.
                        '</span>'.
                    '</span>'.
                    '<label for="site-reviews-foobar-1">Foobar</label>'.
                '</span>'.
            '</div>'.
            '<div class="glsr-field-error"></div>'.
        '</div>');
});

test('build radios', function () {
    expect(buildReviewField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'type' => 'radio',
            'options' => [
                'foo' => 'Foo',
                'bar' => 'Bar',
            ],
        ]))->toEqual('<div class="glsr-field glsr-field-choice" data-field="foobar" data-type="radio">'.
            '<label class="glsr-label" for="">'.
                '<span>Foobar</span>'.
            '</label>'.
            '<div class="glsr-field-subgroup">'.
                '<span class="glsr-field-radio">'.
                    '<span>'.
                        '<span class="glsr-radio">'.
                            '<input type="radio" class="glsr-input-radio" id="site-reviews-foobar-1" name="site-reviews[foobar]" value="foo" />'.
                        '</span>'.
                    '</span>'.
                    '<label for="site-reviews-foobar-1">Foo</label>'.
                '</span>'.
                '<span class="glsr-field-radio">'.
                    '<span>'.
                        '<span class="glsr-radio">'.
                            '<input type="radio" class="glsr-input-radio" id="site-reviews-foobar-2" name="site-reviews[foobar]" value="bar" />'.
                        '</span>'.
                    '</span>'.
                    '<label for="site-reviews-foobar-2">Bar</label>'.
                '</span>'.
            '</div>'.
            '<div class="glsr-field-error"></div>'.
        '</div>');
});

test('build range', function () {
    expect(buildReviewField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'type' => 'range',
            'options' => [
                'foo' => 'Foo',
                'bar' => 'Bar',
            ],
        ]))->toEqual('<div class="glsr-field glsr-field-choice" data-field="foobar" data-type="range">'.
            '<label class="glsr-label" for="">'.
                '<span>Foobar</span>'.
            '</label>'.
            '<span class="glsr-range-options" data-placeholder="Please select">'.
                '<span class="glsr-field-range">'.
                    '<span>'.
                        '<span class="glsr-range">'.
                            '<input type="radio" class="glsr-input-range" id="site-reviews-foobar-1" name="site-reviews[foobar]" value="1" />'.
                        '</span>'.
                    '</span>'.
                    '<label for="site-reviews-foobar-1">Foo</label>'.
                '</span>'.
                '<span class="glsr-field-range">'.
                    '<span>'.
                        '<span class="glsr-range">'.
                            '<input type="radio" class="glsr-input-range" id="site-reviews-foobar-2" name="site-reviews[foobar]" value="2" />'.
                        '</span>'.
                    '</span>'.
                    '<label for="site-reviews-foobar-2">Bar</label>'.
                '</span>'.
            '</span>'.
            '<div class="glsr-field-error"></div>'.
        '</div>');
});

test('build range with labels', function () {
    expect(buildReviewField([
            'label' => 'Foobar',
            'labels' => [
                'Left',
                'Middle',
                'Right',
            ],
            'name' => 'foobar',
            'type' => 'range',
            'options' => [
                'foo' => 'Foo',
                'bar' => 'Bar',
            ],
        ]))->toEqual('<div class="glsr-field glsr-field-choice" data-field="foobar" data-type="range">'.
            '<label class="glsr-label" for="">'.
                '<span>Foobar</span>'.
            '</label>'.
            '<span class="glsr-range-labels">'.
                '<span>Left</span>'.
                '<span>Middle</span>'.
                '<span>Right</span>'.
            '</span>'.
            '<span class="glsr-range-options" data-placeholder="Please select">'.
                '<span class="glsr-field-range">'.
                    '<span>'.
                        '<span class="glsr-range">'.
                            '<input type="radio" class="glsr-input-range" id="site-reviews-foobar-1" name="site-reviews[foobar]" value="1" />'.
                        '</span>'.
                    '</span>'.
                    '<label for="site-reviews-foobar-1">Foo</label>'.
                '</span>'.
                '<span class="glsr-field-range">'.
                    '<span>'.
                        '<span class="glsr-range">'.
                            '<input type="radio" class="glsr-input-range" id="site-reviews-foobar-2" name="site-reviews[foobar]" value="2" />'.
                        '</span>'.
                    '</span>'.
                    '<label for="site-reviews-foobar-2">Bar</label>'.
                '</span>'.
            '</span>'.
            '<div class="glsr-field-error"></div>'.
        '</div>');
});

test('build rating', function () {
    expect(buildReviewField([
            'label' => 'Your overall rating',
            'name' => 'foobar',
            'type' => 'rating',
        ]))->toEqual('<div class="glsr-field glsr-field-rating" data-field="foobar" data-type="rating">'.
            '<label class="glsr-label" for="site-reviews-foobar">'.
                '<span>Your overall rating</span>'.
            '</label>'.
            '<select class="browser-default disable-select glsr-select no-wrap no_wrap" id="site-reviews-foobar" name="site-reviews[foobar]">'.
                '<option value="">Select a Rating</option>'.
                '<option value="5">5 Stars</option>'.
                '<option value="4">4 Stars</option>'.
                '<option value="3">3 Stars</option>'.
                '<option value="2">2 Stars</option>'.
                '<option value="1">1 Star</option>'.
            '</select>'.
            '<div class="glsr-field-error"></div>'.
        '</div>');
});

test('build select', function () {
    expect(buildReviewField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'options' => [
                'red' => 'Red',
                'green' => 'Green',
                'blue' => 'Blue',
            ],
            'placeholder' => 'Select a color',
            'type' => 'select',
        ]))->toEqual('<div class="glsr-field glsr-field-select" data-field="foobar" data-type="select">'.
            '<label class="glsr-label" for="site-reviews-foobar">'.
                '<span>Foobar</span>'.
            '</label>'.
            '<select class="glsr-select" id="site-reviews-foobar" name="site-reviews[foobar]">'.
                '<option value="">Select a color</option>'.
                '<option value="red">Red</option>'.
                '<option value="green">Green</option>'.
                '<option value="blue">Blue</option>'.
            '</select>'.
            '<div class="glsr-field-error"></div>'.
        '</div>');
});

test('build text', function () {
    expect(buildReviewField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'type' => 'text',
        ]))->toEqual('<div class="glsr-field glsr-field-text" data-field="foobar" data-type="text">'.
            '<label class="glsr-label" for="site-reviews-foobar">'.
                '<span>Foobar</span>'.
            '</label>'.
            '<input type="text" class="glsr-input glsr-input-text" id="site-reviews-foobar" name="site-reviews[foobar]" value="" />'.
            '<div class="glsr-field-error"></div>'.
        '</div>');
});

test('build textarea', function () {
    expect(buildReviewField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'placeholder' => 'Foobar...',
            'type' => 'textarea',
        ]))->toEqual('<div class="glsr-field glsr-field-textarea" data-field="foobar" data-type="textarea">'.
            '<label class="glsr-label" for="site-reviews-foobar">'.
                '<span>Foobar</span>'.
            '</label>'.
            '<textarea class="glsr-textarea" id="site-reviews-foobar" name="site-reviews[foobar]" placeholder="Foobar..."></textarea>'.
            '<div class="glsr-field-error"></div>'.
        '</div>');
});

test('build toggle', function () {
    expect(buildReviewField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'required' => true,
            'type' => 'toggle',
        ]))->toEqual('<div class="glsr-field glsr-field-choice glsr-required" data-field="foobar" data-type="toggle">'.
            '<div class="glsr-field-subgroup">'.
                '<span class="glsr-field-toggle">'.
                    '<span>'.
                        '<span class="glsr-toggle">'.
                            '<input type="checkbox" class="glsr-input-toggle" id="site-reviews-foobar-1" name="site-reviews[foobar]" required value="1" />'.
                            '<span class="glsr-toggle-track"></span>'.
                        '</span>'.
                    '</span>'.
                    '<label for="site-reviews-foobar-1">Foobar</label>'.
                '</span>'.
            '</div>'.
            '<div class="glsr-field-error"></div>'.
        '</div>');
});

test('build toggles', function () {
    expect(buildReviewField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'type' => 'toggle',
            'options' => [
                'foo' => 'Foo',
                'bar' => 'Bar',
            ],
        ]))->toEqual('<div class="glsr-field glsr-field-choice" data-field="foobar" data-type="toggle">'.
            '<label class="glsr-label" for="">'.
                '<span>Foobar</span>'.
            '</label>'.
            '<div class="glsr-field-subgroup">'.
                '<span class="glsr-field-toggle">'.
                    '<span>'.
                        '<span class="glsr-toggle">'.
                            '<input type="checkbox" class="glsr-input-toggle" id="site-reviews-foobar-1" name="site-reviews[foobar][]" value="foo" />'.
                            '<span class="glsr-toggle-track"></span>'.
                        '</span>'.
                    '</span>'.
                    '<label for="site-reviews-foobar-1">Foo</label>'.
                '</span>'.
                '<span class="glsr-field-toggle">'.
                    '<span>'.
                        '<span class="glsr-toggle">'.
                            '<input type="checkbox" class="glsr-input-toggle" id="site-reviews-foobar-2" name="site-reviews[foobar][]" value="bar" />'.
                            '<span class="glsr-toggle-track"></span>'.
                        '</span>'.
                    '</span>'.
                    '<label for="site-reviews-foobar-2">Bar</label>'.
                '</span>'.
            '</div>'.
            '<div class="glsr-field-error"></div>'.
        '</div>');
});

test('build unknown', function () {
    expect(buildReviewField([ 'label' => 'Foobar', 'name' => 'foobar', 'type' => 'unknown', ]))->toEqual('');
});

test('build url', function () {
    expect(buildReviewField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'type' => 'url',
        ]))->toEqual('<div class="glsr-field glsr-field-url" data-field="foobar" data-type="url">'.
            '<label class="glsr-label" for="site-reviews-foobar">'.
                '<span>Foobar</span>'.
            '</label>'.
            '<input type="url" class="glsr-input glsr-input-url" id="site-reviews-foobar" name="site-reviews[foobar]" value="" />'.
            '<div class="glsr-field-error"></div>'.
        '</div>');
});

test('build yes no', function () {
    expect(buildReviewField([
            'label' => 'Foobar',
            'name' => 'foobar',
            'type' => 'yes_no',
            'options' => [ // this should be ignored in a yes_no field
                'x' => 'X',
                'y' => 'Y',
                'z' => 'Z',
            ],
        ]))->toEqual('<div class="glsr-field glsr-field-choice" data-field="foobar" data-type="yes_no">'.
            '<label class="glsr-label" for="">'.
                '<span>Foobar</span>'.
            '</label>'.
            '<label for="site-reviews-foobar-1">'.
                '<input type="radio" class="glsr-input-radio" id="site-reviews-foobar-1" name="site-reviews[foobar]" value="no" /> No'.
            '</label>'.
            '<label for="site-reviews-foobar-2">'.
                '<input type="radio" class="glsr-input-radio" id="site-reviews-foobar-2" name="site-reviews[foobar]" value="yes" /> Yes'.
            '</label>'.
            '<div class="glsr-field-error"></div>'.
        '</div>');
});

function buildReviewField(array $args = []): string
{
    $html = makeReviewField($args)->build();
    $parts = preg_split('/\R/', $html);
    $parts = array_map('trim', $parts);
    return implode('', $parts);
}

function makeReviewField(array $args = []): FieldContract
{
    return new ReviewField($args);
}

test('a field with a description builds one for the templates that place it', function () {
    // The default field template carries no {{ description }} tag (probed) — the
    // styled framework templates do — so the assertion is on the builder itself.
    $field = makeReviewField(['name' => 'x', 'type' => 'text', 'description' => 'Help for the reviewer']);

    expect($field->buildFieldDescription())->toContain('Help for the reviewer');
    expect(makeReviewField(['name' => 'x', 'type' => 'text'])->buildFieldDescription())->toBe('');
});

test('errors, hidden and required each mark the field wrapper', function () {
    $withErrors = buildReviewField(['name' => 'x', 'type' => 'text', 'errors' => ['This field is required.']]);
    expect($withErrors)->toContain('glsr-field-is-invalid')
        ->and($withErrors)->toContain('This field is required.');

    $hidden = buildReviewField(['name' => 'x', 'type' => 'text', 'is_hidden' => true]);
    expect($hidden)->toContain('glsr-hidden');
});

test('a custom field is not forced required by the form settings', function () {
    // forms.required lists the BUILT-IN fields the site wants mandatory; a custom
    // field with the same name must keep its own requiredness.
    glsr(\GeminiLabs\SiteReviews\Database\OptionManager::class)->set('settings.forms.required', ['phone']);

    $custom = makeReviewField(['name' => 'phone', 'type' => 'text', 'is_custom' => true]);
    $builtin = makeReviewField(['name' => 'phone', 'type' => 'text']);

    expect($custom->required)->toBeFalse()
        ->and($builtin->required)->toBeTrue();
});

test('conditions with a criteria but no valid rules add no data-conditions', function () {
    $field = makeReviewField(['name' => 'x', 'type' => 'text', 'conditions' => 'all|:broken']);

    expect($field['data-conditions'])->toBeNull();

    $real = makeReviewField(['name' => 'x', 'type' => 'text', 'conditions' => 'all|rating:>:3']);
    expect($real['data-conditions'])->toContain('"criteria":"all"');
});
