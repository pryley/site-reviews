<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Modules\Sanitizer;
use WP_UnitTestCase;

/**
 * Test case for the Plugin.
 * @group plugin
 */
class TestSanitizer extends WP_UnitTestCase
{
    protected $testValues;

    public function setUp()
    {
        parent::setUp();
        $this->testValues = [
            'a' => '',
            'b' => 'abc',
            'c' => ['1'],
            'd' => ['a' => false],
            'e' => [13],
            'f' => [0],
            'g' => ['1' => 13],
            'h' => (object)['b' => true],
            'i' => true,
            'j' => false,
            'k' => '<script>var x = 23;</script>',
            'l' => '<h3>This is a title!</h3>',
            'm' => "Hello\nthere!",
            'n' => 'June 13, 1989',
            'o' => '03-12-2020',
            'p' => '0-0-2020',
            'q' => '2020',
            'r' => 'xx xx',
            's' => '#ax+dex(tomorrow) $200 200% @peter',
            't' => 'this is true',
            'u' => 'this is false',
            'v' => 'matt@wordpress.org',
            'w' => 'https//wordpress.org',
            'x' => 'wordpress.org',
            'y' => 'www.wordpress.org',
            'z' => 'https://wordpress.org',
        ];
    }

    public function test_sanitize_array()
    {
        $sanitizers = array_fill_keys(array_keys($this->testValues), 'array');
        $sanitized = $this->sanitize($this->testValues, $sanitizers);
        $this->assertEquals($sanitized, [
            'a' => [],
            'b' => [],
            'c' => ['1'],
            'd' => ['a' => false],
            'e' => [13],
            'f' => [0],
            'g' => ['1' => 13],
            'h' => ['b' => true],
            'i' => [],
            'j' => [],
            'k' => [],
            'l' => [],
            'm' => [],
            'n' => [],
            'o' => [],
            'p' => [],
            'q' => [],
            'r' => [],
            's' => [],
            't' => [],
            'u' => [],
            'v' => [],
            'w' => [],
            'x' => [],
            'y' => [],
            'z' => [],
        ]);
    }

    public function test_sanitize_array_int()
    {
        $sanitizers = array_fill_keys(array_keys($this->testValues), 'array-int');
        $sanitized = $this->sanitize($this->testValues, $sanitizers);
        $this->assertEquals($sanitized, [
            'a' => [],
            'b' => [],
            'c' => [1],
            'd' => [],
            'e' => [13],
            'f' => [],
            'g' => [13],
            'h' => [],
            'i' => [],
            'j' => [],
            'k' => [],
            'l' => [],
            'm' => [],
            'n' => [1989],
            'o' => [],
            'p' => [],
            'q' => [2020],
            'r' => [],
            's' => [],
            't' => [],
            'u' => [],
            'v' => [],
            'w' => [],
            'x' => [],
            'y' => [],
            'z' => [],
        ]);
    }

    public function test_sanitize_bool()
    {
        $sanitizers = array_fill_keys(array_keys($this->testValues), 'bool');
        $sanitized = $this->sanitize($this->testValues, $sanitizers);
        $this->assertEquals($sanitized, [
            'a' => false,
            'b' => false,
            'c' => false,
            'd' => false,
            'e' => false,
            'f' => false,
            'g' => false,
            'h' => false,
            'i' => true,
            'j' => false,
            'k' => false,
            'l' => false,
            'm' => false,
            'n' => false,
            'o' => false,
            'p' => false,
            'q' => false,
            'r' => false,
            's' => false,
            't' => false,
            'u' => false,
            'v' => false,
            'w' => false,
            'x' => false,
            'y' => false,
            'z' => false,
        ]);
    }

