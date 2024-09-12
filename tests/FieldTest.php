<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Contracts\FieldContract;
use GeminiLabs\SiteReviews\Modules\Html\Field;

/**
 * Test case for the Plugin.
 * @group plugin
 */
class FieldTest extends \WP_UnitTestCase
{
    public function test_build_checkbox(): void
    {
        $this->assertEquals(
            '<div>'.
                '<label for="site-reviews-foobar-1">'.
                    '<input type="checkbox" id="site-reviews-foobar-1" name="site-reviews[foobar]" value="1" /> Foobar'.
                '</label>'.
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
            '<div>'.
                '<label>Foobar</label>'.
                '<label for="site-reviews-foobar-1">'.
                    '<input type="checkbox" id="site-reviews-foobar-1" name="site-reviews[foobar][]" value="foo" /> Foo'.
                '</label>'.
                '<label for="site-reviews-foobar-2">'.
                    '<input type="checkbox" id="site-reviews-foobar-2" name="site-reviews[foobar][]" value="bar" /> Bar'.
                '</label>'.
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

    public function test_build_code(): void
    {
        $this->assertEquals(
            '<div>'.
                '<label for="site-reviews-foobar">Foobar</label>'.
                '<textarea id="site-reviews-foobar" name="site-reviews[foobar]"></textarea>'.
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
            '<div>'.
                '<label for="site-reviews-foobar">Foobar</label>'.
                '<input type="color" id="site-reviews-foobar" name="site-reviews[foobar]" value="" />'.
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
            '<div>'.
                '<label for="site-reviews-foobar">Foobar</label>'.
                '<input type="email" id="site-reviews-foobar" name="site-reviews[foobar]" value="" />'.
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
            '<div>'.
                '<label for="site-reviews-foobar">Foobar</label>'.
                '<input type="number" id="site-reviews-foobar" name="site-reviews[foobar]" max="10" min="0" step="1" value="" />'.
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
            '<div>'.
                '<label for="site-reviews-foobar">Foobar</label>'.
                '<input type="password" id="site-reviews-foobar" name="site-reviews[foobar]" autocomplete="off" value="" />'.
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
            '<div>'.
                '<label for="site-reviews-foobar-1">'.
                    '<input type="radio" id="site-reviews-foobar-1" name="site-reviews[foobar]" value="1" /> Foobar'.
                '</label>'.
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
            '<div>'.
                '<label>Foobar</label>'.
                '<label for="site-reviews-foobar-1">'.
                    '<input type="radio" id="site-reviews-foobar-1" name="site-reviews[foobar]" value="foo" /> Foo'.
                '</label>'.
                '<label for="site-reviews-foobar-2">'.
                    '<input type="radio" id="site-reviews-foobar-2" name="site-reviews[foobar]" value="bar" /> Bar'.
                '</label>'.
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
            '<div>'.
                '<label for="site-reviews-foobar">Your overall rating</label>'.
                '<select class="browser-default disable-select no_wrap no-wrap" id="site-reviews-foobar" name="site-reviews[foobar]">'.
                    '<option value="">Select a Rating</option>'.
                    '<option value="5">5 Stars</option>'.
                    '<option value="4">4 Stars</option>'.
                    '<option value="3">3 Stars</option>'.
                    '<option value="2">2 Stars</option>'.
                    '<option value="1">1 Star</option>'.
                '</select>'.
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
            '<div>'.
                '<label for="site-reviews-foobar">Color</label>'.
                '<select id="site-reviews-foobar" name="site-reviews[foobar]">'.
                    '<option value="">Select a color</option>'.
                    '<option value="red">Red</option>'.
                    '<option value="green">Green</option>'.
                    '<option value="blue">Blue</option>'.
                '</select>'.
            '</div>',
            $this->build([
                'label' => 'Color',
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
            '<div>'.
                '<label for="site-reviews-foobar">Foobar</label>'.
                '<input type="text" id="site-reviews-foobar" name="site-reviews[foobar]" value="" />'.
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
            '<div>'.
                '<label for="site-reviews-foobar">Foobar</label>'.
                '<textarea id="site-reviews-foobar" name="site-reviews[foobar]" placeholder="Foobar..."></textarea>'.
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
            '<div>'.
                '<label for="site-reviews-foobar-1">'.
                    '<input type="checkbox" id="site-reviews-foobar-1" name="site-reviews[foobar]" required value="1" /> Foobar'.
                '</label>'.
            '</div>',
            $this->build([
                'label' => 'Foobar',
                'name' => 'foobar',
                'required' => true,
                'type' => 'toggle',
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
            '<div>'.
                '<label for="site-reviews-foobar">Foobar</label>'.
                '<input type="url" id="site-reviews-foobar" name="site-reviews[foobar]" value="" />'.
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
            '<div>'.
                '<label>Foobar</label>'.
                '<label for="site-reviews-foobar-1">'.
                    '<input type="radio" id="site-reviews-foobar-1" name="site-reviews[foobar]" value="no" /> No'.
                '</label>'.
                '<label for="site-reviews-foobar-2">'.
                    '<input type="radio" id="site-reviews-foobar-2" name="site-reviews[foobar]" value="yes" /> Yes'.
                '</label>'.
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
        return new Field($args);
    }
}
