<?php

namespace GeminiLabs\SiteReviews\Integrations\Polylang;

use GeminiLabs\SiteReviews\Contracts\MultilingualContract;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Translations implements MultilingualContract
{
    /**
     * Get the translated Post for the current language.
     */
    public function getPost(int $postId): ?\WP_Post
    {
        return get_post($this->getPostId($postId), \OBJECT);
    }

    /**
     * Get the translated Post ID for the current language.
     */
    public function getPostId(int $postId): int
    {
        $postType = get_post_type($postId);
        if (empty($postType)) {
            return $postId;
        }
        if (!pll_is_translated_post_type($postType)) {
            return $postId;
        }
        $polylangPostId = pll_get_post($postId);
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
    public function getTerm(int $termId, string $taxonomy = ''): ?\WP_Term
    {
        $taxonomy = $this->taxonomy($taxonomy);
        $term = get_term($this->getTermId($termId, $taxonomy), $taxonomy, \OBJECT);
        if (!is_a($term, '\WP_Term')) {
            return null;
        }
        return $term;
    }

    /**
     * Get the translated Term ID for the current language.
     */
    public function getTermId(int $termId, string $taxonomy = ''): int
    {
        $taxonomy = $this->taxonomy($taxonomy);
        if (!pll_is_translated_taxonomy($taxonomy)) {
            return $termId;
        }
        $term = get_term($termId, $taxonomy);
        if (is_a($term, '\WP_Term')) {
            $polylangTermId = pll_get_term($termId);
        }
        if (!empty($polylangTermId)) {
            $termId = $polylangTermId;
        }
        return intval($termId);
    }

    /**
     * Get the translated Term IDs for the current language.
     */
    public function getTermIds(array $termIds, string $taxonomy = ''): array
    {
        $newTermIds = [];
        foreach (Arr::uniqueInt($termIds) as $termId) {
            $newTermIds[] = $this->getTermId($termId, $taxonomy);
        }
        return Arr::uniqueInt($newTermIds);
    }

    /**
     * Get the translated Term IDs for all languages.
     */
    public function getTermIdsForAllLanguages(array $termIds, string $taxonomy = ''): array
    {
        $taxonomy = $this->taxonomy($taxonomy);
        if (!pll_is_translated_taxonomy($taxonomy)) {
            return $termIds;
        }
        $newTermIds = [];
        foreach (Arr::uniqueInt($termIds) as $termId) {
            $term = get_term($termId, $taxonomy);
            if (!is_a($term, '\WP_Term')) {
                continue;
            }
            $newTermIds = array_merge($newTermIds,
                array_values(pll_get_term_translations($termId))
            );
        }
        return Arr::uniqueInt($newTermIds);
    }

    protected function taxonomy(string $taxonomy): string
    {
        return '' !== $taxonomy ? $taxonomy : glsr()->taxonomy;
    }
}
