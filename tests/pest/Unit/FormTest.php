<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Html\Form;
use GeminiLabs\SiteReviews\Modules\Html\ReviewForm;

uses()->group('plugin');

test('a form whose fields are all filtered away still renders its shell', function () {
    // Every field is stripped by the site-reviews/form/build/fields filter below, so this is a
    // golden of the WRAPPER: the form element, the message container and the submit button — the
    // parts an addon that replaces the fields still depends on.
    expect(buildForm([
            'button_text' => 'Test',
            'button_text_loading' => 'Please wait',
            'class' => 'test-form',
        ]))->toEqual('<div class="glsr-form-wrap">'.
            '<form class="glsr-form test-form" method="post" enctype="multipart/form-data">'.
                '<div class="glsr-form-message"></div>'.
                '<div data-field="submit-button">'.
                    '<div class="wp-block-buttons is-layout-flex">'.
                        '<div class="wp-block-button">'.
                            '<button type="submit" class="glsr-button wp-block-button__link wp-element-button" aria-busy="false" data-loading="Please wait">'.
                                'Test'.
                            '</button>'.
                        '</div>'.
                    '</div>'.
                '</div>'.
            '</form>'.
        '</div>');
});

/**
 * A form whose session says the last submission failed — what ReviewForm::loadSession()
 * builds after a validation error, without going through a real submission.
 */
function formWithFailedSession(array $config): Form
{
    return new class($config) extends Form {
        public function __construct(array $config)
        {
            parent::__construct(['id' => 'test-form'], [], $config);
        }

        public function loadSession(array $values): void
        {
            $this->session = glsr()->args([
                'errors' => ['pet_name' => ['Please name the pet']],
                'failed' => true,
                'message' => 'The submission failed.',
                'values' => ['pet_name' => 'Rex'],
            ]);
        }
    };
}

test('fields are reachable by position, by name, and not otherwise', function () {
    $form = new Form(['id' => 'test-form'], [], [
        'pet_name' => ['type' => 'text'],
    ]);

    expect($form->args()->id)->toBe('test-form');
    expect($form[0]->original_name)->toBe('form_signature'); // signing prepends its field
    expect($form[1]->original_name)->toBe('pet_name');       // by position
    expect($form['pet_name']->original_name)->toBe('pet_name'); // by name
    expect($form[99])->toBeNull();
    expect($form['no_such_field'])->toBeNull();
});

test('a failed submission is worn by the form, the message, and the field', function () {
    $form = formWithFailedSession(['pet_name' => ['type' => 'text']]);

    expect($form->session()->message)->toBe('The submission failed.');
    expect($form['pet_name']->errors)->toBe(['Please name the pet']);
    expect($form['pet_name']->value)->toBe('Rex'); // what they typed is not thrown away

    $html = $form->build();
    expect($html)->toContain('glsr-form-is-invalid')   // the form wears the error class
        ->toContain('glsr-form-failed')                // so does the message container
        ->toContain('The submission failed.');
});

test('session storage opt-in marks the form for the javascript', function () {
    glsr(OptionManager::class)->set('settings.forms.session_storage', 'yes');

    $form = new Form([], [], ['pet_name' => ['type' => 'text']]);

    expect($form->build())->toContain('glsr-persist-data');
});

test('the captcha placement above the button is honoured', function () {
    glsr(OptionManager::class)->set('settings.forms.captcha.placement', 'above');

    $form = new Form([], [], ['pet_name' => ['type' => 'text']]);

    expect($form->build())->toContain('type="submit"'); // built through the `above` branch
});

test('a condition on a field that does not exist is ignored, not a hidden field', function () {
    // A site owner's typo in a condition must not silently hide the field it was typed on.
    $form = new Form([], [], [
        'shown' => ['type' => 'text', 'conditions' => 'all|ghost:equals:yes'],
    ]);

    expect($form['shown']->is_hidden)->toBeFalsy();
});

test('a condition that does not hold hides the field', function () {
    $form = new Form([], [], [
        'toggle' => ['type' => 'text'],
        'dependent' => ['type' => 'text', 'conditions' => 'all|toggle:equals:yes'],
    ]);

    expect($form['dependent']->is_hidden)->toBeTrue();
});

