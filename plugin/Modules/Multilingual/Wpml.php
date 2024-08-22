<?php

namespace GeminiLabs\SiteReviews\Modules\Multilingual;

use GeminiLabs\SiteReviews\Contracts\MultilingualContract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Wpml implements MultilingualContract
{
    public $pluginName = 'WPML';
    public $supportedVersion = '3.3.5';

    /**
     * Get the translated Post for the current language.
     */
    public function getPost(int $postId): ?\WP_Post
    {
        return get_post($this->getPostId($postId), OBJECT);
    }

    /**
     * Get the translated Post ID for the current language.
     */
    public function getPostId(int $postId): int
    {
        if (!$this->isEnabled()) {
            return $postId;
        }
        $postType = get_post_type($postId);
        if (empty($postType)) {
            return $postId;
        }
        if (!apply_filters('wpml_is_translated_post_type', false, $postType)) {
            return $postId;
        }
        $wpmlPostId = apply_filters('wpml_object_id', $postId, $postType, true);
        if (!empty($wpmlPostId)) {
            $postId = $wpmlPostId;
        }
        return intval($postId);
    }

    /**
     * Get the translated Post IDs for the current language.
     */
    public function getPostIds(array $postIds): array
    {
        $newPostIds = [];
        foreach (Arr::uniqueInt($postIds) as $postId) {
            $newPostIds[] = $this->getPostId($postId);
        }
        return Arr::uniqueInt($newPostIds);
    }

    /**
     * Get the translated Post IDs for all languages.
     */
    public function getPostIdsForAllLanguages(array $postIds): array
    {
        if (!$this->isEnabled()) {
            return $postIds;
        }
        $newPostIds = [];
        foreach (Arr::uniqueInt($postIds) as $postId) {
            $postType = get_post_type($postId);
            if (!$postType) {
                continue;
            }
            if (!apply_filters('wpml_is_translated_post_type', false, $postType)) {
                $newPostIds[] = $postId;
                continue;
            }
            $elementType = "post_{$postType}";
            $trid = apply_filters('wpml_element_trid', null, $postId, $elementType);
            $translations = apply_filters('wpml_get_element_translations', null, $trid, $elementType);
            if (!is_array($translations)) {
                $translations = [];
            }
            $translatedPostIds = array_values(wp_list_pluck($translations, 'element_id'));
            $newPostIds = array_merge($newPostIds, $translatedPostIds);
        }
        return Arr::uniqueInt($newPostIds);
    }

    /**
     * Get the translated Term for the current language.
     */
    public function getTerm(int $termId): ?\WP_Term
    {
        $term = get_term($this->getTermId($termId), glsr()->taxonomy, OBJECT);
        if (!is_a($term, '\WP_Term')) {
            return null;
        }
        return $term;
    }

    /**
     * Get the translated Term ID for the current language.
     */
    public function getTermId(int $termId): int
    {
        if (!$this->isEnabled()) {
            return $termId;
        }
        if (!apply_filters('wpml_is_translated_taxonomy', false, glsr()->taxonomy)) {
            return $termId;
        }
        $term = get_term($termId, glsr()->taxonomy);
        if (is_a($term, '\WP_Term')) {
            $wpmlTermId = apply_filters('wpml_object_id', $termId, glsr()->taxonomy, true);
        }
        if (!empty($wpmlTermId)) {
            $termId = $wpmlTermId;
        }
        return intval($termId);
    }

    /**
     * Get the translated Term IDs for the current language.
     */
    public function getTermIds(array $termIds): array
    {
        $newTermIds = [];
        foreach (Arr::uniqueInt($termIds) as $termId) {
            $newTermIds[] = $this->getTermId($termId);
        }
        return Arr::uniqueInt($newTermIds);
    }

    /**
     * Get the translated Term IDs for all languages.
     */
    public function getTermIdsForAllLanguages(array $termIds): array
    {
        if (!$this->isEnabled()) {
            return $termIds;
        }
        if (!apply_filters('wpml_is_translated_taxonomy', false, glsr()->taxonomy)) {
            return $termIds;
        }
        $newTermIds = [];
        foreach (Arr::uniqueInt($termIds) as $termId) {
            $term = get_term($termId, glsr()->taxonomy);
            if (!is_a($term, '\WP_Term')) {
                continue;
            }
            $elementType = 'tax_'.glsr()->taxonomy;
            $trid = apply_filters('wpml_element_trid', null, $termId, $elementType);
            $translations = apply_filters('wpml_get_element_translations', null, $trid, $elementType);
            if (!is_array($translations)) {
                $translations = [];
            }
            $translatedTermIds = array_values(wp_list_pluck($translations, 'element_id'));
            $newTermIds = array_merge($newTermIds, $translatedTermIds);
        }
        return Arr::uniqueInt($newTermIds);
    }

    public function isActive(): bool
    {
        return defined('ICL_SITEPRESS_VERSION');
    }

    public function isEnabled(): bool
    {
        return $this->isActive()
            && 'wpml' === glsr(OptionManager::class)->get('settings.general.multilingual');
    }

    public function isSupported(): bool
    {
        return $this->isActive()
            && Helper::isGreaterThanOrEqual(ICL_SITEPRESS_VERSION, $this->supportedVersion);
    }
}
