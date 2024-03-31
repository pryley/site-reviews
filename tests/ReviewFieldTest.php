<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Contracts\FieldContract;
use GeminiLabs\SiteReviews\Modules\Html\ReviewField;

/**
 * Test case for the Plugin.
 * @group plugin
 */
class ReviewFieldTest extends \WP_UnitTestCase
{
    public function test_build_checkbox(): void
    {
        $this->assertEquals(
            '<div class="glsr-field glsr-field-choice" data-field="foobar">'.
                '<span class="glsr-field-checkbox">'.
                    '<label for="site-reviews-foobar-1">'.
                        '<span><input type="checkbox" id="site-reviews-foobar-1" name="site-reviews[foobar]" value="1" />&#8203;</span> <!-- zero-space character used for alignment -->'.
                        '<span>Foobar</span>'.
                    '</label>'.
                '</span>'.
                '<div class="glsr-field-error"></div>'.
            '</div>',
            $this->build([
                'label' => 'Foobar',
                'name' => 'foobar',
                'type' => 'checkbox',
            ])
        );
    }

    public function test_build_checkboxes(): void
    {
        $this->assertEquals(
            '<div class="glsr-field glsr-field-choice" data-field="foobar">'.
                '<label class="glsr-label" for="">'.
                    '<span>Foobar</span>'.
                '</label>'.
                '<span class="glsr-field-checkbox">'.
                    '<label for="site-reviews-foobar-1">'.
                        '<span><input type="checkbox" id="site-reviews-foobar-1" name="site-reviews[foobar][]" value="foo" />&#8203;</span> <!-- zero-space character used for alignment -->'.
                        '<span>Foo</span>'.
                    '</label>'.
                '</span>'.
                '<span class="glsr-field-checkbox">'.
                    '<label for="site-reviews-foobar-2">'.
                        '<span><input type="checkbox" id="site-reviews-foobar-2" name="site-reviews[foobar][]" value="bar" />&#8203;</span> <!-- zero-space character used for alignment -->'.
                        '<span>Bar</span>'.
                    '</label>'.
                '</span>'.
                '<div class="glsr-field-error"></div>'.
            '</div>',
            $this->build([
                'label' => 'Foobar',
                'name' => 'foobar',
                'type' => 'checkbox',
                'options' => [
                    'foo' => 'Foo',
                    'bar' => 'Bar',
                ],
            ])
        );
    }

    public function test_build_checkboxes_with_descriptions(): void
    {
        $this->assertEquals(
            '<div class="glsr-field glsr-field-choice" data-field="foobar">'.
                '<label class="glsr-label" for="">'.
                    '<span>Foobar</span>'.
                '</label>'.
                '<span class="glsr-field-checkbox">'.
                    '<label for="site-reviews-foobar-1">'.
                        '<span><input type="checkbox" id="site-reviews-foobar-1" name="site-reviews[foobar][]" value="foo" />&#8203;</span> <!-- zero-space character used for alignment -->'.
                        '<span>'.
                            '<span>Foo</span>'.
                            '<span>this is foo</span>'.
                        '</span>'.
                    '</label>'.
                '</span>'.
                '<span class="glsr-field-checkbox">'.
                    '<label for="site-reviews-foobar-2">'.
                        '<span><input type="checkbox" id="site-reviews-foobar-2" name="site-reviews[foobar][]" value="bar" />&#8203;</span> <!-- zero-space character used for alignment -->'.
                        '<span>'.
                            '<span>Bar</span>'.
                            '<span>this is bar</span>'.
                        '</span>'.
                    '</label>'.
                '</span>'.
                '<div class="glsr-field-error"></div>'.
            '</div>',
            $this->build([
                'label' => 'Foobar',
                'name' => 'foobar',
                'type' => 'checkbox',
                'options' => [
                    'foo' => ['Foo', 'this is foo'],
                    'bar' => ['Bar', 'this is bar'],
                ],
            ])
        );
    }