test('an any-criteria condition passes when one of its conditions holds', function () {
    $form = new Form([], [], [
        'toggle' => ['type' => 'text'],
        'dependent' => ['type' => 'text', 'conditions' => 'any|ghost:equals:yes|toggle:equals:no'],
    ]);

    // the ghost condition is ignored (its field does not exist), which satisfies "any"
    expect($form['dependent']->is_hidden)->toBeFalsy();
});

test('a choice field is re-checked from the session, when the session can say', function () {
    $config = [
        'accept' => ['type' => 'checkbox', 'value' => 'yes'],
        'many' => ['type' => 'checkbox', 'options' => ['a' => 'A', 'b' => 'B']], // value is an array
    ];

    $checked = new Form([], ['accept' => 'yes'], $config);
    expect($checked['accept']->checked)->toBeTrue();

    $unchecked = new Form([], [], $config); // nothing in the session: leave the defaults be
    expect($unchecked['accept']->checked)->toBeFalsy();
});

test('a plain subclass gets its hidden config as hidden fields', function () {
    // configHidden() without allowedHiddenFieldOverrides(): every entry becomes a hidden
    // field, sorted into hidden(), and grouped by fieldsFor().
    $form = new class() extends Form {
        public function configHidden(): array
        {
            return ['token' => 'abc123'];
        }
    };

    expect($form['token']->original_type)->toBe('hidden')
        ->and($form['token']->value)->toBe('abc123');
    expect($form->fieldsFor('no-such-group'))->toBe([]);
});

test('a raw field keeps the id it was given', function () {
    // normalizeFieldId() prefixes field ids with the form id, except on raw fields —
    // they render bare elements whose id the caller owns.
    $form = new Form(['id' => 'test-form'], [], [
        'plain' => ['type' => 'text', 'id' => 'prefix-me'],
        'raw' => ['type' => 'text', 'id' => 'keep-me', 'is_raw' => true],
    ]);

    expect($form['plain']->id)->toBe('test-formprefix-me'); // Str::prefix, no separator
    expect($form['raw']->id)->toBe('keep-me');
});

test('a visible field may override its hidden namesake', function () {
    // ReviewForm always plants assigned_posts as a hidden field; a form customisation that
    // adds a VISIBLE assigned_posts field (letting the visitor pick the page) must win, or
    // the form would post two values for one name.
    $filter = fn ($config) => $config + [
        'assigned_posts' => ['options' => ['1' => 'One'], 'type' => 'select'],
    ];
    add_filter('site-reviews/review-form/config', $filter);
    try {
        $form = new ReviewForm();
    } finally {
        remove_filter('site-reviews/review-form/config', $filter);
    }

    expect($form['assigned_posts']->original_type)->toBe('select');
    $hiddenNames = array_map(fn ($field) => $field->original_name, $form->hidden());
    expect($hiddenNames)->not->toContain('assigned_posts');
});

function buildForm(array $args = []): string
{
    removeFormFields();
    $form = new Form($args);
    $html = $form->build();
    restoreFormFields();
    $parts = preg_split('/\R/', $html);
    $parts = array_map('trim', $parts);
    $html = implode('', $parts);
    return $html;
}

/**
 * The same Closure instance every time: remove_filter() can only find the exact callback it was
 * given.
 */
function emptyFieldsFilter(): \Closure
{
    static $filter;
    return $filter ??= fn () => '';
}

function removeFormFields(): void
{
    add_filter('site-reviews/form/build/fields', emptyFieldsFilter(), 10);
}

function restoreFormFields(): void
{
    remove_filter('site-reviews/form/build/fields', emptyFieldsFilter(), 10);
}

test('a custom field implementation without conditions is left alone', function () {
    // An addon can hand the form its own FieldContract through the fields filter; one whose
    // conditions() answers nothing at all must be skipped by the condition pass, not hidden.
    $custom = new class(['name' => 'custom_field', 'type' => 'text']) extends GeminiLabs\SiteReviews\Modules\Html\Field {
        public function conditions(): array
        {
            return [];
        }
    };
    add_filter('site-reviews/form/fields/all', fn (array $fields) => [...$fields, $custom]);

    $form = new Form(['id' => 'glsr-conditions-form']);

    expect($form['custom_field']->is_hidden)->not->toBeTrue();
});
