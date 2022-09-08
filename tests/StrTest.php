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
        $this->assertTrue(Str::contains('abcdef', 'cd'));
        $this->assertTrue(Str::contains('abcdef', 'abcdef'));
        $this->assertFalse(Str::contains('abcdef', ''));
        $this->assertFalse(Str::contains('abcdef', 'z'));
    }

    public function test_convert_name()
    {
        $this->assertEquals(Str::convertName('Steve'), 'Steve');
        $this->assertEquals(Str::convertName('Steve', 'initials'), 'S');
        $this->assertEquals(Str::convertName('Steve', 'initials', 'space'), 'S');
        $this->assertEquals(Str::convertName('Steve', 'initials', 'period'), 'S.');
        $this->assertEquals(Str::convertName('Steve', 'initials', 'period_space'), 'S.');
        $this->assertEquals(Str::convertName('Steve', 'first'), 'Steve');
        $this->assertEquals(Str::convertName('Steve', 'first', 'space'), 'Steve');
        $this->assertEquals(Str::convertName('Steve', 'first', 'period'), 'Steve');
        $this->assertEquals(Str::convertName('Steve', 'first', 'period_space'), 'Steve');
        $this->assertEquals(Str::convertName('Steve', 'first_initial'), 'S');
        $this->assertEquals(Str::convertName('Steve', 'first_initial', 'space'), 'S');
        $this->assertEquals(Str::convertName('Steve', 'first_initial', 'period'), 'S.');
        $this->assertEquals(Str::convertName('Steve', 'first_initial', 'period_space'), 'S.');
        $this->assertEquals(Str::convertName('Steve', 'last'), 'Steve');
        $this->assertEquals(Str::convertName('Steve', 'last', 'space'), 'Steve');
        $this->assertEquals(Str::convertName('Steve', 'last', 'period'), 'Steve');
        $this->assertEquals(Str::convertName('Steve', 'last', 'period_space'), 'Steve');
        $this->assertEquals(Str::convertName('Steve', 'last_initial'), 'Steve');
        $this->assertEquals(Str::convertName('Steve', 'last_initial', 'space'), 'Steve');
        $this->assertEquals(Str::convertName('Steve', 'last_initial', 'period'), 'Steve');
        $this->assertEquals(Str::convertName('Steve', 'last_initial', 'period_space'), 'Steve');
        $this->assertEquals(Str::convertName('Steve Jobs'), 'Steve Jobs');
        $this->assertEquals(Str::convertName('Steve Jobs', 'initials'), 'S J');
        $this->assertEquals(Str::convertName('Steve Jobs', 'initials', 'space'), 'S J');
        $this->assertEquals(Str::convertName('Steve Jobs', 'initials', 'period'), 'S.J.');
        $this->assertEquals(Str::convertName('Steve Jobs', 'initials', 'period_space'), 'S. J.');
        $this->assertEquals(Str::convertName('Steve Jobs', 'first'), 'Steve');
        $this->assertEquals(Str::convertName('Steve Jobs', 'first', 'space'), 'Steve');
        $this->assertEquals(Str::convertName('Steve Jobs', 'first', 'period'), 'Steve');
        $this->assertEquals(Str::convertName('Steve Jobs', 'first', 'period_space'), 'Steve');
        $this->assertEquals(Str::convertName('Steve Jobs', 'first_initial'), 'S Jobs');
        $this->assertEquals(Str::convertName('Steve Jobs', 'first_initial', 'space'), 'S Jobs');
        $this->assertEquals(Str::convertName('Steve Jobs', 'first_initial', 'period'), 'S.Jobs');
        $this->assertEquals(Str::convertName('Steve Jobs', 'first_initial', 'period_space'), 'S. Jobs');
        $this->assertEquals(Str::convertName('Steve Jobs', 'last'), 'Jobs');
        $this->assertEquals(Str::convertName('Steve Jobs', 'last', 'space'), 'Jobs');
        $this->assertEquals(Str::convertName('Steve Jobs', 'last', 'period'), 'Jobs');
        $this->assertEquals(Str::convertName('Steve Jobs', 'last', 'period_space'), 'Jobs');
        $this->assertEquals(Str::convertName('Steve Jobs', 'last_initial'), 'Steve J');
        $this->assertEquals(Str::convertName('Steve Jobs', 'last_initial', 'space'), 'Steve J');
        $this->assertEquals(Str::convertName('Steve Jobs', 'last_initial', 'period'), 'Steve J.');
        $this->assertEquals(Str::convertName('Steve Jobs', 'last_initial', 'period_space'), 'Steve J.');
        $this->assertEquals(Str::convertName('Steve Paul Jobs'), 'Steve Paul Jobs');
        $this->assertEquals(Str::convertName('Steve Paul Jobs', 'initials'), 'S P J');
        $this->assertEquals(Str::convertName('Steve Paul Jobs', 'initials', 'space'), 'S P J');
        $this->assertEquals(Str::convertName('Steve Paul Jobs', 'initials', 'period'), 'S.P.J.');
        $this->assertEquals(Str::convertName('Steve Paul Jobs', 'initials', 'period_space'), 'S. P. J.');
        $this->assertEquals(Str::convertName('Steve Paul Jobs', 'first'), 'Steve');
        $this->assertEquals(Str::convertName('Steve Paul Jobs', 'first', 'space'), 'Steve');
        $this->assertEquals(Str::convertName('Steve Paul Jobs', 'first', 'period'), 'Steve');
        $this->assertEquals(Str::convertName('Steve Paul Jobs', 'first', 'period_space'), 'Steve');
        $this->assertEquals(Str::convertName('Steve Paul Jobs', 'first_initial'), 'S Jobs');
        $this->assertEquals(Str::convertName('Steve Paul Jobs', 'first_initial', 'space'), 'S Jobs');
        $this->assertEquals(Str::convertName('Steve Paul Jobs', 'first_initial', 'period'), 'S.Jobs');
        $this->assertEquals(Str::convertName('Steve Paul Jobs', 'first_initial', 'period_space'), 'S. Jobs');
        $this->assertEquals(Str::convertName('Steve Paul Jobs', 'last'), 'Jobs');
        $this->assertEquals(Str::convertName('Steve Paul Jobs', 'last', 'space'), 'Jobs');
        $this->assertEquals(Str::convertName('Steve Paul Jobs', 'last', 'period'), 'Jobs');
        $this->assertEquals(Str::convertName('Steve Paul Jobs', 'last', 'period_space'), 'Jobs');
        $this->assertEquals(Str::convertName('Steve Paul Jobs', 'last_initial'), 'Steve J');
        $this->assertEquals(Str::convertName('Steve Paul Jobs', 'last_initial', 'space'), 'Steve J');
        $this->assertEquals(Str::convertName('Steve Paul Jobs', 'last_initial', 'period'), 'Steve J.');
        $this->assertEquals(Str::convertName('Steve Paul Jobs', 'last_initial', 'period_space'), 'Steve J.');
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

    public function test_convert_to_initials()
    {
        $this->assertEquals(Str::convertToInitials('Steve', ' '), 'S');
        $this->assertEquals(Str::convertToInitials('Steve', '.'), 'S.');
        $this->assertEquals(Str::convertToInitials('Steve', '. '), 'S.');
        $this->assertEquals(Str::convertToInitials('Steve Jobs', ' '), 'S J');
        $this->assertEquals(Str::convertToInitials('Steve Jobs', '.'), 'S.J.');
        $this->assertEquals(Str::convertToInitials('Steve Jobs', '. '), 'S. J.');
        $this->assertEquals(Str::convertToInitials('Steve Paul Jobs', ' '), 'S P J');
        $this->assertEquals(Str::convertToInitials('Steve Paul Jobs', '.'), 'S.P.J.');
        $this->assertEquals(Str::convertToInitials('Steve Paul Jobs', '. '), 'S. P. J.');
    }

    public function test_dash_case()
    {
        $this->assertEquals(Str::dashCase('a-b_cDE'), 'a-b-c-d-e');
    }

    public function test_ends_with()
    {
        $this->assertTrue(Str::endsWith('abcdefg', 'efg'));
        $this->assertFalse(Str::endsWith('ABCDEFG', 'efg'));
        $this->assertFalse(Str::endsWith('ABCDEFG', ''));
    }

    public function test_fallback()
    {
        $this->assertEquals(Str::fallback('1', '2'), '1');
        $this->assertEquals(Str::fallback('', '2'), '2');
        $this->assertEquals(Str::fallback(1, '2'), '1');
        $this->assertEquals(Str::fallback([], '2'), '2');
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
        $this->assertTrue(Str::startsWith('abcdefg', 'abc'));
        $this->assertTrue(Str::startsWith('defg', 'abc,def'));
        $this->assertFalse(Str::startsWith('ABCDEFG', 'abc'));
        $this->assertFalse(Str::startsWith('ABCDEFG', ''));
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
