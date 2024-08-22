<?php

namespace GeminiLabs\SiteReviews\Modules\Multilingual;

use GeminiLabs\SiteReviews\Contracts\MultilingualContract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Polylang implements MultilingualContract
{
    public $pluginName = 'Polylang';
    public $supportedVersion = '2.3';

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
        if (!pll_is_translated_post_type($postType)) {
            return $postId;
        }
        $polylangPostId = pll_get_post($postId, pll_get_post_language($postId));
        if (!empty($polylangPostId)) {
            $postId = $polylangPostId;
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
            if (!pll_is_translated_post_type($postType)) {
                $newPostIds[] = $postId;
                continue;
            }
            $newPostIds = array_merge($newPostIds,
                array_values(pll_get_post_translations($postId))
            );
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
        if (!pll_is_translated_taxonomy(glsr()->taxonomy)) {
            return $termId;
        }
        $term = get_term($termId, glsr()->taxonomy);
        if (is_a($term, '\WP_Term')) {
            $polylangTermId = pll_get_term($termId, pll_get_term_language($termId));
        }
        if (!empty($polylangTermId)) {
            $termId = $polylangTermId;
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
        if (!pll_is_translated_taxonomy(glsr()->taxonomy)) {
            return $termIds;
        }
        $newTermIds = [];
        foreach (Arr::uniqueInt($termIds) as $termId) {
            $term = get_term($termId, glsr()->taxonomy);
            if (!is_a($term, '\WP_Term')) {
                continue;
            }
            $newTermIds = array_merge($newTermIds,
                array_values(pll_get_term_translations($termId))
            );
        }
        return Arr::uniqueInt($newTermIds);
    }

    public function isActive(): bool
    {
        return defined('POLYLANG_VERSION');
    }

    public function isEnabled(): bool
    {
        return $this->isActive()
            && 'polylang' === glsr(OptionManager::class)->get('settings.general.multilingual');
    }

    public function isSupported(): bool
    {
        return $this->isActive()
            && Helper::isGreaterThanOrEqual(POLYLANG_VERSION, $this->supportedVersion);
    }
}
