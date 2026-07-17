<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Multilingual\Wpml;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createTerm;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Reviews on a WPML site — the same two questions PolylangTest asks (which
 * translation does a review belong to when querying, which to show when
 * displaying), against the OTHER bridge.
 *
 * WPML needs no function fakes at all: the plugin talks to it exclusively
 * through wpml_* filters, so the test IS the seam. The one thing it does need
 * is the ICL_SITEPRESS_VERSION constant, defined lazily in beforeEach — a
 * one-way door for the rest of the process, which is safe HERE because the
 * suites run in declared order and the one test that asserts WPML is absent
 * (SettingsControllerTest) lives in the Integration suite, which has already
 * finished by the time ThirdParty runs. Do not move this file to another suite.
 */

beforeEach(function () {
    resetPluginState();
    if (!defined('ICL_SITEPRESS_VERSION')) {
        define('ICL_SITEPRESS_VERSION', '4.6.5');
    }
    glsr(OptionManager::class)->set('settings.general.multilingual', 'wpml');
    $GLOBALS['glsr_wpml'] = [
        'translated_post_types' => ['page', 'post'],
        'translated_taxonomies' => [glsr()->taxonomy],
        'objects' => [], // id => translated id for the current language
        'translations' => [], // id => [element_id, element_id…]
    ];
    add_filter('wpml_is_translated_post_type', fn ($is, $type) => in_array($type, $GLOBALS['glsr_wpml']['translated_post_types']), 10, 2);
    add_filter('wpml_is_translated_taxonomy', fn ($is, $taxonomy) => in_array($taxonomy, $GLOBALS['glsr_wpml']['translated_taxonomies']), 10, 2);
    add_filter('wpml_object_id', fn ($id) => $GLOBALS['glsr_wpml']['objects'][$id] ?? $id, 10, 3);
    add_filter('wpml_element_trid', fn ($trid, $id) => $id, 10, 3); // the id doubles as its own group
    add_filter('wpml_get_element_translations',
        fn ($translations, $trid) => isset($GLOBALS['glsr_wpml']['translations'][$trid])
            ? array_map(fn ($id) => (object) ['element_id' => $id], $GLOBALS['glsr_wpml']['translations'][$trid])
            : null, // what WPML answers for an unknown translation group
        10, 3);
});

afterEach(function () {
    unset($GLOBALS['glsr_wpml']);
});

function wpml(): Wpml
{
    return glsr(Wpml::class);
}

/**
 * An English page and its French translation, with French current.
 *
 * @return int[] [$english, $french]
 */
function wpmlTranslatedPages(): array
{
    $english = createPost(['post_title' => 'About us']);
    $french = createPost(['post_title' => 'À propos']);
    $GLOBALS['glsr_wpml']['objects'][$english] = $french;
    $GLOBALS['glsr_wpml']['translations'][$english] = [$english, $french];
    $GLOBALS['glsr_wpml']['translations'][$french] = [$english, $french];

    return [$english, $french];
}

test('wpml is only used when the site has chosen it', function () {
    expect(wpml()->isActive())->toBeTrue()
        ->and(wpml()->isEnabled())->toBeTrue()
        ->and(wpml()->isSupported())->toBeTrue();

    glsr(OptionManager::class)->set('settings.general.multilingual', '');
    [$english] = wpmlTranslatedPages();
    expect(wpml()->isEnabled())->toBeFalse()
        ->and(wpml()->getPostId($english))->toBe($english)
        ->and(wpml()->getPostIdsForAllLanguages([$english]))->toBe([$english])
        ->and(wpml()->getTermId(123))->toBe(123)
        ->and(wpml()->getTermIdsForAllLanguages([123]))->toBe([123]);
});

test('an assigned page is shown in the visitor language', function () {
    [$english, $french] = wpmlTranslatedPages();

    expect(wpml()->getPostId($english))->toBe($french)
        ->and(wpml()->getPostIds([$english, $french]))->toBe([$french])
        ->and(wpml()->getPost($english)->ID)->toBe($french);
});

test('a post wpml does not manage is left exactly alone', function () {
    // a deleted post, and a post type the site never told WPML to translate
    expect(wpml()->getPostId(999999001))->toBe(999999001);

    $GLOBALS['glsr_wpml']['translated_post_types'] = [];
    [$english] = wpmlTranslatedPages();
    expect(wpml()->getPostId($english))->toBe($english);
});

test('a review assigned to one language of a page belongs to every language of it', function () {
    [$english, $french] = wpmlTranslatedPages();

    expect(wpml()->getPostIdsForAllLanguages([$english]))->toBe([$english, $french]);
    // a deleted post is skipped, an untranslated type passes through unexpanded
    $GLOBALS['glsr_wpml']['translated_post_types'] = [];
    expect(wpml()->getPostIdsForAllLanguages([$english, 999999001]))->toBe([$english]);
});

test('an assigned category is translated the same two ways', function () {
    $english = createTerm(['taxonomy' => glsr()->taxonomy]);
    $french = createTerm(['taxonomy' => glsr()->taxonomy]);
    $GLOBALS['glsr_wpml']['objects'][$english] = $french;
    $GLOBALS['glsr_wpml']['translations'][$english] = [$english, $french];

    expect(wpml()->getTermId($english))->toBe($french)
        ->and(wpml()->getTermIds([$english, $french]))->toBe([$french])
        ->and(wpml()->getTerm($english)->term_id)->toBe($french)
        ->and(wpml()->getTermIdsForAllLanguages([$english]))->toBe([$english, $french]);
});

test('a term wpml does not manage is left exactly alone', function () {
    $term = createTerm(['taxonomy' => glsr()->taxonomy]);

    // a term that is not there is neither translated nor invented
    expect(wpml()->getTermId(999999001))->toBe(999999001)
        ->and(wpml()->getTerm(999999001))->toBeNull();

    // the review taxonomy is not translated on this site
    $GLOBALS['glsr_wpml']['translated_taxonomies'] = [];
    expect(wpml()->getTermId($term))->toBe($term)
        ->and(wpml()->getTermIdsForAllLanguages([$term]))->toBe([$term]);
});

test('a thing wpml has never grouped expands to nothing rather than fataling', function () {
    // wpml_get_element_translations answers null for an unknown group; the bridge
    // must treat that as no translations, not iterate it.
    $orphanPost = createPost(['post_type' => 'page']);
    $orphanTerm = createTerm(['taxonomy' => glsr()->taxonomy]);

    expect(wpml()->getPostIdsForAllLanguages([$orphanPost]))->toBe([])
        ->and(wpml()->getTermIdsForAllLanguages([$orphanTerm]))->toBe([]);
});

test('a deleted term contributes nothing to the all-languages list', function () {
    $english = createTerm(['taxonomy' => glsr()->taxonomy]);
    $french = createTerm(['taxonomy' => glsr()->taxonomy]);
    $GLOBALS['glsr_wpml']['objects'][$english] = $french;
    $GLOBALS['glsr_wpml']['translations'][$english] = [$english, $french];

    expect(wpml()->getTermIdsForAllLanguages([$english, 999999001]))->toBe([$english, $french]);
});
