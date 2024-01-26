<?php

namespace {
    /**
     * Among the post and its translations, returns the id of the post which is in the language represented by $lang.
     *
     * @api
     * @since 0.5
     *
     * @param int    $post_id Post id.
     * @param string $lang    Optional language code, defaults to the current language.
     * @return int|false|null Post id of the translation if it exists, false otherwise, null if the current language is not defined yet.
     */
    function pll_get_post($post_id, $lang = '')
    {}
    /**
     * Among the term and its translations, returns the ID of the term which is in the language represented by $lang.
     *
     * @api
     * @since 0.5
     * @since 3.4 Returns 0 instead of false.
     * @since 3.4 $lang accepts PLL_Language or string.
     *
     * @param int                 $term_id Term ID.
     * @param PLL_Language|string $lang    Optional language (object or slug), defaults to the current language.
     * @return int|false The translation term ID if exists, otherwise the passed ID. False if the passed object has no language or if the language doesn't exist.
     */
    function pll_get_term($term_id, $lang = null)
    {}
    /**
     * Returns the post language.
     *
     * @api
     * @since 1.5.4
     *
     * @param int    $post_id Post id.
     * @param string $field   Optional, the language field to return ( @see PLL_Language ), defaults to 'slug'.
     * @return string|false The requested field for the post language, false if no language is associated to that post.
     */
    function pll_get_post_language($post_id, $field = 'slug')
    {}
    /**
     * Returns the term language.
     *
     * @api
     * @since 1.5.4
     * @since 3.4 Accepts composite values for `$field`.
     *
     * @param int    $term_id Term ID.
     * @param string $field Optional, the language field to return (@see PLL_Language), defaults to `'slug'`.
     *                      Pass `\OBJECT` constant to get the language object. A composite value can be used for language
     *                      term property values, in the form of `{language_taxonomy_name}:{property_name}` (see
     *                      {@see PLL_Language::get_tax_prop()} for the possible values). Ex: `term_language:term_taxonomy_id`.
     * @return string|int|bool|string[]|PLL_Language The requested field or object for the post language, `false` if no language is associated to that term.
     */
    function pll_get_term_language($term_id, $field = 'slug')
    {}
    /**
     * Returns an array of translations of a post.
     *
     * @api
     * @since 1.8
     *
     * @param int $post_id Post id.
     * @return int[] An associative array of translations with language code as key and translation post id as value.
     */
    function pll_get_post_translations($post_id)
    {}
    /**
     * Returns an array of translations of a term.
     *
     * @api
     * @since 1.8
     *
     * @param int $term_id Term ID.
     * @return int[] An associative array of translations with language code as key and translation term ID as value.
     */
    function pll_get_term_translations($term_id)
    {}
}
