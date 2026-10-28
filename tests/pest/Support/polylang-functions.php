<?php

/*
 * Polylang, as a WORKING fake rather than a signature-only stub — because Site Reviews does not
 * merely check that Polylang is there, it calls into it and reads the answers back. A stub
 * returning null proves only that the call compiles.
 *
 * Every function below delegates to GeminiLabs\SiteReviews\Tests\PolylangFake, which holds the
 * state a test sets up: which post types are translated, what language a thing is in, and what
 * its translations are. The semantics are Polylang's own, from its documented API — in
 * particular that `$lang` DEFAULTS TO THE CURRENT LANGUAGE, which is the entire purpose of
 * pll_get_post() and pll_get_term(), and which Site Reviews does not use.
 */

namespace {
    use GeminiLabs\SiteReviews\Tests\PolylangFake;

    /**
     * Returns true if Polylang manages languages and translations for this post type.
     *
     * @param string $post_type Post type name.
     *
     * @return bool
     */
    function pll_is_translated_post_type($post_type)
    {
        return in_array($post_type, PolylangFake::$translatedPostTypes);
    }

    /**
     * Returns true if Polylang manages languages and translations for this taxonomy.
     *
     * @param string $tax Taxonomy name.
     *
     * @return bool
     */
    function pll_is_translated_taxonomy($tax)
    {
        return in_array($tax, PolylangFake::$translatedTaxonomies);
    }

    /**
     * Among the post and its translations, returns the id of the post which is in the language
     * represented by $lang.
     *
     * @param int    $post_id Post id.
     * @param string $lang    Optional language code, DEFAULTS TO THE CURRENT LANGUAGE.
     *
     * @return int|false|null Post id of the translation if it exists, false otherwise.
     */
    function pll_get_post($post_id, $lang = '')
    {
        return PolylangFake::translatedPost((int) $post_id, (string) $lang);
    }

    /**
     * Among the term and its translations, returns the ID of the term which is in the language
     * represented by $lang.
     *
     * @param int    $term_id Term ID.
     * @param string $lang    Optional language, defaults to the current language.
     *
     * @return int|false
     */
    function pll_get_term($term_id, $lang = null)
    {
        return PolylangFake::translatedTerm((int) $term_id, (string) $lang);
    }

    /**
     * Returns the post language. NOT the current language — the language the post is in.
     *
     * @param int    $post_id Post id.
     * @param string $field   Optional, the language field to return, defaults to 'slug'.
     *
     * @return string|false
     */
    function pll_get_post_language($post_id, $field = 'slug')
    {
        return PolylangFake::$postLanguages[(int) $post_id] ?? false;
    }

    /**
     * Returns the term language. NOT the current language.
     *
     * @param int    $term_id Term ID.
     * @param string $field   Optional, the language field to return, defaults to 'slug'.
     *
     * @return string|int|bool|string[]
     */
    function pll_get_term_language($term_id, $field = 'slug')
    {
        return PolylangFake::$termLanguages[(int) $term_id] ?? false;
    }

    /**
     * Returns an array of translations of a post.
     *
     * @param int $post_id Post id.
     *
     * @return int[] An associative array of translations with language code as key.
     */
    function pll_get_post_translations($post_id)
    {
        return PolylangFake::$postTranslations[(int) $post_id] ?? [];
    }

    /**
     * Returns an array of translations of a term.
     *
     * @param int $term_id Term ID.
     *
     * @return int[] An associative array of translations with language code as key.
     */
    function pll_get_term_translations($term_id)
    {
        return PolylangFake::$termTranslations[(int) $term_id] ?? [];
    }

    /**
     * The language the visitor is looking at.
     *
     * @param string $field
     *
     * @return string
     */
    function pll_current_language($field = 'slug')
    {
        return PolylangFake::$currentLanguage;
    }
}
namespace {
    \define('POLYLANG_VERSION', '2.3');
}
