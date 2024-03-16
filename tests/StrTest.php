<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Helpers\Str;
use WP_UnitTestCase;

/**
 * Test case for the Plugin.
 * @group plugin
 */
class StrTest extends WP_UnitTestCase
{
    public function test_camel_case()
    {
        $this->assertEquals(Str::camelCase('a-b_cDE'), 'ABCDE');
        $this->assertEquals(Str::camelCase('aaa-bbb_cde'), 'AaaBbbCde');
    }

    public function test_contains()
    {
        $this->assertTrue(Str::contains('abcdef', ['cd']));
        $this->assertTrue(Str::contains('abcdef', ['abcdef']));
        $this->assertFalse(Str::contains('abcdef', ['']));
        $this->assertFalse(Str::contains('abcdef', ['z']));
        $this->assertFalse(Str::contains('abcdef', []));
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
        $this->assertTrue(Str::endsWith('abcdefg', ['efg']));
        $this->assertFalse(Str::endsWith('ABCDEFG', ['efg']));
        $this->assertFalse(Str::endsWith('ABCDEFG', ['']));
        $this->assertFalse(Str::endsWith('ABCDEFG', []));
    }

    public function test_fallback()
    {
        $this->assertEquals(Str::fallback('1', '2'), '1');
        $this->assertEquals(Str::fallback('', '2'), '2');
        $this->assertEquals(Str::fallback(1, '2'), '1');
        $this->assertEquals(Str::fallback([], '2'), '2');
    }

    public function test_hash()
    {
        require_once ABSPATH.WPINC.'/pluggable.php';
        $hash = wp_hash('123', 'nonce');
        $this->assertEquals(Str::hash('123'), $hash);
        $this->assertEquals(Str::hash('123', 0), substr($hash, 0, 8));
        $this->assertEquals(Str::hash('123', 8), substr($hash, 0, 8));
        $this->assertEquals(Str::hash('123', 12), substr($hash, 0, 12));
    }

    public function test_join()
    {
        $this->assertEquals(Str::join(['1']), '1');
        $this->assertEquals(Str::join(['1'], true), "'1'");
        $this->assertEquals(Str::join(['1', '2']), '1, 2');
        $this->assertEquals(Str::join(['1', '2'], true), "'1','2'");
    }

    public function test_mask()
    {
        $string = 'abcdefghijklmnopqrstuvwxyz';
        $this->assertEquals(Str::mask($string), '*************');
        $this->assertEquals(Str::mask($string, 4), 'abcd*********');
        $this->assertEquals(Str::mask($string, 0, 4), '*********wxyz');
        $this->assertEquals(Str::mask($string, 4, 4), 'abcd*****wxyz');
        $this->assertEquals(Str::mask($string, 4, 4, 20), 'abcd************wxyz');
        $this->assertEquals(Str::mask($string, 4, 4, 2), $string);
        $this->assertEquals(Str::mask($string, 20, 20), $string);
        $this->assertEquals(Str::mask($string, 40, 0), $string);
        $this->assertEquals(Str::mask($string, 0, 40), $string);
        $this->assertEquals(Str::mask($string, -10, 40), $string);
    }

    public function test_natural_join()
    {
        $this->assertEquals(Str::naturalJoin(['1']), '1');
        $this->assertEquals(Str::naturalJoin(['1', '2']), '1 and 2');
        $this->assertEquals(Str::naturalJoin(['1', '2', '3']), '1, 2 and 3');
        $this->assertEquals(Str::naturalJoin(['1', '2', '3', '4']), '1, 2, 3 and 4');
    }

    public function test_prefix()
    {
        $this->assertEquals(Str::prefix(' bob ', 'hello_'), 'hello_bob');
    }

    public function test_random()
    {
        $this->assertEquals(strlen(Str::random(4)), 4);
        $this->assertEquals(strlen(Str::random(8)), 8);
    }

    public function test_remove_prefix()
    {
        $this->assertEquals(Str::removePrefix('_abc', '_'), 'abc');
        $this->assertEquals(Str::removePrefix('_abc', ''), '_abc');
    }

    public function test_replace_first()
    {
        $this->assertEquals(Str::replaceFirst('', 'xyz', 'abcabc'), 'abcabc');
        $this->assertEquals(Str::replaceFirst('zzx', 'xyz', 'abcabc'), 'abcabc');
        $this->assertEquals(Str::replaceFirst('abc', 'xyz', 'abcabc'), 'xyzabc');
    }

    public function test_replace_last()
    {
        $this->assertEquals(Str::replaceLast('', 'xyz', 'abcabc'), 'abcabc');
        $this->assertEquals(Str::replaceLast('abc', 'xyz', 'abcabc'), 'abcxyz');
        $this->assertEquals(Str::replaceLast('zzz', 'xyz', 'abcabc'), 'abcabc');
    }

    public function test_restrict_to()
    {
        $this->assertEquals(Str::restrictTo('asc,desc,', 'ASC', 'DESC'), 'ASC');
        $this->assertEquals(Str::restrictTo('asc,desc,', 'ASC', 'DESC', true), 'DESC');
    }

    public function test_snake_case()
    {
        $this->assertEquals(Str::snakeCase('a-b_cDE'), 'a_b_c_d_e');
    }

    public function test_starts_with()
    {
        $this->assertTrue(Str::startsWith('abcdefg', ['abc']));
        $this->assertTrue(Str::startsWith('defg', ['abc', 'def']));
        $this->assertFalse(Str::startsWith('ABCDEFG', ['abc']));
        $this->assertFalse(Str::startsWith('ABCDEFG', ['']));
        $this->assertFalse(Str::startsWith('ABCDEFG', []));
    }

    public function test_suffix()
    {
        $this->assertEquals(Str::suffix('bob', '_goodbye'), 'bob_goodbye');
        $this->assertEquals(Str::suffix('bob_goodbye', '_goodbye'), 'bob_goodbye');
        $this->assertEquals(Str::suffix(' bob ', '_goodbye'), ' bob _goodbye');
        $this->assertEquals(Str::suffix(' bob ', ' _goodbye '), ' bob  _goodbye ');
    }

    public function test_truncate()
    {
        $this->assertEquals(Str::truncate('abc', 1), 'a');
        $this->assertEquals(Str::truncate('abc', 2), 'ab');
        $this->assertEquals(Str::truncate('abc', 3), 'abc');
        $this->assertEquals(Str::truncate('abc', 4), 'abc');
    }
}
