<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Modules\Html\Form;

/**
 * Test case for the Plugin.
 *
 * @group plugin
 */
class FormTest extends \WP_UnitTestCase
{
    protected $fields;

    public function testConditionContains()
    {
        $this->assertEquals(
            '<div class="glsr-form-wrap">'.
                '<form class="test-form glsr-form" method="post" enctype="multipart/form-data">'.
                    '<div class="glsr-form-message"></div>'.
                    '<div data-field="submit-button">'.
                        '<div class="wp-block-button">'.
                            '<button type="submit" class="glsr-button wp-block-button__link" aria-busy="false" data-loading="Please wait">'.
                                'Test'.
                            '</button>'.
                        '</div>'.
                    '</div>'.
                '</form>'.
            '</div>',
            $this->build([
                'button_text' => 'Test',
                'button_text_loading' => 'Please wait',
                'class' => 'test-form',
            ])
        );
    }

    protected function build(array $args = []): string
    {
        $this->removeFields();
        $form = new Form($args);
        $html = $form->build();
        $this->restoreFields();
        $parts = preg_split('/\R/', $html);
        $parts = array_map('trim', $parts);
        $html = implode('', $parts);
        return $html;
    }

    protected function removeFields(): void
    {
        $this->fields = fn () => '';
        add_filter('site-reviews/form/build/fields', $this->fields, 10);
    }

    protected function restoreFields(): void
    {
        if ($this->fields) {
            remove_filter('site-reviews/form/build/fields', $this->fields, 10);
        }
    }
}
