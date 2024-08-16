<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Helpers\Arr;
use WP_UnitTestCase;

/**
 * Test case for the Plugin.
 * @group plugin
 */
class ArrTest extends WP_UnitTestCase
{
    public function test_compare()
    {
        $this->assertTrue(Arr::compare(['one' => ['two']], ['one' => ['two']]));
        $this->assertFalse(Arr::compare(['one' => ['two']], ['one' => 'two']));
    }

    public function test_consolidate()
    {
        $this->assertEquals(Arr::consolidate(''), []);
        $this->assertEquals(Arr::consolidate('0'), []);
        $this->assertEquals(Arr::consolidate('1'), []);
        $this->assertEquals(Arr::consolidate('1,2'), []);
        $this->assertEquals(Arr::consolidate((object)[]), []);
        $this->assertEquals(Arr::consolidate(0), []);
        $this->assertEquals(Arr::consolidate(1), []);
        $this->assertEquals(Arr::consolidate([]), []);
        $this->assertEquals(Arr::consolidate(false), []);
        $this->assertEquals(Arr::consolidate(true), []);
        $this->assertEquals(Arr::consolidate((object)[1]), [1]);
    }

    public function test_convert_from_string()
    {
        $this->assertEquals(Arr::convertFromString(',a,,b,c,1,,'), ['a','b','c','1']);
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

    public function test_get_as()
    {
        $values1 = ['parent' => ['child' => 'toys', 'number' => '2.3', 'version' => '2.0.0']];
        $this->assertEquals(Arr::getAs('array', $values1, 'parent.child'), ['toys']);
        $this->assertEquals(Arr::getAs('bool', $values1, 'parent.child'), false);
        $this->assertEquals(Arr::getAs('float', $values1, 'parent.child'), 0);
        $this->assertEquals(Arr::getAs('float', $values1, 'parent.number'), 2.3);
        $this->assertEquals(Arr::getAs('float', $values1, 'parent.version'), 0);
        $this->assertEquals(Arr::getAs('int', $values1, 'parent.child'), 0);
        $this->assertEquals(Arr::getAs('int', $values1, 'parent.number'), 2);
        $this->assertEquals(Arr::getAs('int', $values1, 'parent.version'), 0);
        $this->assertEquals(Arr::getAs('object', $values1, 'parent.child'), (object) ['toys']);
        $this->assertEquals(Arr::getAs('string', $values1, 'parent.child'), 'toys');
    }

    public function test_insert_after()
    {
        $array1 = ['1','2','3'];
        $array2 = ['a' => 1, 'b' => 2, 'c' => 3];
        $this->assertEquals(Arr::insertAfter(1, $array1, ['a','b']), ['1','2','a','b','3']);
        $this->assertEquals(Arr::insertAfter(9, $array1, ['a','b']), ['1','2','3','a','b']);
        $this->assertEquals(Arr::insertAfter('b', $array2, ['z' => 13]), ['a' => 1, 'b' => 2, 'z' => 13, 'c' => 3]);
        $this->assertEquals(Arr::insertAfter('z', $array2, ['z' => 13]), ['a' => 1, 'b' => 2, 'c' => 3, 'z' => 13]);
    }

    public function test_insert_before()
    {
        $array1 = ['1','2','3'];
        $array2 = ['a' => 1, 'b' => 2, 'c' => 3];
        $this->assertEquals(Arr::insertBefore(1, $array1, ['a','b']), ['1','a','b','2','3']);
        $this->assertEquals(Arr::insertBefore(9, $array1, ['a','b']), ['1','2','3','a','b']);
        $this->assertEquals(Arr::insertBefore('b', $array2, ['z' => 13]), ['a' => 1, 'z' => 13, 'b' => 2, 'c' => 3]);
        $this->assertEquals(Arr::insertBefore('z', $array2, ['z' => 13]), ['a' => 1, 'b' => 2, 'c' => 3, 'z' => 13]);
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
        $array1 = ['1','2','3'];
        $array2 = ['a' => 1, 'b' => 2, 'c' => 3];
        $this->assertEquals(Arr::prepend($array1, 13), ['13','1','2','3']);
        $this->assertEquals(Arr::prepend($array2, 13, 'z'), ['z' => 13, 'a' => 1, 'b' => 2, 'c' => 3]);
    }

    public function test_reindex()
    {
        $array = ['1','2','3','4','5'];
        unset($array[3]);
        $this->assertEquals(Arr::reindex($array), ['1','2','3','5']);
    }

    public function test_remove()
    {
        $array = [
            'indexed',
            'emptyString' => '',
            'array' => ['string' => 'string'],
        ];
        $this->assertEquals(Arr::remove($array), $array);
        $this->assertEquals(Arr::remove($array, ''), $array);
        $this->assertEquals(Arr::remove($array, '0'), 
            ['emptyString' => '', 'array' => ['string' => 'string']]
        );
        $this->assertEquals(Arr::remove($array, 0), 
            ['emptyString' => '', 'array' => ['string' => 'string']]
        );
        $this->assertEquals(Arr::remove($array, 'array'),
            ['indexed', 'emptyString' => '']
        );
        $this->assertEquals(Arr::remove($array, 'array.string'),
            ['indexed', 'emptyString' => '', 'array' => []]
        );
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
        $this->assertEquals(Arr::set([], '', '13'),
            []
        );
    }

    public function test_unflatten()
    {
        $original = ['parent.child' => 'toys'];
        $converted = ['parent' => ['child' => 'toys']];
        $this->assertEquals(Arr::unflatten($original), $converted);
    }

    public function test_unique()
    {
        $array = ['1','3','2','3','4','3','5'];
        $array = Arr::unique($array);
        sort($array);
        $this->assertEquals($array, ['1','2','3','4','5']);
    }

    public function test_unique_int()
    {
        $array = ['1','3','2','a','4','3','5','0', '-1'];
        $test1 = Arr::uniqueInt($array);
        sort($test1);
        $this->assertEquals($test1, [1,2,3,4,5]);
        $test2 = Arr::uniqueInt($array, false);
        sort($test2);
        $this->assertEquals($test2, [-1,0,1,2,3,4,5]);
    }

    public function test_unprefix_keys()
    {
        $array = ['_a' => '', 'b' => ''];
        $this->assertEquals(Arr::unprefixKeys($array), ['a' => '', 'b' => '']);
    }
}