    public function test_sanitize_date()
    {
        $today = gmdate('Y-m-d H:i:s', time());
        $sanitizers = array_fill_keys(array_keys($this->testValues), 'date');
        $sanitized = $this->sanitize($this->testValues, $sanitizers);
        $this->assertEquals($sanitized, [
            'a' => $today,
            'b' => $today,
            'c' => $today,
            'd' => $today,
            'e' => $today,
            'f' => $today,
            'g' => $today,
            'h' => $today,
            'i' => $today,
            'j' => $today,
            'k' => $today,
            'l' => $today,
            'm' => $today,
            'n' => '1989-06-13 00:00:00',
            'o' => '2020-12-03 00:00:00',
            'p' => '2019-11-30 00:00:00',
            'q' => gmdate('Y-m-d H:i:s', strtotime('2020')),
            'r' => $today,
            's' => $today,
            't' => $today,
            'u' => $today,
            'v' => $today,
            'w' => $today,
            'x' => $today,
            'y' => $today,
            'z' => $today,
        ]);
    }

    public function test_sanitize_email()
    {
        $sanitizers = array_fill_keys(array_keys($this->testValues), 'email');
        $sanitized = $this->sanitize($this->testValues, $sanitizers);
        $this->assertEquals($sanitized, [
            'a' => '',
            'b' => '',
            'c' => '',
            'd' => '',
            'e' => '',
            'f' => '',
            'g' => '',
            'h' => '',
            'i' => '',
            'j' => '',
            'k' => '',
            'l' => '',
            'm' => '',
            'n' => '',
            'o' => '',
            'p' => '',
            'q' => '',
            'r' => '',
            's' => '',
            't' => '',
            'u' => '',
            'v' => 'matt@wordpress.org',
            'w' => '',
            'x' => '',
            'y' => '',
            'z' => '',
        ]);
    }

    public function test_sanitize_int()
    {
        $sanitizers = array_fill_keys(array_keys($this->testValues), 'int');
        $sanitized = $this->sanitize($this->testValues, $sanitizers);
        $this->assertEquals($sanitized, [
            'a' => 0,
            'b' => 0,
            'c' => 0,
            'd' => 0,
            'e' => 0,
            'f' => 0,
            'g' => 0,
            'h' => 0,
            'i' => 1,
            'j' => 0,
            'k' => 0,
            'l' => 0,
            'm' => 0,
            'n' => 0,
            'o' => 0,
            'p' => 0,
            'q' => 2020,
            'r' => 0,
            's' => 0,
            't' => 0,
            'u' => 0,
            'v' => 0,
            'w' => 0,
            'x' => 0,
            'y' => 0,
            'z' => 0,
        ]);
    }

    public function test_sanitize_key()
    {
        $sanitizers = array_fill_keys(array_keys($this->testValues), 'key');
        $sanitized = $this->sanitize($this->testValues, $sanitizers);
        $this->assertEquals($sanitized, [
            'a' => '',
            'b' => 'abc',
            'c' => '',
            'd' => '',
            'e' => '',
            'f' => '',
            'g' => '',
            'h' => '',
            'i' => '',
            'j' => '',
            'k' => '',
            'l' => 'thisisatitle',
            'm' => 'hellothere',
            'n' => 'june131989',
            'o' => '03-12-2020',
            'p' => '0-0-2020',
            'q' => '2020',
            'r' => 'xxxx',
            's' => 'axdextomorrow200200peter',
            't' => 'thisistrue',
            'u' => 'thisisfalse',
            'v' => 'mattwordpressorg',
            'w' => 'httpswordpressorg',
            'x' => 'wordpressorg',
            'y' => 'wwwwordpressorg',
            'z' => 'httpswordpressorg',
        ]);
    }

