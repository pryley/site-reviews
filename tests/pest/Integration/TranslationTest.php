<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Translation;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The machinery behind the Translations settings screen.
 *
 * Translator (TranslatorTest) APPLIES a custom string once it is saved; this module is everything
 * before that: parsing the shipped .pot catalog into the strings a site owner may customise,
 * searching it, and rendering the settings rows — including the warnings when a saved custom
 * string has gone stale (the original text changed in an update) or is broken (the site owner's
 * text dropped a %s the plugin needs to fill in).
 *
 * Two properties of the class shape these tests:
 *
 * 1. strings() memoises into a function-level static nothing can reset (TranslatorTest fills it
 *    permanently, whatever the file order), so every test needing saved strings swaps them in
 *    through a subclass — the same sidestep TranslationControllerTest uses.
 *
 * 2. entries() parses languages/site-reviews.pot — the REAL catalog, because that file being
 *    parseable, keyed by msgid, and stripped of admin-text entries is exactly the behaviour under
 *    test. Assertions use entries that have been stable for years ("Anonymous", "No",
 *    "%s star or less") rather than counts, which change with every release.
 */

beforeEach(function () {
    resetPluginState();
});

/**
 * A Translation whose saved custom strings are known, sidestepping the memoising static.
 * Strings are given in their NORMALIZED shape (id/s1/s2/p1/p2/type), as strings() returns them.
 */
function translationWithStrings(array $strings): Translation
{
    return new class($strings) extends Translation {
        private array $fakeStrings;

        public function __construct(array $strings)
        {
            $this->fakeStrings = $strings;
        }

        public function strings(): array
        {
            return $this->fakeStrings;
        }
    };
}

/**
 * A normalized saved string, as the Translations screen stores one.
 */
function customString(string $id, string $s2 = '', array $overrides = []): array
{
    // the id IS the .pot key; for a context entry that is "ctx<##EOC##>msgid",
    // and s1 is the original msgid the customisation was made against
    $s1 = str_contains($id, '<##EOC##>') ? explode('<##EOC##>', $id)[1] : $id;

    return wp_parse_args($overrides, [
        'id' => $id,
        's1' => $s1,
        's2' => $s2,
        'p1' => '',
        'p2' => '',
        'type' => 'plural' === ($overrides['type'] ?? '') ? 'plural' : 'single',
    ]);
}

/*
 * The catalog.
 */

test('the catalog is the pot file, without the admin strings', function () {
    // The Translations screen is for the words a VISITOR sees. Every admin-facing string is
    // registered with an `admin-text` context precisely so it is dropped here — a site owner
    // customising their review form must not be offered the plugin's Settings menu to rename.
    $entries = glsr(Translation::class)->entries();

    expect($entries)->not->toBeEmpty()
        ->toHaveKey('Anonymous'); // a visitor-facing string, keyed by its msgid
    foreach ($entries as $entry) {
        expect($entry['msgctxt'] ?? '')->not->toContain(Translation::CONTEXT_ADMIN_KEY);
        expect($entry['domain'])->toBe(glsr()->id); // stamped with the plugin's text domain
    }
});

test('an entity in a msgid is decoded in the key, so a search can find it', function () {
    // The pot stores "&hellip;"; the catalog keys it as "…" — what somebody would actually
    // type into the search box.
    $entries = glsr(Translation::class)->entries();

    expect($entries)->toHaveKey('…')
        ->not->toHaveKey('&hellip;');
    expect($entries['…']['msgid'])->toBe('&hellip;'); // the msgid itself is left as shipped
});

test('an addon can add its own entries to the catalog', function () {
    // The documented filter, and how the premium addons put their strings on the same screen.
    add_filter('site-reviews/translation/entries', function (array $entries) {
        $entries['An addon string'] = ['msgid' => 'An addon string', 'domain' => 'site-reviews-addon'];
        return $entries;
    });

    expect((new Translation())->entries())->toHaveKey('An addon string');
});

test('a catalog that cannot be parsed is logged and yields nothing, not a fatal', function () {
    // The pot file is shipped, but a broken deployment (a partial upload, a file replaced by
    // something else) is somebody's live settings screen — it degrades to an empty catalog and
    // a log entry. readme.txt stands in for the corrupted file: it exists and is not a catalog.
    // (A MISSING file takes the same catch, but through a deprecated code path in the vendored
    // parser that would fail the suite's failOnDeprecation gate.)
    $entries = glsr(Translation::class)
        ->extractEntriesFromPotFile(glsr()->path('readme.txt'), glsr()->id);

    expect($entries)->toBe([]);
});

/*
 * Searching it.
 */

test('a search matches anywhere in the text, in either plural form', function () {
    $results = glsr(Translation::class)->search('anonymous')->results();

    expect($results)->toHaveKey('Anonymous');

    // matching the msgid_plural finds the entry too
    expect(glsr(Translation::class)->search('stars or less')->results())
        ->toHaveKey('%s star or less');
});

test('a very short search must match the whole string, or it would match everything', function () {
    // Under SEARCH_THRESHOLD characters, "no" as a substring is in half the catalog.
    $results = glsr(Translation::class)->search('no')->results();

    expect($results)->toHaveKey('No')
        ->not->toHaveKey('Anonymous');
});

test('searching again does not accumulate the last search\'s results', function () {
    $translation = glsr(Translation::class);
    $translation->search('anonymous');

    expect($translation->search('stars or less')->results())
        ->toHaveKey('%s star or less')
        ->not->toHaveKey('Anonymous');
});

