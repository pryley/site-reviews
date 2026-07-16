<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Text;

uses()->group('plugin');

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

test('html tags do not count against the limit, and survive the excerpt', function () {
    // Tags are swapped out for a placeholder before measuring and restored afterwards, so
    // a link mid-review is neither truncated in half nor counted as words.
    $excerpt = Text::excerpt('Great <a href="https://x.com">product</a> here and more words', 4);

    expect($excerpt)->toContain('<a href="https://x.com">product</a>')
        ->toContain('<span class="glsr-hidden"> words</span>');
});

test('name initials period space', function () {
    expect(Text::name('Steve', 'initials', 'period_space'))->toEqual('S.');
    expect(Text::name('Steve Jobs', 'initials', 'period_space'))->toEqual('S. J.');
    expect(Text::name('Steve Paul Jobs', 'initials', 'period_space'))->toEqual('S. P. J.');
});
