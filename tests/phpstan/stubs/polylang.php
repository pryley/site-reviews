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
     * Allows to access the Polylang instance.
     * However, it is always preferable to use API functions
     * as internal methods may be changed without prior notice.
     *
     * @since 1.8
     *
     * @return PLL_Frontend|PLL_Admin|PLL_Settings|PLL_REST_Request
     */
    function PLL()
    {}
}
