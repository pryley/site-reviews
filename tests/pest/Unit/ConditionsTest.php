<?php

use GeminiLabs\SiteReviews\Contracts\FieldContract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Html\FieldCondition;
use GeminiLabs\SiteReviews\Modules\Html\ReviewForm;
use GeminiLabs\SiteReviews\Modules\Validator\DefaultValidator;
use GeminiLabs\SiteReviews\Request;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

beforeEach(fn () => resetPluginState());

test('condition contains', function () {
    $field = conditionField([
        'type' => 'text',
        'value' => 'foo bar',
    ]);
    $values = [
        '' => true,
        'foo' => true,
        'bar' => true,
        'foo bar' => true,
        'foo,bar' => false,
        'foobar' => false,
    ];
    foreach ($values as $value => $expected) {
        expect($expected === conditionIsValid($field, 'contains', $value))->toBeTrue();
    }
});

test('condition contains with multi field', function () {
    $field = conditionField([
        'type' => 'select',
        'multiple' => true,
        'options' => ['Foo', 'Bar', 'Abc', 'Xyz'],
        'value' => ['Foo', 'Bar'],
    ]);
    $values = [
        '' => true,
        'Foo' => true,
        'Bar' => true,
        'Abc, Foo' => true,
        'Abc' => false,
    ];
    foreach ($values as $value => $expected) {
        expect($expected === conditionIsValid($field, 'contains', $value))->toBeTrue();
    }
});

test('condition equals', function () {
    $field = conditionField([
        'type' => 'text',
        'value' => 'foo bar',
    ]);
    $values = [
        '' => false,
        'foo' => false,
        'bar' => false,
        'foo bar' => true,
    ];
    foreach ($values as $value => $expected) {
        expect($expected === conditionIsValid($field, 'equals', $value))->toBeTrue();
    }
    $field = conditionField([
        'type' => 'rating',
        'value' => 5,
    ]);
    $values = [
        '' => false,
        0 => false,
        '0' => false,
        4 => false,
        '4' => false,
        5 => true,
        '5' => true,
    ];
    foreach ($values as $value => $expected) {
        expect($expected === conditionIsValid($field, 'equals', $value))->toBeTrue();
    }
});

test('condition equals with multi field', function () {
    $field = conditionField([
        'type' => 'select',
        'multiple' => true,
        'options' => ['Foo', 'Bar', 'Abc', 'Xyz'],
        'value' => ['Foo', 'Bar'],
    ]);
    $values = [
        '' => false,
        'Foo' => false,
        'Bar' => false,
        'Abc, Foo' => false,
        'Foo,Bar' => true,
        'Foo, Bar' => true,
        'Bar, Foo' => true,
    ];
    foreach ($values as $value => $expected) {
        expect($expected === conditionIsValid($field, 'equals', $value))->toBeTrue();
    }
});

test('condition greater', function () {
    $field = conditionField([
        'type' => 'rating',
        'value' => 3,
    ]);
    $values = [
        '' => true,
        '0' => true,
        '1' => true,
        '4' => false,
        '5' => false,
        0 => true,
        1 => true,
        4 => false,
        5 => false,
    ];
    foreach ($values as $value => $expected) {
        expect($expected === conditionIsValid($field, 'greater', $value))->toBeTrue();
    }
});

test('condition less', function () {
    $field = conditionField([
        'type' => 'rating',
        'value' => 3,
    ]);
    $values = [
        '' => false,
        '0' => false,
        '4' => true,
        '5' => true,
        0 => false,
        4 => true,
        5 => true,
    ];
    foreach ($values as $value => $expected) {
        expect($expected === conditionIsValid($field, 'less', $value))->toBeTrue();
    }
});

