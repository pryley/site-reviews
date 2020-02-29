<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use WP_UnitTestCase;

/**
 * Test case for the Plugin.
 * @group plugin
 */
class TestHelper extends WP_UnitTestCase
{
    public function test_build_class_name()
    {
        $this->assertEquals(Helper::buildClassName('hello-doll'), 'HelloDoll');
        $this->assertEquals(
            Helper::buildClassName('Doll', 'Hello'),
            'GeminiLabs\SiteReviews\Hello\Doll'
        );
    }

    public function test_build_method_name()
    {
        $this->assertEquals(Helper::buildMethodName('Hello-Doll', 'get'), 'getHelloDoll');
    }

    public function test_build_property_name()
    {
        $this->assertEquals(Helper::buildPropertyName('Hello-Doll'), 'helloDoll');
    }

    public function test_compare_arrays()
    {
        $this->assertTrue(Arr::compare(['one' => ['two']], ['one' => ['two']]));
        $this->assertFalse(Arr::compare(['one' => ['two']], ['one' => 'two']));
    }

    public function test_convert_dot_notation_array()
    {
        $original = ['parent.child' => 'toys'];
        $converted = ['parent' => ['child' => 'toys']];
        $this->assertEquals(Arr::convertFromDotNotation($original), $converted);
    }

    public function test_convert_path_to_id()
    {
        $this->assertEquals(Str::convertPathToId('abc.d.e'), '-abc-d-e');
        $this->assertEquals(Str::convertPathToId('d.e', 'abc'), 'abc-d-e');
        $this->assertEquals(Str::convertPathToId('d.e.', 'abc'), 'abc-d-e-');
        $this->assertEquals(Str::convertPathToId('.d.e', 'abc'), 'abc--d-e');
    }

    public function test_convert_path_to_name()
    {
        $this->assertEquals(Str::convertPathToName('abc.d.e'), '[abc][d][e]');
        $this->assertEquals(Str::convertPathToName('d.e', 'abc'), 'abc[d][e]');
        $this->assertEquals(Str::convertPathToName('d.e.', 'abc'), 'abc[d][e][]');
        $this->assertEquals(Str::convertPathToName('.d.e', 'abc'), 'abc[][d][e]');
    }

    public function test_dash_case()
    {
        $this->assertEquals(Str::dashCase('a-b_cDE'), 'a-b-c-d-e');
    }

    public function test_ends_with()
    {
        $this->assertTrue(Str::endsWith('efg', 'abcdefg'));
        $this->assertFalse(Str::endsWith('efg', 'ABCDEFG'));
    }

    public function test_filter_input()
    {
        $_POST['xxx'] = 'xxx';
        $this->assertEquals(Helper::filterInput('xxx'), 'xxx');
        $this->assertEquals(Helper::filterInput('zzz'), null);
    }

    public function test_filter_input_array()
    {
        $test = ['a' => ['b', 'c']];
        $_POST['xxx'] = $test;
        $this->assertEquals(Helper::filterInputArray('xxx'), $test);
        $this->assertEquals(Helper::filterInputArray('zzz'), []);
    }

    public function test_flatten_array()
    {
        $test = ['one' => ['two' => ['three' => ['x', 'y', 'z']]]];
        $this->assertEquals(
            Arr::flatten($test),
            ['one.two.three' => ['x', 'y', 'z']]
        );
        $this->assertEquals(
            Arr::flatten($test, true),
            ['one.two.three' => '[x, y, z]']
        );
        $this->assertEquals(
            Arr::flatten($test, true, 'test'),
            ['test.one.two.three' => '[x, y, z]']
        );
    }

    public function test_get_path_value()
    {
        $values = ['parent' => ['child' => 'toys']];
        $this->assertEquals(
            Arr::get($values, 'parent.child'),
            'toys'
        );
        $this->assertEquals(
            Arr::get($values, 'parent.child.toys', 'fallback'),
            'fallback'
        );
    }

    public function test_is_indexed_flat_array()
    {
        $this->assertFalse(Arr::isIndexedAndFlat('not an array'));
        $this->assertFalse(Arr::isIndexedAndFlat([[]]));
        $this->assertTrue(Arr::isIndexedAndFlat([]));
        $this->assertTrue(Arr::isIndexedAndFlat([1, 2, 3]));
    }

    public function test_prefix_string()
    {
        $this->assertEquals(Str::prefix('hello_', ' bob '), 'hello_bob');
    }

    public function test_remove_empty_array_values()
    {
        $array = [
            'emptyString' => '',
            'emptyArray' => [],
            'array' => [
                'string' => 'string',
                'emptyString' => [],
            ],
        ];
        $this->assertEquals(
            Arr::removeEmptyValues($array),
            ['array' => ['string' => 'string']]
        );
    }

    public function test_set_path_value()
    {
        $this->assertEquals(
            Arr::set([], 'number.thirteen', '13'),
            ['number' => ['thirteen' => '13']]
        );
    }

    public function test_snake_case()
    {
        $this->assertEquals(Str::snakeCase('a-b_cDE'), 'a_b_c_d_e');
    }

    public function test_starts_with()
    {
        $this->assertTrue(Str::startsWith('abc', 'abcdefg'));
        $this->assertFalse(Str::startsWith('abc', 'ABCDEFG'));
    }
}
