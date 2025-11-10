<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Helper;
use WP_UnitTestCase;

/**
 * Test case for the Plugin.
 * @group plugin
 */
class HelperTest extends WP_UnitTestCase
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
        $this->assertEquals(Helper::buildMethodName('get', 'Hello-Doll'), 'getHelloDoll');
    }

    public function test_compare_versions()
    {
        $this->assertTrue(Helper::compareVersions('1.0', '1'));
        $this->assertTrue(Helper::compareVersions('1.0', '1.00'));
        $this->assertFalse(Helper::compareVersions('1.0', '1.0.10'));
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

    public function test_get_ip_address()
    {
        $this->assertEquals(Helper::getIpAddress(), '127.0.0.1');
    }

    public function test_get_page_number()
    {
        $queryvar = glsr()->constant('PAGED_QUERY_VAR');
        $this->assertEquals(Helper::getPageNumber("https://test.com?{$queryvar}=2"), '2');
        $this->assertEquals(Helper::getPageNumber(), '1');
    }

    public function test_get_user_id()
    {
        $userId = self::factory()->user->create([
            'user_login' => 'test_user',
        ]);
        wp_set_current_user($userId);
        $user = wp_get_current_user();
        $this->assertEquals(Helper::getUserId($user), $userId);
        $this->assertEquals(Helper::getUserId($userId), $userId);
        $this->assertEquals(Helper::getUserId('user_id'), $userId);
        $this->assertEquals(Helper::getUserId('test_user'), $userId);
        $this->assertEquals(Helper::getUserId('xxx'), 0);
        $fn = fn () => 13;
        add_filter('site-reviews/assigned_users/author_id', $fn);
        add_filter('site-reviews/assigned_users/profile_id', $fn);
        add_filter('site-reviews/assigned_users/user_id', $fn);
        $this->assertEquals(Helper::getUserId('author_id'), 13);
        $this->assertEquals(Helper::getUserId('profile_id'), 13);
        $this->assertEquals(Helper::getUserId('user_id'), 13);
        remove_filter('site-reviews/assigned_users/author_id', $fn);
        remove_filter('site-reviews/assigned_users/profile_id', $fn);
        remove_filter('site-reviews/assigned_users/user_id', $fn);
    }

    public function test_if_empty()
    {
        $this->assertEquals(Helper::ifEmpty(0, 'abc'), 0);
        $this->assertEquals(Helper::ifEmpty(0, 'abc', $strict = true), 'abc');
        $this->assertEquals(Helper::ifEmpty([], 'abc'), 'abc');
        $this->assertEquals(Helper::ifEmpty([], 'abc', $strict = true), 'abc');
        $this->assertEquals(Helper::ifEmpty(false, 'abc'), $strict = false);
        $this->assertEquals(Helper::ifEmpty(false, 'abc', $strict = true), 'abc');
        $this->assertEquals(Helper::ifEmpty(null, 'abc'), 'abc');
        $this->assertEquals(Helper::ifEmpty(null, 'abc', $strict = true), 'abc');
        $this->assertEquals(Helper::ifEmpty('', 'abc'), 'abc');
        $this->assertEquals(Helper::ifEmpty('', 'abc', $strict = true), 'abc');
    }

    public function test_is_greater_then()
    {
        $this->assertFalse(Helper::isGreaterThan('1.0', '1'));
        $this->assertFalse(Helper::isGreaterThan('1.0.0', '1.0'));
        $this->assertFalse(Helper::isGreaterThan('1.0.0', '1.0.0'));
        $this->assertFalse(Helper::isGreaterThan('1.0.0', '1.0.1'));
    }

    public function test_is_greater_then_or_equal()
    {
        $this->assertTrue(Helper::isGreaterThanOrEqual('1.0', '1'));
        $this->assertTrue(Helper::isGreaterThanOrEqual('1.0.0', '1.0'));
        $this->assertTrue(Helper::isGreaterThanOrEqual('1.0.0', '1.0.0'));
        $this->assertFalse(Helper::isGreaterThanOrEqual('1.0.0', '1.0.1'));
    }

    public function test_is_less_then()
    {
        $this->assertFalse(Helper::isLessThan('1', '1.0'));
        $this->assertFalse(Helper::isLessThan('1.0', '1.0.0'));
        $this->assertFalse(Helper::isLessThan('1.0.0', '1.0.0'));
        $this->assertFalse(Helper::isLessThan('1.0.1', '1.0.0'));
    }

    public function test_is_less_then_or_equal()
    {
        $this->assertTrue(Helper::isLessThanOrEqual('1', '1.0'));
        $this->assertTrue(Helper::isLessThanOrEqual('1.0', '1.0.0'));
        $this->assertTrue(Helper::isLessThanOrEqual('1.0.0', '1.0.0'));
        $this->assertFalse(Helper::isLessThanOrEqual('1.0.1', '1.0.0'));
    }
}
