<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Translator;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Letting a site owner rewrite the plugin's own words.
 *
 * Settings > Translations lets somebody change any string the plugin ships — "Submit your review"
 * becomes "Leave feedback", "Anonymous" becomes "A guest". It is not translation in the gettext
 * sense: the site is in English and they want different English.
 *
 * It works by filtering `gettext_site-reviews`, which fires for EVERY string the plugin renders,
 * on every page of every site. So the assertions that matter are the cheap ones: a string nobody
 * customised comes back untouched, and a string belonging to somebody else's text domain is not
 * ours to rewrite.
 *
 * TWO THINGS ABOUT THE FIXTURE, both of which are properties of the code under test:
 *
 * 1. Translation::strings() memoises into a function-level `static $strings`, which nothing can
 *    reset — not the transaction, not resetGlobalState(). Once it holds a non-empty list it holds
 *    it for the rest of the PHP process. So the custom strings are defined ONCE, identically, in
 *    beforeEach: the first test to run fills the cache and every later one reads the same thing.
 *    A test here that needed a different set of strings would silently get this one.
 *
 * 2. That cache outlives this file. The phrases below are therefore deliberately ones that appear
 *    NOWHERE else in the plugin — the lookup matches on the exact original string, so a phrase
 *    nothing else ever translates cannot change anything else's behaviour, whatever order the
 *    suite runs in.
 */

beforeEach(function () {
    resetPluginState();

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
