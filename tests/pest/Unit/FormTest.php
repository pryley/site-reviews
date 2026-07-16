<?php

use GeminiLabs\SiteReviews\Modules\Html\Form;

uses()->group('plugin');

test('condition contains', function () {
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
