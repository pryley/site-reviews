<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Text;
use GeminiLabs\SiteReviews\Modules\Console;

uses()->group('plugin');

test('escapeShortcodes encodes square brackets', function () {
    expect(Text::escapeShortcodes('<h4>[probe x=1]</h4>'))->toBe('<h4>&#091;probe x=1&#093;</h4>');
    expect(Text::escapeShortcodes('<a href="/?a=[b]" data-c=\'[1,2]\'>d</a>'))->toBe('<a href="/?a=&#091;b&#093;" data-c=\'&#091;1,2&#093;\'>d</a>');
});

test('escapeShortcodes encodes the entities do_shortcode decodes', function () {
    expect(Text::escapeShortcodes('&#91;probe&#93;'))->toBe('&#091;probe&#093;');
    expect(Text::escapeShortcodes('&#091;probe&#093;'))->toBe('&#091;probe&#093;');
    expect(Text::escapeShortcodes('&amp;#91;probe'))->toBe('&amp;#91;probe');
});

test('escapeShortcodes leaves script and style elements alone', function () {
    $html = '<script>var a = [1];</script><STYLE>a[b]{}</STYLE>';
    expect(Text::escapeShortcodes($html.'[c]'))->toBe($html.'&#091;c&#093;');
    expect(Text::escapeShortcodes('<script type="module">[1]</script>[c]'))->toBe('<script type="module">[1]</script>&#091;c&#093;');
});

test('escapeShortcodes encodes everything outside a complete script element', function () {
    expect(Text::escapeShortcodes('<script>[1]'))->toBe('<script>&#091;1&#093;');
    expect(Text::escapeShortcodes('< script>[1]</script>'))->toBe('< script>&#091;1&#093;</script>');
    expect(Text::escapeShortcodes('<scripted>[1]</scripted>'))->toBe('<scripted>&#091;1&#093;</scripted>');
});

test('escapeShortcodes encodes braces', function () {
    // Bricks reads a brace pair in rendered output as a dynamic data tag of its own.
    expect(Text::escapeShortcodes('<p>{probe} and {probe:x}</p>'))->toBe('<p>&#123;probe&#125; and &#123;probe:x&#125;</p>');
    expect(Text::escapeShortcodes('<div data-c=\'{"a":1}\'>d</div>'))->toBe('<div data-c=\'&#123;"a":1&#125;\'>d</div>');
    $once = Text::escapeShortcodes('<a title="{b}">{c} &#123;d&#125;</a>');
    expect($once)->toBe('<a title="&#123;b&#125;">&#123;c&#125; &#123;d&#125;</a>')
        ->and(Text::escapeShortcodes($once))->toBe($once);
});

test('escapeShortcodes leaves the braces of script and style elements alone', function () {
    $html = '<script>var a = {b: 1};</script><style>a{color:red}</style>';
    expect(Text::escapeShortcodes($html.'{c}'))->toBe($html.'&#123;c&#125;');
});

test('escapeShortcodes is idempotent', function () {
    $once = Text::escapeShortcodes('<a href="[b]">[c] &#91;d&#93;</a><script>[1]</script>');
    expect(Text::escapeShortcodes($once))->toBe($once);
});

test('escapeShortcodes encodes everything when the pattern fails', function () {
    $limit = ini_get('pcre.backtrack_limit');
    $jit = ini_get('pcre.jit');
    ini_set('pcre.jit', '0');
    ini_set('pcre.backtrack_limit', '1000'); // low enough for this script but not for the query the log runs
    $script = str_repeat('x', 5000);
    $result = Text::escapeShortcodes("<script>{$script}[1];</script>[c]");
    ini_set('pcre.backtrack_limit', $limit);
    ini_set('pcre.jit', $jit);
    expect($result)->toBe("<script>{$script}&#091;1&#093;;</script>&#091;c&#093;")
        ->and(glsr(Console::class)->get())->toContain('Text::escapeShortcodes');
});

