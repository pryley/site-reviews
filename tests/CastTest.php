<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Helpers\Cast;
use WP_UnitTestCase;

/**
 * Test case for the Plugin.
 * @group plugin
 */
class CastTest extends WP_UnitTestCase
{
    public function test_to()
    {
        $this->assertTrue(is_array(Cast::to('array', '')));
        $this->assertTrue(is_bool(Cast::to('bool', '')));
        $this->assertTrue(is_float(Cast::to('float', '')));
        $this->assertTrue(is_int(Cast::to('int', '12.3')));
        $this->assertTrue(is_object(Cast::to('object', '')));
        $this->assertTrue(is_string(Cast::to('string', [])));
        $this->assertEquals(Cast::to('xyz', 'abc'), 'abc');
    }

    public function test_to_array()
    {
        $this->assertEquals(Cast::toArray(''), []);
        $this->assertEquals(Cast::toArray('abc'), ['abc']);
        $this->assertEquals(Cast::toArray('a,b,c'), ['a', 'b', 'c']);
        $this->assertEquals(Cast::toArray('a,b,c', false), ['a,b,c']);
        $this->assertEquals(Cast::toArray(1), [1]);
        $this->assertEquals(Cast::toArray([1]), [1]);
        $this->assertEquals(Cast::toArray((object) ['a' => 123]), ['a' => 123]);
    }

    public function test_to_bool()
    {
        $this->assertFalse(Cast::toBool(''));
        $this->assertFalse(Cast::toBool(0));
        $this->assertFalse(Cast::toBool('0'));
        $this->assertFalse(Cast::toBool([]));
        $this->assertFalse(Cast::toBool([1]));
        $this->assertTrue(Cast::toBool(1));
        $this->assertTrue(Cast::toBool('1'));
        $this->assertTrue(Cast::toBool('true'));
    }

    public function test_to_float()
    {
        $this->assertEquals(Cast::toFloat(''), 0);
        $this->assertEquals(Cast::toFloat([]), 0);
        $this->assertEquals(Cast::toFloat('abc'), 0);
        $this->assertEquals(Cast::toFloat('123.123'), 123.123);
        $this->assertEquals(Cast::toFloat(123), 123);
        $this->assertEquals(Cast::toFloat(123.123), 123.123);
    }

    public function test_to_int()
    {
        $this->assertEquals(Cast::toInt(''), 0);
        $this->assertEquals(Cast::toInt([]), 0);
        $this->assertEquals(Cast::toInt('abc'), 0);
        $this->assertEquals(Cast::toInt('123.123'), 123);
        $this->assertEquals(Cast::toInt('123'), 123);
        $this->assertEquals(Cast::toInt(123.123), 123);
    }

    public function test_to_object()
    {
        $this->assertEquals(Cast::toObject(''), (object) []);
        $this->assertEquals(Cast::toObject((object) []), (object) []);
    }

    public function test_to_string()
    {
        $this->assertEquals(Cast::toString([]), '');
        $this->assertEquals(Cast::toString(123), '123');
        $this->assertEquals(Cast::toString([123]), '');
        $this->assertEquals(Cast::toString([123], false), 'a:1:{i:0;i:123;}');
        $this->assertEquals(Cast::toString(new MockClass()), '123');
    }
}