    public function test_sanitize_slug()
    {
        $sanitizers = array_fill_keys(array_keys($this->testValues), 'slug');
        $sanitized = $this->sanitize($this->testValues, $sanitizers);
        $this->assertEquals($sanitized, [
            'a' => '',
            'b' => 'abc',
            'c' => '',
            'd' => '',
            'e' => '',
            'f' => '',
            'g' => '',
            'h' => '',
            'i' => '',
            'j' => '',
            'k' => '',
            'l' => 'this-is-a-title',
            'm' => 'hello-there',
            'n' => 'june-13-1989',
            'o' => '03-12-2020',
            'p' => '0-0-2020',
            'q' => '2020',
            'r' => 'xx-xx',
            's' => 'axdextomorrow-200-200-peter',
            't' => 'this-is-true',
            'u' => 'this-is-false',
            'v' => 'mattwordpress-org',
            'w' => 'https-wordpress-org',
            'x' => 'wordpress-org',
            'y' => 'www-wordpress-org',
            'z' => 'https-wordpress-org',
        ]);
    }

    public function test_sanitize_text()
    {
        $sanitizers = array_fill_keys(array_keys($this->testValues), 'text');
        $sanitized = $this->sanitize($this->testValues, $sanitizers);
        $this->assertEquals($sanitized, [
            'a' => '',
            'b' => 'abc',
            'c' => '',
            'd' => '',
            'e' => '',
            'f' => '',
            'g' => '',
            'h' => '',
            'i' => '',
            'j' => '',
            'k' => '',
            'l' => 'This is a title!',
            'm' => "Hello there!",
            'n' => 'June 13, 1989',
            'o' => '03-12-2020',
            'p' => '0-0-2020',
            'q' => '2020',
            'r' => 'xx xx',
            's' => '#ax+dex(tomorrow) $200 200% @peter',
            't' => 'this is true',
            'u' => 'this is false',
            'v' => 'matt@wordpress.org',
            'w' => 'https//wordpress.org',
            'x' => 'wordpress.org',
            'y' => 'www.wordpress.org',
            'z' => 'https://wordpress.org',
        ]);
    }

    public function test_sanitize_text_multiline()
    {
        $sanitizers = array_fill_keys(array_keys($this->testValues), 'text-multiline');
        $sanitized = $this->sanitize($this->testValues, $sanitizers);
        $this->assertEquals($sanitized, [
            'a' => '',
            'b' => 'abc',
            'c' => '',
            'd' => '',
            'e' => '',
            'f' => '',
            'g' => '',
            'h' => '',
            'i' => '',
            'j' => '',
            'k' => '',
            'l' => 'This is a title!',
            'm' => "Hello\nthere!",
            'n' => 'June 13, 1989',
            'o' => '03-12-2020',
            'p' => '0-0-2020',
            'q' => '2020',
            'r' => 'xx xx',
            's' => '#ax+dex(tomorrow) $200 200% @peter',
            't' => 'this is true',
            'u' => 'this is false',
            'v' => 'matt@wordpress.org',
            'w' => 'https//wordpress.org',
            'x' => 'wordpress.org',
            'y' => 'www.wordpress.org',
            'z' => 'https://wordpress.org',
        ]);
    }

    public function test_sanitize_url()
    {
        $sanitizers = array_fill_keys(array_keys($this->testValues), 'url');
        $sanitized = $this->sanitize($this->testValues, $sanitizers);
        $this->assertEquals($sanitized, [
            'a' => '',
            'b' => '',
            'c' => '',
            'd' => '',
            'e' => '',
            'f' => '',
            'g' => '',
            'h' => '',
            'i' => '',
            'j' => '',
            'k' => '',
            'l' => '',
            'm' => '',
            'n' => '',
            'o' => '',
            'p' => '',
            'q' => '',
            'r' => '',
            's' => '',
            't' => '',
            'u' => '',
            'v' => '',
            'w' => '',
            'x' => 'https://wordpress.org',
            'y' => 'https://www.wordpress.org',
            'z' => 'https://wordpress.org',
        ]);
    }

    protected function sanitize(array $args, array $sanitizers = [])
    {
        return (new Sanitizer($args, $sanitizers))->run();
    }
}