test('initials', function () {
    expect(Text::initials((string) null, ' '))->toEqual('');
    expect(Text::initials('Steve', ' '))->toEqual('S');
    expect(Text::initials('Steve', '.'))->toEqual('S.');
    expect(Text::initials('Steve', '. '))->toEqual('S.');
    expect(Text::initials('Steve Jobs', ' '))->toEqual('S J');
    expect(Text::initials('Steve Jobs', '.'))->toEqual('S.J.');
    expect(Text::initials('Steve Jobs', '. '))->toEqual('S. J.');
    expect(Text::initials('Steve Paul Jobs', ' '))->toEqual('S P J');
    expect(Text::initials('Steve Paul Jobs', '.'))->toEqual('S.P.J.');
    expect(Text::initials('Steve Paul Jobs', '. '))->toEqual('S. P. J.');
});

test('name', function () {
    expect(Text::name('Steve'))->toEqual('Steve');
    expect(Text::name('Steve Jobs'))->toEqual('Steve Jobs');
    expect(Text::name('Steve Paul Jobs'))->toEqual('Steve Paul Jobs');
});

test('name first', function () {
    expect(Text::name('Steve', 'first'))->toEqual('Steve');
    expect(Text::name('Steve Jobs', 'first'))->toEqual('Steve');
    expect(Text::name('Steve Paul Jobs', 'first'))->toEqual('Steve');
});

test('name first initial', function () {
    expect(Text::name('Steve', 'first_initial'))->toEqual('S');
    expect(Text::name('Steve Jobs', 'first_initial'))->toEqual('S Jobs');
    expect(Text::name('Steve Paul Jobs', 'first_initial'))->toEqual('S Jobs');
});

test('name first initial period', function () {
    expect(Text::name('Steve', 'first_initial', 'period'))->toEqual('S.');
    expect(Text::name('Steve Jobs', 'first_initial', 'period'))->toEqual('S.Jobs');
    expect(Text::name('Steve Paul Jobs', 'first_initial', 'period'))->toEqual('S.Jobs');
});

test('name first initial period space', function () {
    expect(Text::name('Steve', 'first_initial', 'period_space'))->toEqual('S.');
    expect(Text::name('Steve Jobs', 'first_initial', 'period_space'))->toEqual('S. Jobs');
    expect(Text::name('Steve Paul Jobs', 'first_initial', 'period_space'))->toEqual('S. Jobs');
});

test('name last initial', function () {
    expect(Text::name('Steve', 'last_initial'))->toEqual('Steve');
    expect(Text::name('Steve Jobs', 'last_initial'))->toEqual('Steve J');
    expect(Text::name('Steve Paul Jobs', 'last_initial'))->toEqual('Steve J');
});

test('name last initial period', function () {
    expect(Text::name('Steve', 'last_initial', 'period'))->toEqual('Steve');
    expect(Text::name('Steve Jobs', 'last_initial', 'period'))->toEqual('Steve J.');
    expect(Text::name('Steve Paul Jobs', 'last_initial', 'period'))->toEqual('Steve J.');
});

test('name last initial period space', function () {
    expect(Text::name('Steve', 'last_initial', 'period_space'))->toEqual('Steve');
    expect(Text::name('Steve Jobs', 'last_initial', 'period_space'))->toEqual('Steve J.');
    expect(Text::name('Steve Paul Jobs', 'last_initial', 'period_space'))->toEqual('Steve J.');
});

test('name initials', function () {
    expect(Text::name('Steve', 'initials'))->toEqual('S');
    expect(Text::name('Steve Jobs', 'initials'))->toEqual('S J');
    expect(Text::name('Steve Paul Jobs', 'initials'))->toEqual('S P J');
});

test('name initials period', function () {
    expect(Text::name('Steve', 'initials', 'period'))->toEqual('S.');
    expect(Text::name('Steve Jobs', 'initials', 'period'))->toEqual('S.J.');
    expect(Text::name('Steve Paul Jobs', 'initials', 'period'))->toEqual('S.P.J.');
});

test('text becomes paragraphs', function () {
    expect(Text::text("First line\nSecond line"))
        ->toContain('<p>First line</p>')
        ->toContain('<p>Second line</p>');
});

