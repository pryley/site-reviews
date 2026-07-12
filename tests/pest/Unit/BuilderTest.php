<?php

use GeminiLabs\SiteReviews\Compatibility;
use GeminiLabs\SiteReviews\Contracts\BuilderContract;
use GeminiLabs\SiteReviews\Controllers\PublicController;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

uses()->group('plugin');

test('build html element', function () {
    expect(builder()->span())->toEqual('<span></span>');
    expect(builder()->span('foo'))->toEqual('<span>foo</span>');
    expect(builder()->span('foo', [ 'class' => 'xxx', ]))->toEqual('<span class="xxx">foo</span>');
    expect(builder()->span('foo', [
            'class' => 'xxx',
            'text' => 'bar',
        ]))->toEqual('<span class="xxx">foo</span>');
    expect(builder()->span([ 'class' => 'xxx', 'foo' => 'bar', ]))->toEqual('<span class="xxx"></span>');
});

test('build input', function () {
    expect(builder()->input())->toEqual('<input type="text" value="" />');
    expect(builder()->input([
            'id' => 'foo',
            'name' => 'foo',
        ]))->toEqual('<input type="text" id="foo" name="foo" value="" />');
    expect(builder()->input([
            'id' => 'foo',
            'max' => 10, // invalid for submit type
            'min' => 0, // invalid for submit type
            'name' => 'bar',
            'type' => 'submit',
            'step' => 2, // invalid for submit type
        ]))->toEqual('<input type="submit" id="foo" name="bar" value="" />');
});

test('build input choice', function () {
    expect(builder()->input([
            'id' => 'foo',
            'name' => 'foo',
            'type' => 'checkbox',
        ]))->toEqual('<input type="checkbox" id="foo" name="foo" value="" />');
    expect(builder()->input([
            'checked' => true,
            'id' => 'foo',
            'label' => 'bar',
            'name' => 'foo',
            'type' => 'checkbox',
            'value' => 1,
        ]))->toEqual('<label for="foo">'.
            '<input type="checkbox" id="foo" checked name="foo" value="1" /> bar'.
        '</label>');
});

test('build input choices', function () {
    expect(builder()->input([
            'id' => 'foo',
            'name' => 'foo',
            'options' => [
                'a' => 'A',
            ],
            'type' => 'checkbox',
        ]))->toEqual('<label for="foo-1">'.
            '<input type="checkbox" id="foo-1" name="foo" value="a" /> A'.
        '</label>');
    expect(builder()->input([
            'id' => 'foo',
            'name' => 'foo',
            'options' => [
                'a' => 'A',
                'b' => 'B',
            ],
            'type' => 'checkbox',
        ]))->toEqual('<label for="foo-1">'.
            '<input type="checkbox" id="foo-1" name="foo" value="a" /> A'.
        '</label>'.
        '<label for="foo-2">'.
            '<input type="checkbox" id="foo-2" name="foo" value="b" /> B'.
        '</label>');
    expect(builder()->input([
            'id' => 'foo',
            'name' => 'foo',
            'options' => [
                'a' => ['A', 'description'],
                'b' => ['B', 'description'],
            ],
            'type' => 'checkbox',
        ]))->toEqual('<label for="foo-1">'.
            '<input type="checkbox" id="foo-1" name="foo" value="a" /> A, description'.
        '</label>'.
        '<label for="foo-2">'.
            '<input type="checkbox" id="foo-2" name="foo" value="b" /> B, description'.
        '</label>');
});

test('build input choices where value determines checked', function () {
    expect(builder()->input([
            'id' => 'foo',
            'label' => 'bar',
            'name' => 'foo',
            'options' => [
                'a' => 'A',
            ],
            'type' => 'checkbox',
            'value' => 1,
        ]))->toEqual('<label for="foo-1">'.
            '<input type="checkbox" id="foo-1" name="foo" value="a" /> A'.
        '</label>');
    expect(builder()->input([
            'id' => 'foo',
            'label' => 'bar',
            'name' => 'foo',
            'options' => [
                'a' => 'A',
                'b' => 'B',
            ],
            'type' => 'checkbox',
            'value' => 'a',
        ]))->toEqual('<label for="foo-1">'.
            '<input type="checkbox" id="foo-1" checked name="foo" value="a" /> A'.
        '</label>'.
        '<label for="foo-2">'.
            '<input type="checkbox" id="foo-2" name="foo" value="b" /> B'.
        '</label>');
    expect(builder()->input([
            'id' => 'foo',
            'label' => 'bar',
            'name' => 'foo',
            'options' => [
                'a' => 'A',
                'b' => 'B',
            ],
            'type' => 'checkbox',
            'value' => ['a', 'b'],
        ]))->toEqual('<label for="foo-1">'.
            '<input type="checkbox" id="foo-1" checked name="foo" value="a" /> A'.
        '</label>'.
        '<label for="foo-2">'.
            '<input type="checkbox" id="foo-2" checked name="foo" value="b" /> B'.
        '</label>');
});

test('build input with invalid type', function () {
    expect(builder()->input([
            'id' => 'foo',
            'label' => 'bar',
            'name' => 'foo',
            'type' => 'rating', // invalid input type
            'value' => 1,
        ]))->toEqual('<label for="foo">bar</label>'.
        '<input type="text" id="foo" name="foo" value="1" />');
});

test('build select', function () {
    expect(builder()->select([
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
        ]))->toEqual('<label for="foo">Select one</label>'.
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
        '</select>');
});

test('build textarea', function () {
    expect(builder()->textarea([
            'id' => 'foo',
            'label' => 'bar',
            'name' => 'foo',
            'value' => 'foobar',
        ]))->toEqual('<label for="foo">bar</label>'.
        '<textarea id="foo" name="foo">foobar</textarea>');
});

function builder(): BuilderContract
{
    return new Builder();
}
