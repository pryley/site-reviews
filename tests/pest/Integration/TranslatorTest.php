<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Translation;
use GeminiLabs\SiteReviews\Modules\Translator;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Letting a site owner rewrite the plugin's own words.
 *
 * Settings > Translations lets someone change any string the plugin ships — "Submit your review" →
 * "Leave feedback", "Anonymous" → "A guest". Not translation in the gettext sense: the site is in
 * English and they want different English.
 *
 * It filters `gettext_site-reviews`, which fires for EVERY string the plugin renders, on every page.
 * So the assertions that matter are cheap: an uncustomised string comes back untouched, and a string
 * from someone else's text domain is not ours to rewrite.
 *
 * Translation::strings() caches the custom strings for the request, on the Translation
 * singleton. Each test here swaps in a fresh instance, so each test reads the strings it set,
 * and the original instance goes back afterwards for the files that run next.
 */

beforeEach(function () {
    resetPluginState();
    $this->translation = glsr(Translation::class);
    glsr()->alias(Translation::class, new Translation());

    // As the Translations settings screen saves them. `type` is not stored — normalizeStrings()
    // derives it from whether a `p1` key is PRESENT, and drops any string without an `id`.
    glsr(OptionManager::class)->set('settings.strings', [
        [
            'id' => 'a-single-string',
            's1' => 'A phrase used nowhere else in the plugin',
            's2' => 'The words the site owner wanted instead',
        ],
        [
            'id' => 'a-plural-string',
            's1' => 'One thing used nowhere else',
            'p1' => 'Several things used nowhere else',
            's2' => 'One thing, their way',
            'p2' => 'Several things, their way',
        ],
    ]);
});

afterEach(fn () => glsr()->alias(Translation::class, $this->translation));

test('a string nobody has customised is handed back exactly as it was', function () {
    // The common case, and it is the whole plugin: this filter runs on every string on every page.
    // Anything other than "return the original" here is a bug on every site that ever renders a
    // review.
    expect(glsr(Translator::class)->translate('Submit your review', 'site-reviews', [
        'single' => 'Submit your review',
    ]))->toBe('Submit your review');
});

test('a customised string is replaced with the words the site owner chose', function () {
    expect(glsr(Translator::class)->translate('A phrase used nowhere else in the plugin', 'site-reviews', [
        'single' => 'A phrase used nowhere else in the plugin',
    ]))->toBe('The words the site owner wanted instead');
});

test('a site owner who changes their words is heard', function () {
    // The strings are read once per request. A test, like a page load, starts with none cached,
    // so a set saved here replaces the one from beforeEach.
    glsr(OptionManager::class)->set('settings.strings', [
        [
            'id' => 'a-single-string',
            's1' => 'A phrase used nowhere else in the plugin',
            's2' => 'Their second thoughts',
        ],
    ]);

    expect(glsr(Translator::class)->translate('A phrase used nowhere else in the plugin', 'site-reviews', [
        'single' => 'A phrase used nowhere else in the plugin',
    ]))->toBe('Their second thoughts');
});

test('a customised plural is replaced in both its forms, and the number still picks between them', function () {
    // The plural branch has to keep BOTH replacements and then hand them to the text domain, so
    // that "1 review" / "2 reviews" still agrees with the number after being rewritten.
    $translator = glsr(Translator::class);
    $args = [
        'plural' => 'Several things used nowhere else',
        'single' => 'One thing used nowhere else',
    ];

    expect($translator->translate('One thing used nowhere else', 'site-reviews', $args + ['number' => 1]))
        ->toBe('One thing, their way');
    expect($translator->translate('One thing used nowhere else', 'site-reviews', $args + ['number' => 2]))
        ->toBe('Several things, their way');
});

test('a string belonging to somebody else is not ours to rewrite', function () {
    // The domain gate, and it comes FIRST — before the lookup. Even a string this plugin has been
    // told to customise is left alone when WooCommerce is the one asking, because those are
    // WooCommerce's words that happen to read the same.
    expect(glsr(Translator::class)->translate('A phrase used nowhere else in the plugin', 'woocommerce', [
        'single' => 'A phrase used nowhere else in the plugin',
    ]))->toBe('A phrase used nowhere else in the plugin');
});

test('an addon can add its own text domain to the ones that may be rewritten', function () {
    // How the premium addons get their strings into the same settings screen.
    add_filter('site-reviews/translator/domains', fn ($domains) => [...$domains, 'site-reviews-images']);

    expect(glsr(Translator::class)->translate('A phrase used nowhere else in the plugin', 'site-reviews-images', [
        'single' => 'A phrase used nowhere else in the plugin',
    ]))->toBe('The words the site owner wanted instead');
});

test('the arguments are normalized, so a caller may leave anything out', function () {
    // gettext, ngettext, and their _with_context variants all arrive here with different shapes.
    // A missing `number` or `plural` must not be a warning on somebody's front end.
    expect(glsr(Translator::class)->translate('Anything', 'site-reviews', []))->toBe('Anything');
    expect(glsr(Translator::class)->translate('Anything', 'site-reviews', ['single' => 'Anything']))
        ->toBe('Anything');
});

test('a translation can be looked up without going back through the filter', function () {
    // getTranslation() is used when the plugin needs a string WordPress has already translated —
    // it asks the text domain directly rather than re-entering the filter it is being called from.
    expect(glsr(Translator::class)->getTranslation([
        'number' => 1,
        'plural' => 'Reviews',
        'single' => 'Review',
    ]))->toBe('Review');

    expect(glsr(Translator::class)->getTranslation([
        'number' => 2,
        'plural' => 'Reviews',
        'single' => 'Review',
    ]))->toBe('Reviews');
});