test('words are counted with the intl word-break rules', function () {
    expect(Text::wordCount('One two three.'))->toBe(3);
    expect(Text::wordCount('One, two — three!'))->toBe(3); // punctuation is not words
    // The regex fallback for text intl cannot segment: preg_split of an empty string is
    // [''] — one element — so empty text counts as ONE word. Confirmed by execution;
    // harmless while a required-rule runs first, but it is what the code does.
    expect(Text::wordCount(''))->toBe(1);
});

test('words are cut at the limit, not mid-word', function () {
    expect(Text::words('one two three four', 2))->toBe('one two');
    expect(Text::words('one two', 5))->toBe('one two'); // a limit past the end changes nothing
});

test('text the intl normalizer rejects falls back to the regex splitter', function () {
    // Invalid UTF-8: Normalizer::normalize() returns false, and the /u regex fallback
    // cannot match it either — the excerpt degrades to empty rather than warning.
    $invalid = "\xC3\x28 two three";
    expect(Text::words($invalid, 2))->toBe('');
    expect(Text::words($invalid, 0))->toHaveLength(12); // no limit: the full length, verbatim bytes
});

test('an excerpt within the limit is a plain paragraph', function () {
    expect(Text::excerpt('one two', 55))->toBe('<p>one two</p>');
});

test('an excerpt over the limit hides the rest behind a toggle', function () {
    expect(Text::excerpt('one two three four five six seven eight', 3))->toBe(
        '<p class="glsr-hidden-text" data-show-less="Show less" data-show-more="Show more" data-trigger="expand">'
        .'one two three<span class="glsr-hidden"> four five six seven eight</span></p>'
    );
});

test('a paragraph past the cut is hidden whole', function () {
    $excerpt = Text::excerpt("short one\n\nsecond paragraph here", 2);

    expect($excerpt)->toContain('short one<span class="glsr-hidden"></span>')
        ->toContain('<p class="glsr-hidden">second paragraph here</p>');
});

test('texturized punctuation counts as one character, not as its entity', function () {
    // normalize() leaves a curly quote as the seven-character &#8220;. Counted as written, a
    // quoted review was cut six characters short per quote.
    $excerpt = Text::excerpt('He said "hello" and then left', 10, false);

    expect($excerpt)->toContain('He said &#8220;h<span class="glsr-hidden">ello&#8221; and then left</span>');
});

test('an entity does not count as a word', function () {
    // The word-break rules read &#8220; as the number 8220: a quoted word cost two words.
    $excerpt = Text::excerpt('I said "hello" to them all', 3);

    expect($excerpt)->toContain('I said &#8220;hello<span class="glsr-hidden">&#8221; to them all</span>');
});

test('the words inside a tag count, and the tag survives the excerpt', function () {
    $excerpt = Text::excerpt('Great <a href="https://x.com">product</a> here and more words', 4);

    expect($excerpt)->toContain('Great <a href="https://x.com">product</a> here and<span class="glsr-hidden"> more words</span>');
});

test('a tag that is open at the cut is closed before it and opened again after it', function () {
    expect(Text::excerpt('One <em>two three four</em> five', 2))->toBe(
        '<p class="glsr-hidden-text" data-show-less="Show less" data-show-more="Show more" data-trigger="expand">'
        .'One <em>two</em><span class="glsr-hidden"><em> three four</em> five</span></p>'
    );
});

test('a cut at the end of a tag leaves the tag whole', function () {
    $excerpt = Text::excerpt('A <a href="https://x.com" title="t"><strong>bold link</strong></a> end', 3);

    expect($excerpt)->toContain('A <a href="https://x.com" title="t"><strong>bold link</strong></a><span class="glsr-hidden"> end</span>');
});

test('a tag that is open at a line break is closed there and opened again in the next paragraph', function () {
    expect(Text::excerpt("One <strong>two\n\nthree</strong> four"))
        ->toBe("<p>One <strong>two</strong></p>\n<p><strong>three</strong> four</p>");
});