    public function test_build_code(): void
    {
        $this->assertEquals(
            '<div class="glsr-field glsr-field-code" data-field="foobar">'.
                '<label class="glsr-label" for="site-reviews-foobar">'.
                    '<span>Foobar</span>'.
                '</label>'.
                '<textarea class="glsr-textarea" id="site-reviews-foobar" name="site-reviews[foobar]"></textarea>'.
                '<div class="glsr-field-error"></div>'.
            '</div>',
            $this->build([
                'label' => 'Foobar',
                'name' => 'foobar',
                'type' => 'code',
            ])
        );
    }

    public function test_build_color(): void
    {
        $this->assertEquals(
            '<div class="glsr-field glsr-field-color" data-field="foobar">'.
                '<label class="glsr-label" for="site-reviews-foobar">'.
                    '<span>Foobar</span>'.
                '</label>'.
                '<input type="color" class="glsr-input glsr-input-color" id="site-reviews-foobar" name="site-reviews[foobar]" value="" />'.
                '<div class="glsr-field-error"></div>'.
            '</div>',
            $this->build([
                'label' => 'Foobar',
                'name' => 'foobar',
                'type' => 'color',
            ])
        );
    }

    public function test_build_email(): void
    {
        $this->assertEquals(
            '<div class="glsr-field glsr-field-email" data-field="foobar">'.
                '<label class="glsr-label" for="site-reviews-foobar">'.
                    '<span>Foobar</span>'.
                '</label>'.
                '<input type="email" class="glsr-input glsr-input-email" id="site-reviews-foobar" name="site-reviews[foobar]" value="" />'.
                '<div class="glsr-field-error"></div>'.
            '</div>',
            $this->build([
                'label' => 'Foobar',
                'name' => 'foobar',
                'type' => 'email',
            ])
        );
    }

    public function test_build_hidden(): void
    {
        $this->assertEquals(
            '<input type="hidden" name="site-reviews[foobar]" value="" />',
            $this->build([
                'label' => 'Foobar',
                'name' => 'foobar',
                'type' => 'hidden',
            ])
        );
    }

    public function test_build_number(): void
    {
        $this->assertEquals(
            '<div class="glsr-field glsr-field-number" data-field="foobar">'.
                '<label class="glsr-label" for="site-reviews-foobar">'.
                    '<span>Foobar</span>'.
                '</label>'.
                '<input type="number" class="glsr-input glsr-input-number" id="site-reviews-foobar" name="site-reviews[foobar]" max="10" min="0" step="1" value="" />'.
                '<div class="glsr-field-error"></div>'.
            '</div>',
            $this->build([
                'label' => 'Foobar',
                'max' => 10,
                'min' => 0,
                'name' => 'foobar',
                'step' => 1,
                'type' => 'number',
            ])
        );
    }

    public function test_build_password(): void
    {
        $this->assertEquals(
            '<div class="glsr-field glsr-field-password" data-field="foobar">'.
                '<label class="glsr-label" for="site-reviews-foobar">'.
                    '<span>Foobar</span>'.
                '</label>'.
                '<input type="password" class="glsr-input glsr-input-password" id="site-reviews-foobar" name="site-reviews[foobar]" autocomplete="off" value="" />'.
                '<div class="glsr-field-error"></div>'.
            '</div>',
            $this->build([
                'label' => 'Foobar',
                'name' => 'foobar',
                'type' => 'password',
            ])
        );
    }

    public function test_build_radio(): void
    {
        $this->assertEquals(
            '<div class="glsr-field glsr-field-choice" data-field="foobar">'.
                '<span class="glsr-field-radio">'.
                    '<label for="site-reviews-foobar-1">'.
                        '<span><input type="radio" id="site-reviews-foobar-1" name="site-reviews[foobar]" value="1" />&#8203;</span> <!-- zero-space character used for alignment -->'.
                        '<span>Foobar</span>'.
                    '</label>'.
                '</span>'.
                '<div class="glsr-field-error"></div>'.
            '</div>',
            $this->build([
                'label' => 'Foobar',
                'name' => 'foobar',
                'type' => 'radio',
            ])
        );
    }

