<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Helper;
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

    public function test_if_empty()
    {
        $this->assertEquals(Helper::ifEmpty(0, 'abc'), 0);
        $this->assertEquals(Helper::ifEmpty(0, 'abc', true), 'abc');
        $this->assertEquals(Helper::ifEmpty([], 'abc'), 'abc');
        $this->assertEquals(Helper::ifEmpty([], 'abc', true), 'abc');
        $this->assertEquals(Helper::ifEmpty(false, 'abc'), false);
        $this->assertEquals(Helper::ifEmpty(false, 'abc', true), 'abc');
        $this->assertEquals(Helper::ifEmpty(null, 'abc'), 'abc');
        $this->assertEquals(Helper::ifEmpty(null, 'abc', true), 'abc');
        $this->assertEquals(Helper::ifEmpty('', 'abc'), 'abc');
        $this->assertEquals(Helper::ifEmpty('', 'abc', true), 'abc');
    }

    public function test_is_greater_then()
    {
        $this->assertTrue(Helper::isGreaterThan('1.0.0', '1.0'));
        $this->assertFalse(Helper::isGreaterThan('1.0.0', '1.0.0'));
        $this->assertFalse(Helper::isGreaterThan('1.0.0', '1.0.1'));
    }

    public function test_is_greater_then_or_equal()
    {
        $this->assertTrue(Helper::isGreaterThanOrEqual('1.0.0', '1.0'));
        $this->assertTrue(Helper::isGreaterThanOrEqual('1.0.0', '1.0.0'));
        $this->assertFalse(Helper::isGreaterThanOrEqual('1.0.0', '1.0.1'));
    }

    public function test_is_less_then()
    {
        $this->assertTrue(Helper::isLessThan('1.0', '1.0.0'));
        $this->assertFalse(Helper::isLessThan('1.0.0', '1.0.0'));
        $this->assertFalse(Helper::isLessThan('1.0.1', '1.0.0'));
    }

    public function test_is_less_then_or_equal()
    {
        $this->assertTrue(Helper::isLessThanOrEqual('1.0', '1.0.0'));
        $this->assertTrue(Helper::isLessThanOrEqual('1.0.0', '1.0.0'));
        $this->assertFalse(Helper::isLessThanOrEqual('1.0.1', '1.0.0'));
    }
}
