<?php

namespace GeminiLabs\SiteReviews\Tests;

/**
 * Polylang, as far as Site Reviews is concerned: which post types and taxonomies are translated,
 * what language a thing is in, and what its translations are.
 *
 * A WORKING fake, not a signature-only stub — like Akismet's, because the plugin CALLS into
 * Polylang and reads the answers, and a stub returning null proves only that the call compiles.
 * The semantics are Polylang's own:
 *
 *   pll_get_post($id, $lang = '')  id of $id's translation in $lang; $lang DEFAULTS to the current
 *                                  language — the point of the function, and what Site Reviews
 *                                  does not use.
 *   pll_get_post_language($id)     the language $id is in (not the current one).
 *   pll_get_post_translations($id) every translation of $id, keyed by language.
 *
 * Nothing about the current language is invented: set it, and the fake answers accordingly.
 */
class PolylangFake
{
    /** @var string The language the visitor is looking at. */
    public static string $currentLanguage = 'en';

    /** @var array<int, string> post id => language */
    public static array $postLanguages = [];

    /** @var array<int, array<string, int>> post id => [language => post id] */
    public static array $postTranslations = [];

    /** @var array<int, string> term id => language */
    public static array $termLanguages = [];

    /** @var array<int, array<string, int>> term id => [language => term id] */
    public static array $termTranslations = [];

    /** @var string[] */
    public static array $translatedPostTypes = [];

    /** @var string[] */
    public static array $translatedTaxonomies = [];

    /**
     * Declares a set of posts to be translations of one another.
     *
     * @param array<string, int> $byLanguage ['en' => 12, 'fr' => 34]
     */
    public static function linkPosts(array $byLanguage): void
    {
        foreach ($byLanguage as $language => $postId) {
            static::$postLanguages[$postId] = $language;
            static::$postTranslations[$postId] = $byLanguage;
        }
    }

    /**
     * @param array<string, int> $byLanguage
     */
    public static function linkTerms(array $byLanguage): void
    {
        foreach ($byLanguage as $language => $termId) {
            static::$termLanguages[$termId] = $language;
            static::$termTranslations[$termId] = $byLanguage;
        }
    }

    public static function reset(): void
    {
        static::$currentLanguage = 'en';
        static::$postLanguages = [];
        static::$postTranslations = [];
        static::$termLanguages = [];
        static::$termTranslations = [];
        static::$translatedPostTypes = [];
        static::$translatedTaxonomies = [];
    }

    /**
     * @return int|false
     */
    public static function translatedPost(int $postId, string $language)
    {
        $language = $language ?: static::$currentLanguage;

        return static::$postTranslations[$postId][$language] ?? false;
    }

    /**
     * @return int|false
     */
    public static function translatedTerm(int $termId, string $language)
    {
        $language = $language ?: static::$currentLanguage;

        return static::$termTranslations[$termId][$language] ?? false;
    }
}