    public function test_build_radios(): void
    {
        $this->assertEquals(
            '<div class="glsr-field glsr-field-choice" data-field="foobar">'.
                '<label class="glsr-label" for="">'.
                    '<span>Foobar</span>'.
                '</label>'.
                '<span class="glsr-field-radio">'.
                    '<label for="site-reviews-foobar-1">'.
                        '<span><input type="radio" id="site-reviews-foobar-1" name="site-reviews[foobar]" value="foo" />&#8203;</span> <!-- zero-space character used for alignment -->'.
                        '<span>Foo</span>'.
                    '</label>'.
                '</span>'.
                '<span class="glsr-field-radio">'.
                    '<label for="site-reviews-foobar-2">'.
                        '<span><input type="radio" id="site-reviews-foobar-2" name="site-reviews[foobar]" value="bar" />&#8203;</span> <!-- zero-space character used for alignment -->'.
                        '<span>Bar</span>'.
                    '</label>'.
                '</span>'.
                '<div class="glsr-field-error"></div>'.
            '</div>',
            $this->build([
                'label' => 'Foobar',
                'name' => 'foobar',
                'type' => 'radio',
                'options' => [
                    'foo' => 'Foo',
                    'bar' => 'Bar',
                ],
            ])
        );
    }

    public function test_build_rating(): void
    {
        $this->assertEquals(
            '<div class="glsr-field glsr-field-rating" data-field="foobar">'.
                '<label class="glsr-label" for="site-reviews-foobar">'.
                    '<span>Your overall rating</span>'.
                '</label>'.
                '<select class="browser-default no_wrap no-wrap glsr-select" id="site-reviews-foobar" name="site-reviews[foobar]">'.
                    '<option value="">Select a Rating</option>'.
                    '<option value="5">5 Stars</option>'.
                    '<option value="4">4 Stars</option>'.
                    '<option value="3">3 Stars</option>'.
                    '<option value="2">2 Stars</option>'.
                    '<option value="1">1 Star</option>'.
                '</select>'.
                '<div class="glsr-field-error"></div>'.
            '</div>',
            $this->build([
                'label' => 'Your overall rating',
                'name' => 'foobar',
                'type' => 'rating',
            ])
        );
    }

    public function test_build_select(): void
    {
        $this->assertEquals(
            '<div class="glsr-field glsr-field-select" data-field="foobar">'.
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
            '</div>',
            $this->build([
                'label' => 'Foobar',
                'name' => 'foobar',
                'options' => [
                    'red' => 'Red',
                    'green' => 'Green',
                    'blue' => 'Blue',
                ],
                'placeholder' => 'Select a color',
                'type' => 'select',
            ])
        );
    }

    public function test_build_text(): void
    {
        $this->assertEquals(
            '<div class="glsr-field glsr-field-text" data-field="foobar">'.
                '<label class="glsr-label" for="site-reviews-foobar">'.
                    '<span>Foobar</span>'.
                '</label>'.
                '<input type="text" class="glsr-input glsr-input-text" id="site-reviews-foobar" name="site-reviews[foobar]" value="" />'.
                '<div class="glsr-field-error"></div>'.
            '</div>',
            $this->build([
                'label' => 'Foobar',
                'name' => 'foobar',
                'type' => 'text',
            ])
        );
    }

    public function test_build_textarea(): void
    {
        $this->assertEquals(
            '<div class="glsr-field glsr-field-textarea" data-field="foobar">'.
                '<label class="glsr-label" for="site-reviews-foobar">'.
                    '<span>Foobar</span>'.
                '</label>'.
                '<textarea class="glsr-textarea" id="site-reviews-foobar" name="site-reviews[foobar]" placeholder="Foobar..."></textarea>'.
                '<div class="glsr-field-error"></div>'.
            '</div>',
            $this->build([
                'label' => 'Foobar',
                'name' => 'foobar',
                'placeholder' => 'Foobar...',
                'type' => 'textarea',
            ])
        );
    }

