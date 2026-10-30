<?php

namespace GeminiLabs\SiteReviews\Contracts;

interface MultilingualContract
{
    /**
     * Get the translated Post for the current language.
     */
    public function getPost(int $postId): ?\WP_Post;

    /**
     * Get the translated Post ID for the current language.
     */
    public function getPostId(int $postId): int;

    /**
     * Get the translated Post IDs for the current language.
     */
    public function getPostIds(array $postIds): array;

    /**
     * Get the translated Post IDs for all languages.
     */
    public function getPostIdsForAllLanguages(array $postIds): array;

    /**
     * Get the translated Term for the current language.
     * The taxonomy defaults to the review category taxonomy.
     */
    public function getTerm(int $termId, string $taxonomy = ''): ?\WP_Term;

    /**
     * Get the translated Term ID for the current language.
     * The taxonomy defaults to the review category taxonomy.
     */
    public function getTermId(int $termId, string $taxonomy = ''): int;

    /**
     * Get the translated Term IDs for the current language.
     * The taxonomy defaults to the review category taxonomy.
     */
    public function getTermIds(array $termIds, string $taxonomy = ''): array;

    /**
     * Get the translated Term IDs for all languages.
     * The taxonomy defaults to the review category taxonomy.
     */
    public function getTermIdsForAllLanguages(array $termIds, string $taxonomy = ''): array;
}
