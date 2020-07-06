<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Helpers\Url;
use WP_UnitTestCase;

/**
 * Test case for the Plugin.
 * @group plugin
 */
class TestUrl extends WP_UnitTestCase
{
    public function test_home()
    {
        $url = home_url();
        $this->assertEquals(Url::home(), $url.'/');
        $this->assertEquals(Url::home('test'), $url.'/test/');
    }

    public function test_path()
    {
        $url = 'https://test.com';
        $this->assertEquals(Url::path($url), '');
        $this->assertEquals(Url::path($url.'/'), '');
        $this->assertEquals(Url::path($url.'/test'), '/test');
        $this->assertEquals(Url::path($url.'/test/'), '/test');
        $this->assertEquals(Url::path($url.'/test/dir'), '/test/dir');
        $this->assertEquals(Url::path($url.'/test/dir/'), '/test/dir');
    }

    public function test_query()
    {
        $url = 'https://test.com?abc=xyz';
        $this->assertEquals(Url::query($url, 'abc'), 'xyz');
        $this->assertEquals(Url::query($url, 'ab', '123'), '123');
        $this->assertNull(Url::query($url, 'ab'));
    }
}