    public function test_build_toggle(): void
    {
        $this->assertEquals(
            '<div class="glsr-field glsr-field-choice glsr-required" data-field="foobar">'.
                '<span class="glsr-field-toggle">'.
                    '<span class="glsr-toggle">'.
                        '<label for="site-reviews-foobar-1">Foobar</label>'.
                        '<span class="glsr-toggle-switch">'.
                            '<input type="checkbox" id="site-reviews-foobar-1" name="site-reviews[foobar]" required value="1" /> &#8203; <!-- zero-space character used for alignment -->'.
                            '<span class="glsr-toggle-track"></span>'.
                        '</span>'.
                    '</span>'.
                '</span>'.
                '<div class="glsr-field-error"></div>'.
            '</div>',
            $this->build([
                'label' => 'Foobar',
                'name' => 'foobar',
                'required' => true,
                'type' => 'toggle',
            ])
        );
    }

    public function test_build_toggles(): void
    {
        $this->assertEquals(
            '<div class="glsr-field glsr-field-choice" data-field="foobar">'.
                '<label class="glsr-label" for="">'.
                    '<span>Foobar</span>'.
                '</label>'.
                '<span class="glsr-field-toggle">'.
                    '<span class="glsr-toggle">'.
                        '<label for="site-reviews-foobar-1">Foo</label>'.
                        '<span class="glsr-toggle-switch">'.
                            '<input type="checkbox" id="site-reviews-foobar-1" name="site-reviews[foobar][]" value="foo" /> &#8203; <!-- zero-space character used for alignment -->'.
                            '<span class="glsr-toggle-track"></span>'.
                        '</span>'.
                    '</span>'.
                '</span>'.
                '<span class="glsr-field-toggle">'.
                    '<span class="glsr-toggle">'.
                        '<label for="site-reviews-foobar-2">Bar</label>'.
                        '<span class="glsr-toggle-switch">'.
                            '<input type="checkbox" id="site-reviews-foobar-2" name="site-reviews[foobar][]" value="bar" /> &#8203; <!-- zero-space character used for alignment -->'.
                            '<span class="glsr-toggle-track"></span>'.
                        '</span>'.
                    '</span>'.
                '</span>'.
                '<div class="glsr-field-error"></div>'.
            '</div>',
            $this->build([
                'label' => 'Foobar',
                'name' => 'foobar',
                'type' => 'toggle',
                'options' => [
                    'foo' => 'Foo',
                    'bar' => 'Bar',
                ],
            ])
        );
    }

    public function test_build_unknown(): void
    {
        $this->assertEquals(
            '',
            $this->build([
                'label' => 'Foobar',
                'name' => 'foobar',
                'type' => 'unknown',
            ])
        );
    }

    public function test_build_url(): void
    {
        $this->assertEquals(
            '<div class="glsr-field glsr-field-url" data-field="foobar">'.
                '<label class="glsr-label" for="site-reviews-foobar">'.
                    '<span>Foobar</span>'.
                '</label>'.
                '<input type="url" class="glsr-input glsr-input-url" id="site-reviews-foobar" name="site-reviews[foobar]" value="" />'.
                '<div class="glsr-field-error"></div>'.
            '</div>',
            $this->build([
                'label' => 'Foobar',
                'name' => 'foobar',
                'type' => 'url',
            ])
        );
    }

    public function test_build_yes_no(): void
    {
        $this->assertEquals(
            '<div class="glsr-field glsr-field-choice" data-field="foobar">'.
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
            '</div>',
            $this->build([
                'label' => 'Foobar',
                'name' => 'foobar',
                'type' => 'yes_no',
                'options' => [ // this should be ignored in a yes_no field
                    'x' => 'X',
                    'y' => 'Y',
                    'z' => 'Z',
                ],
            ])
        );
    }

    protected function build(array $args = []): string
    {
        $html = $this->field($args)->build();
        $parts = preg_split('/\R/', $html);
        $parts = array_map('trim', $parts);
        return implode('', $parts);
    }

    protected function field(array $args = []): FieldContract
    {
        return new ReviewField($args);
    }
}
