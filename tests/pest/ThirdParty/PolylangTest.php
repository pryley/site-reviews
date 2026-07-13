<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Multilingual\Polylang;
use GeminiLabs\SiteReviews\Tests\PolylangFake;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createTerm;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Reviews on a Polylang site.
 *
 * A review is assigned to a page. On a multilingual site that page exists several times over —
 * one post per language — and the plugin has to decide which of them a review "belongs" to, and
 * which of them to show it on. Two different questions, and the plugin answers them with two
 * different methods:
 *
 *   getPostIdsForAllLanguages()  used when QUERYING: a review assigned to the English page must
 *                                appear on the French one too, because it is the same page.
 *   getPostId() / getPostIds()   used when DISPLAYING a review: the assigned page should be the
 *                                one in the language the visitor is reading.
 *
 * Get the first wrong and a French visitor sees no reviews. Get the second wrong and they see
 * the reviews, with a link to the English page.
 *
 * Polylang is faked (tests/stubs/polylang.php + Support/PolylangFake) with its own documented
 * semantics — notably that pll_get_post($id, $lang) defaults $lang to the CURRENT language,
 * which is the whole purpose of the function.
 */

beforeEach(function () {
    resetPluginState();
    PolylangFake::reset();
    glsr(OptionManager::class)->set('settings.general.multilingual', 'polylang');
    PolylangFake::$translatedPostTypes = ['page', 'post'];
    PolylangFake::$translatedTaxonomies = [glsr()->taxonomy];
});

afterEach(function () {
    PolylangFake::reset();
});

function polylang(): Polylang
{
    return glsr(Polylang::class);
}

/**
 * An English page and its French translation.
 *
 * @return int[] [$english, $french]
 */
function translatedPages(): array
{
    $english = createPost(['post_title' => 'About us']);
    $french = createPost(['post_title' => 'À propos']);
    PolylangFake::linkPosts(['en' => $english, 'fr' => $french]);

    return [$english, $french];
}

/*
 * Is it on?
 */

test('polylang is only used when the site has chosen it', function () {
    expect(polylang()->isActive())->toBeTrue()   // the plugin is installed
        ->and(polylang()->isEnabled())->toBeTrue()
        ->and(polylang()->isSupported())->toBeTrue();

    glsr(OptionManager::class)->set('settings.general.multilingual', '');
    expect(polylang()->isEnabled())->toBeFalse();

    // …and a site with Polylang installed but not selected is left completely alone
    [$english, $french] = translatedPages();
    expect(polylang()->getPostId($english))->toBe($english)
        ->and(polylang()->getPostIdsForAllLanguages([$english]))->toBe([$english]);
});

/*
 * Querying: a review assigned to one language's page belongs to all of them.
 */

test('a review assigned to the english page is found on the french one', function () {
    // THE ONE THAT DECIDES WHETHER A FRENCH VISITOR SEES ANY REVIEWS AT ALL. The review was left
    // on the English page; the French page is the same page in another language; the reviews
    // must appear on both.
    [$english, $french] = translatedPages();

    expect(polylang()->getPostIdsForAllLanguages([$english]))
        ->toEqualCanonicalizing([$english, $french]);
});

test('a page that is not translated is left as it is', function () {
    $post = createPost();
    PolylangFake::$translatedPostTypes = []; // this post type is not translated by Polylang

    expect(polylang()->getPostIdsForAllLanguages([$post]))->toBe([$post]);
});

test('a category is looked for in every language too', function () {
    $english = createTerm(['taxonomy' => glsr()->taxonomy]);
    $french = createTerm(['taxonomy' => glsr()->taxonomy]);
    PolylangFake::linkTerms(['en' => $english, 'fr' => $french]);

    expect(polylang()->getTermIdsForAllLanguages([$english]))
        ->toEqualCanonicalizing([$english, $french]);
});

/*
 * Displaying: the assigned page should be the one the visitor is reading.
 */

test('the assigned page is the one in the language being read', function () {
    // A French visitor reading a review of "About us" should be pointed at "À propos".
    //
    // getPostId() is documented as "Get the translated Post ID for the current language", and
    // its WPML sibling does exactly that — apply_filters('wpml_object_id', $id, $type, true)
    // returns the object in the CURRENT language.
    [$english, $french] = translatedPages();
    PolylangFake::$currentLanguage = 'fr';

    expect(polylang()->getPostId($english))->toBe($french);
    expect(polylang()->getPostIds([$english]))->toBe([$french]);
});

test('the assigned category is the one in the language being read', function () {
    $english = createTerm(['taxonomy' => glsr()->taxonomy]);
    $french = createTerm(['taxonomy' => glsr()->taxonomy]);
    PolylangFake::linkTerms(['en' => $english, 'fr' => $french]);
    PolylangFake::$currentLanguage = 'fr';

    expect(polylang()->getTermId($english))->toBe($french);
});

test('a page with no translation in the current language stays as it is', function () {
    // Better the English page than no page.
    $post = createPost();
    PolylangFake::linkPosts(['en' => $post]);
    PolylangFake::$currentLanguage = 'fr';

    expect(polylang()->getPostId($post))->toBe($post);
});