test('reading the results consumes them; rendering them may choose not to', function () {
    $translation = glsr(Translation::class);

    $translation->search('anonymous');
    expect($translation->results())->toHaveKey('Anonymous');
    expect($translation->results())->toBe([]); // consumed

    $translation->search('anonymous');
    expect($translation->renderResults(false))->toContain('Anonymous');
    expect($translation->results())->toHaveKey('Anonymous'); // kept, then consumed here
});

test('the results can be narrowed to the saved strings, or purged of them', function () {
    // filter() keeps the entries a site already customised; exclude() removes them — how the
    // search suggestions avoid offering a string that is already in the list.
    $translation = glsr(Translation::class);
    $entries = $translation->entries();

    $kept = $translation->filter([['id' => 'Anonymous']], $entries)->results();
    expect(array_keys($kept))->toBe(['Anonymous']);

    $purged = $translation->exclude([['id' => 'Anonymous']], $entries)->results();
    expect($purged)->not->toHaveKey('Anonymous');
    expect(count($purged))->toBe(count($entries) - 1);
});

test('filtering with no arguments filters the last search against the saved strings', function () {
    $translation = translationWithStrings([customString('Anonymous', 'A guest')]);

    $results = $translation->search('anonymous')->filter()->results();

    expect(array_keys($results))->toBe(['Anonymous']);
});

/*
 * Judging a saved string.
 */

test('a custom string is missing when its original no longer exists in the plugin', function () {
    // The plugin renamed or removed the string in an update; the customisation now matches
    // nothing and silently does nothing — the settings row gets a warning instead.
    $translation = glsr(Translation::class);

    expect($translation->isMissing(['s1' => 'Anonymous']))->toBeFalse();
    expect($translation->isMissing(['s1' => 'A string this plugin never shipped']))->toBeTrue();

    // an empty original cannot be judged, and is not flagged
    expect($translation->isMissing(['s1' => '']))->toBeFalse();
    expect($translation->isMissing([]))->toBeFalse();

    // "…" was saved from the decoded key, but the msgid is stored as "&hellip;" — the
    // entity-encoded form is checked too before the string is declared missing
    expect($translation->isMissing(['s1' => '…']))->toBeFalse();
});

test('a custom string is invalid when it loses a placeholder the plugin fills in', function () {
    $translation = glsr(Translation::class);

    // untouched, or reworded with the placeholders intact: fine
    expect($translation->isInvalid(['s1' => '%s star', 's2' => '%s star', 'p1' => '', 'p2' => '']))->toBeFalse();
    expect($translation->isInvalid(['s1' => '%s star', 's2' => 'Rated %s', 'p1' => '', 'p2' => '']))->toBeFalse();

    // the single form dropped its %s
    expect($translation->isInvalid(['s1' => '%s star', 's2' => 'One star', 'p1' => '', 'p2' => '']))->toBeTrue();

    // the plural form dropped its %s, even though the single form is fine
    expect($translation->isInvalid([
        's1' => '%s star', 's2' => '%s star',
        'p1' => '%s stars', 'p2' => 'Many stars',
    ]))->toBeTrue();
});

test('counting placeholders understands the printf specifiers the plugin uses', function () {
    $translation = glsr(Translation::class);

    expect($translation->placeholders('%s of %d'))->toBe(2);
    expect($translation->placeholders('no placeholders'))->toBe(0);
    expect($translation->placeholders('100%% literal'))->toBe(0); // an escaped percent
    expect($translation->placeholders('trailing %'))->toBe(0);
    expect($translation->placeholders('%.1f and %10.3E'))->toBe(2); // precision floats
    expect($translation->placeholders('%.2q'))->toBe(0); // digits not followed by a float specifier
    expect($translation->placeholders('%1$s'))->toBe(0); // positional arguments are not counted
});

/*
 * Rendering the settings rows.
 */

test('a healthy custom string renders as a plain row', function () {
    $html = glsr(Translation::class)->render('single', customString('Anonymous', 'A guest'));

    expect($html)->toContain('glsr-string-tr')
        ->toContain('A guest')
        ->not->toContain('is-invalid');
});

test('a stale custom string is rendered with a warning', function () {
    $html = glsr(Translation::class)->render('single', customString('A string this plugin never shipped'));

    expect($html)->toContain('is-invalid')
        ->toContain('the original text has been changed or removed');
});

test('a custom string that lost its placeholder is rendered with the other warning', function () {
    $entry = customString('%s star or less', 'star or less'); // the %s is gone

    $html = glsr(Translation::class)->render('single', $entry);

    expect($html)->toContain('is-invalid')
        ->toContain('placeholder tags are missing');
});

test('the settings screen renders every saved string, with its context as the description', function () {
    // all() decorates each saved string with the msgctxt of its catalog entry — the hint that
    // tells "admin email" the button text apart from "admin email" anywhere else — and an id
    // the catalog no longer contains gets none.
    $translation = translationWithStrings([
        customString('template tag button text<##EOC##>admin email', 'the owner'),
        customString('A string this plugin never shipped', 'whatever'),
    ]);

    $all = $translation->all();
    expect($all[0]['desc'])->toBe('template tag button text')
        ->and($all[1]['desc'])->toBe('');

    $html = $translation->renderAll();
    expect($html)->toContain('the owner')
        ->toContain(OptionManager::databaseKey()) // the input names save back into the option
        ->toContain('is-invalid'); // the never-shipped string carries its warning
});

test('a search result renders with the entry a click would save', function () {
    $html = glsr(Translation::class)->search('stars or less')->renderResults();

    expect($html)->toContain('data-entry=')
        ->toContain('data-domain=\'site-reviews\'')
        ->toContain('%s star or less | %s stars or less'); // both plural forms, so the owner knows
});
