<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Helpers\Arr;
use WP_UnitTestCase;

/**
 * Test case for the Plugin.
 * @group plugin
 */
class TestArr extends WP_UnitTestCase
{
    public function test_compare()
    {
        $this->assertTrue(Arr::compare(['one' => ['two']], ['one' => ['two']]));
        $this->assertFalse(Arr::compare(['one' => ['two']], ['one' => 'two']));
    }

    public function test_consolidate()
    {
    }

    public function test_convert_from_dot_notation()
    {
        $original = ['parent.child' => 'toys'];
        $converted = ['parent' => ['child' => 'toys']];
        $this->assertEquals(Arr::convertFromDotNotation($original), $converted);
    }

    public function test_convert_from_string()
    {
    }

    public function test_flatten()
    {
        $test = ['one' => ['two' => ['three' => ['x', 'y', 'z']]]];
        $this->assertEquals(Arr::flatten($test),
            ['one.two.three' => ['x', 'y', 'z']]
        );
        $this->assertEquals(Arr::flatten($test, true),
            ['one.two.three' => '[x, y, z]']
        );
        $this->assertEquals(Arr::flatten($test, true, 'test'),
            ['test.one.two.three' => '[x, y, z]']
        );
    }

    public function test_get()
    {
        $values1 = ['parent' => ['child' => 'toys']];
        $values2 = ['parent' => ['child' => (object) ['toys' => 123]]];
        $this->assertEquals(Arr::get($values1, 'parent.child'), 'toys');
        $this->assertEquals(Arr::get($values1, 'parent.child.toys', 'fallback'), 'fallback');
        $this->assertTrue(is_object(Arr::get($values2, 'parent.child')));
    }

    public function test_insert_after()
    {
    }

    public function test_insert_before()
    {
    }

    public function test_insert()
    {
    }

    public function test_is_indexed_and_flat()
    {
        $this->assertFalse(Arr::isIndexedAndFlat('not an array'));
        $this->assertFalse(Arr::isIndexedAndFlat([[]]));
        $this->assertTrue(Arr::isIndexedAndFlat([]));
        $this->assertTrue(Arr::isIndexedAndFlat([1, 2, 3]));
    }

    public function test_prefix_keys()
    {
        $array = ['_a' => '', 'b' => ''];
        $this->assertEquals(Arr::prefixKeys($array), ['_a' => '', '_b' => '']);
    }

    public function test_prepend()
    {
    }

    public function test_reindex()
    {
    }

    public function test_remove_empty_values()
    {
        $array = [
            'emptyString' => '',
            'emptyArray' => [],
            'array' => [
                'string' => 'string',
                'emptyString' => [],
            ],
        ];
        $this->assertEquals(Arr::removeEmptyValues($array),
            ['array' => ['string' => 'string']]
        );
    }

    public function test_set()
    {
        $this->assertEquals(Arr::set([], 'number.thirteen', '13'),
            ['number' => ['thirteen' => '13']]
        );
    }

    public function test_unique()
    {
    }

    public function test_unique_int()
    {
    }

    public function test_unprefix_keys()
    {
        $array = ['_a' => '', 'b' => ''];
        $this->assertEquals(Arr::unprefixKeys($array), ['a' => '', 'b' => '']);
    }
}