test('an excerpt keeps only the text when a tag is not inline, and the tag ends its line', function () {
    // A snippet can allow a tag such as br or p in a review.
    $allow = fn () => array_intersect_key(wp_kses_allowed_html('post'), array_flip(['br', 'em', 'p']));
    add_filter('site-reviews/sanitize/allowed-html', $allow);
    try {
        expect(Text::excerpt('One <em>two</em><br>three four'))->toBe("<p>One two</p>\n<p>three four</p>")
            ->and(Text::excerpt('<p>One <em>two</em></p><p>three four</p>'))->toBe("<p>One two</p>\n<p>three four</p>");
    } finally {
        remove_filter('site-reviews/sanitize/allowed-html', $allow);
    }
});

test('an excerpt keeps only the text when its tags are closed out of order', function () {
    // WordPress's HTML API refuses tags it would have to rebuild.
    $allow = fn () => array_intersect_key(wp_kses_allowed_html('post'), array_flip(['b', 'i']));
    add_filter('site-reviews/sanitize/allowed-html', $allow);
    try {
        expect(Text::excerpt('<b>one <i>two</b> three</i> four'))->toBe('<p>one two three four</p>');
    } finally {
        remove_filter('site-reviews/sanitize/allowed-html', $allow);
    }
});

test('a tag that is never closed, or closed without being opened, leaves no tag open', function () {
    $tags = fn (string $html) => [preg_match_all('/<(em|p|span)\b/', $html), preg_match_all('~</(em|p|span)>~', $html)];

    foreach (['<em>one two three four', "<em>one two\n\nthree four", 'one</em> two three four', "one</em> two\n\nthree four"] as $text) {
        [$opened, $closed] = $tags(Text::excerpt($text, 2));
        expect($opened)->toBe($closed);
    }
    expect(Text::excerpt('<em>one two three four', 2))->toContain('<em>one two</em><span class="glsr-hidden"><em> three four</em></span>');
});

test('a tag that is opened again does not repeat its id', function () {
    $allow = fn () => ['em' => ['class' => true, 'id' => true]];
    add_filter('site-reviews/sanitize/allowed-html', $allow);
    try {
        expect(Text::excerpt('<em id="x" class="y">one two three four</em>', 2))
            ->toContain('<em id="x" class="y">one two</em><span class="glsr-hidden"><em class="y"> three four</em></span>');
    } finally {
        remove_filter('site-reviews/sanitize/allowed-html', $allow);
    }
});

test('an excerpt cut by characters counts the text of a tag, not the tag', function () {
    expect(Text::excerpt('One <em>two three</em> four', 6, false))
        ->toContain('One <em>tw</em><span class="glsr-hidden"><em>o three</em> four</span>');
});

test('a character typed in the text is not read as a tag of the excerpt', function () {
    expect(Text::excerpt('Five ⍈ stars and <strong>bold</strong> words'))
        ->toBe('<p>Five ⍈ stars and <strong>bold</strong> words</p>');
});

test('the text beside a tag keeps its entities', function () {
    expect(Text::excerpt('Tom & <em>Jerry</em> ate 3 < 4 pies'))->toBe('<p>Tom &amp; <em>Jerry</em> ate 3 &lt; 4 pies</p>');
});

test('name initials period space', function () {
    expect(Text::name('Steve', 'initials', 'period_space'))->toEqual('S.');
    expect(Text::name('Steve Jobs', 'initials', 'period_space'))->toEqual('S. J.');
    expect(Text::name('Steve Paul Jobs', 'initials', 'period_space'))->toEqual('S. P. J.');
});

test('without the intl extension the word count falls back to a plain split', function () {
    // A PHP build without intl (the armed shadow reports exactly that): no break iterator,
    // so counting and excerpting fall back to regex splitting and still answer.
    \GeminiLabs\SiteReviews\Tests\armFailingFunction('extension_loaded');
    try {
        expect(Text::wordCount('Hello wonderful world'))->toBe(3)
            ->and(Text::words('Hello wonderful world', 2))->toBe('Hello wonderful');
    } finally {
        \GeminiLabs\SiteReviews\Tests\disarmFailingFunctions();
    }
});