test('condition not', function () {
    $field = conditionField([
        'type' => 'text',
        'value' => 'foo bar',
    ]);
    $values = [
        '' => true,
        'foo' => true,
        'bar' => true,
        'foo bar' => false,
    ];
    foreach ($values as $value => $expected) {
        expect($expected === conditionIsValid($field, 'not', $value))->toBeTrue();
    }
    $field = conditionField([
        'type' => 'rating',
        'value' => 5,
    ]);
    $values = [
        '' => true,
        0 => true,
        '0' => true,
        4 => true,
        '4' => true,
        5 => false,
        '5' => false,
    ];
    foreach ($values as $value => $expected) {
        expect($expected === conditionIsValid($field, 'not', $value))->toBeTrue();
    }
});

test('condition not with multi field', function () {
    $field = conditionField([
        'type' => 'select',
        'multiple' => true,
        'options' => ['Foo', 'Bar', 'Abc', 'Xyz'],
        'value' => ['Foo', 'Bar'],
    ]);
    $values = [
        '' => true,
        'Foo' => false,
        'Bar' => false,
        'Abc, Foo' => false,
        'Foo,Bar' => false,
        'Foo, Bar' => false,
        'Bar, Foo' => false,
        'Abc' => true,
        'Abc, Xyz' => true,
    ];
    foreach ($values as $value => $expected) {
        expect($expected === conditionIsValid($field, 'not', $value))->toBeTrue();
    }
});

test('condition overrides required', function () {
    $config = [
        'rating' => [
            'type' => 'rating',
            'required' => true,
        ],
        'title' => [
            'type' => 'text',
            'conditions' => 'all|rating:equals:5',
            'required' => true,
        ],
        'content' => [
            'type' => 'textarea',
            'conditions' => 'any|rating:equals:5',
            'required' => true,
        ],
    ];
    overrideConfig($config);
    $request = new Request([
        'rating' => 1,
    ]);
    expect(glsr(DefaultValidator::class, compact('request'))->isValid())->toBeTrue();
    restoreConfig();
});

test('multi field condition overrides required', function () {
    $config = [
        'rating' => [
            'type' => 'rating',
            'required' => true,
        ],
        'title' => [
            'type' => 'text',
            'conditions' => 'any|rating:equals:5|foo:equals:Bar',
            'required' => true,
        ],
        'foo' => [
            'type' => 'checkbox',
            'options' => [
                'Foo' => 'Foo',
                'Bar' => 'Bar',
                'Xyz' => 'Xyz',
            ],
            'required' => true,
        ],
    ];
    overrideConfig($config);
    $request = new Request([
        'rating' => 1,
        'foo' => 'Bar',
    ]);
    expect(glsr(DefaultValidator::class, compact('request'))->isValid())->toBeFalse();
    $request = new Request([
        'rating' => 1,
        'foo' => 'Xyz',
    ]);
    expect(glsr(DefaultValidator::class, compact('request'))->isValid())->toBeTrue();
    restoreConfig();
});

function conditionField(array $args): FieldContract
{
    return glsr(ReviewForm::class)->field('foobar', $args);
}

/**
 * The phpunit test case held the overriding closure and the saved required
 * fields on $this. override/restore are always paired within a test, so a
 * static store does the same job without a test-case property.
 */
function conditionStore(): \ArrayObject
{
    static $store;
    return $store ??= new \ArrayObject(['config' => null, 'required' => null]);
}

function overrideConfig(array $config): void
{
    $store = conditionStore();
    $store['config'] = fn () => $config;
    $store['required'] = glsr(OptionManager::class)->getArray('settings.forms.required');
    add_filter('site-reviews/review-form/config', $store['config'], 10);
    glsr(OptionManager::class)->set('settings.forms.required', []);
}

function restoreConfig(): void
{
    $store = conditionStore();
    if ($store['config']) {
        remove_filter('site-reviews/review-form/config', $store['config'], 10);
    }
    if (isset($store['required'])) {
        glsr(OptionManager::class)->set('settings.forms.required', $store['required']);
    }
    $store['config'] = null;
    $store['required'] = null;
}

function conditionIsValid(FieldContract $field, string $operator, $value): bool
{
    $values = wp_parse_args(compact('operator', 'value'), [
        'name' => 'foobar',
    ]);
    return (new FieldCondition($values, $field))->isValid();
}
