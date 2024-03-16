<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Compatibility;
use GeminiLabs\SiteReviews\Contracts\BuilderContract;
use GeminiLabs\SiteReviews\Controllers\PublicController;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

/**
 * Test case for the Plugin.
 * @group plugin
 */
class BuilderTest extends \WP_UnitTestCase
{
    public function set_up(): void
    {
        parent::set_up();
        // Remove styled element classes
        // glsr(Compatibility::class)->removeHook('site-reviews/builder', 'modifyBuilder', PublicController::class);
    }

    public function test_build_html_element(): void
    {
        $this->assertEquals('<span></span>', $this->builder()->span());
        $this->assertEquals('<span>foo</span>', $this->builder()->span('foo'));
        $this->assertEquals(
            '<span class="xxx">foo</span>',
            $this->builder()->span('foo', [
                'class' => 'xxx',
            ])
        );
        $this->assertEquals(
            '<span class="xxx">foo</span>',
            $this->builder()->span('foo', [
                'class' => 'xxx',
                'text' => 'bar',
            ])
        );
        $this->assertEquals(
            '<span class="xxx"></span>',
            $this->builder()->span([
                'class' => 'xxx',
                'foo' => 'bar',
            ])
        );
    }

    public function test_build_input(): void
    {
        $this->assertEquals(
            '<input type="text" value="" />',
            $this->builder()->input()
        );
        $this->assertEquals(
            '<input type="text" id="foo" name="foo" value="" />',
            $this->builder()->input([
                'id' => 'foo',
                'name' => 'foo',
            ])
        );
        $this->assertEquals(
            '<input type="submit" id="foo" name="bar" value="" />',
            $this->builder()->input([
                'id' => 'foo',
                'max' => 10, // invalid for submit type
                'min' => 0, // invalid for submit type
                'name' => 'bar',
                'type' => 'submit',
                'step' => 2, // invalid for submit type
            ])
        );
    }

    public function test_build_input_choice(): void
    {
        $this->assertEquals(
            '<input type="checkbox" id="foo" name="foo" value="" />',
            $this->builder()->input([
                'id' => 'foo',
                'name' => 'foo',
                'type' => 'checkbox',
            ])
        );
        $this->assertEquals(
            '<label for="foo">'.
                '<input type="checkbox" id="foo" checked name="foo" value="1" /> bar'.
            '</label>',
            $this->builder()->input([
                'checked' => true,
                'id' => 'foo',
                'label' => 'bar',
                'name' => 'foo',
                'type' => 'checkbox',
                'value' => 1,
            ])
        );
    }

    public function test_build_input_choices(): void
    {
        $this->assertEquals(
            '<label for="foo-1">'.
                '<input type="checkbox" id="foo-1" name="foo" value="a" /> A'.
            '</label>',
            $this->builder()->input([
                'id' => 'foo',
                'name' => 'foo',
                'options' => [
                    'a' => 'A',
                ],
                'type' => 'checkbox',
            ])
        );
        $this->assertEquals(
            '<label for="foo-1">'.
                '<input type="checkbox" id="foo-1" name="foo" value="a" /> A'.
            '</label>'.
            '<label for="foo-2">'.
                '<input type="checkbox" id="foo-2" name="foo" value="b" /> B'.
            '</label>',
            $this->builder()->input([
                'id' => 'foo',
                'name' => 'foo',
                'options' => [
                    'a' => 'A',
                    'b' => 'B',
                ],
                'type' => 'checkbox',
            ])
        );
    }

    public function test_build_input_choices_where_value_determines_checked(): void
    {
        $this->assertEquals(
            '<label for="foo-1">'.
                '<input type="checkbox" id="foo-1" name="foo" value="a" /> A'.
            '</label>',
            $this->builder()->input([
                'id' => 'foo',
                'label' => 'bar',
                'name' => 'foo',
                'options' => [
                    'a' => 'A',
                ],
                'type' => 'checkbox',
                'value' => 1,
            ])
        );
        $this->assertEquals(
            '<label for="foo-1">'.
                '<input type="checkbox" id="foo-1" checked name="foo" value="a" /> A'.
            '</label>'.
            '<label for="foo-2">'.
                '<input type="checkbox" id="foo-2" name="foo" value="b" /> B'.
            '</label>',
            $this->builder()->input([
                'id' => 'foo',
                'label' => 'bar',
                'name' => 'foo',
                'options' => [
                    'a' => 'A',
                    'b' => 'B',
                ],
                'type' => 'checkbox',
                'value' => 'a',
            ])
        );
        $this->assertEquals(
            '<label for="foo-1">'.
                '<input type="checkbox" id="foo-1" checked name="foo" value="a" /> A'.
            '</label>'.
            '<label for="foo-2">'.
                '<input type="checkbox" id="foo-2" checked name="foo" value="b" /> B'.
            '</label>',
            $this->builder()->input([
                'id' => 'foo',
                'label' => 'bar',
                'name' => 'foo',
                'options' => [
                    'a' => 'A',
                    'b' => 'B',
                ],
                'type' => 'checkbox',
                'value' => ['a', 'b'],
            ])
        );
    }

    public function test_build_input_with_invalid_type(): void
    {
        $this->assertEquals(
            '<label for="foo">bar</label>'.
            '<input type="text" id="foo" name="foo" value="1" />',
            $this->builder()->input([
                'id' => 'foo',
                'label' => 'bar',
                'name' => 'foo',
                'type' => 'rating', // invalid input type
                'value' => 1,
            ])
        );
    }

    public function test_build_select(): void
    {
        $this->assertEquals(
            '<label for="foo">Select one</label>'.
            '<select id="foo" name="bar">'.
                '<option value="3">Three</option>'.
                '<option title="The Number 8" value="8">Eight</option>'.
                '<optgroup label="9">'.
                    '<option value="0">Nine</option>'.
                '</optgroup>'.
                '<optgroup label="Letters">'.
                    '<option value="a">A</option>'.
                    '<option value="b">B</option>'.
                    '<option title="The Letter C" value="c">C</option>'.
                '</optgroup>'.
            '</select>',
            $this->builder()->select([
                'id' => 'foo',
                'label' => 'Select one',
                'name' => 'bar',
                'options' => [
                    3 => 'Three',
                    8 => [
                        'text' => 'Eight',
                        'title' => 'The Number 8',
                    ],
                    9 => [
                        'Nine',
                    ],
                    'Letters' => [
                        'a' => 'A',
                        'b' => 'B',
                        'c' => [
                            'text' => 'C',
                            'title' => 'The Letter C',
                        ],
                        [
                            'd' => 'D',
                            'e' => 'E',
                            'f' => [
                                'text' => 'F',
                                'title' => 'The Letter F',
                            ],
                        ],
                    ],
                ],
            ])
        );
    }

    public function test_build_textarea(): void
    {
        $this->assertEquals(
            '<label for="foo">bar</label>'.
            '<textarea id="foo" name="foo">foobar</textarea>',
            $this->builder()->textarea([
                'id' => 'foo',
                'label' => 'bar',
                'name' => 'foo',
                'value' => 'foobar',
            ])
        );
    }

    protected function builder(): BuilderContract
    {
        return new Builder();
    }
}
