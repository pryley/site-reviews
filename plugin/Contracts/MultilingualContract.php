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
     */
    public function getTerm(int $termId): ?\WP_Term;

    /**
     * Get the translated Term ID for the current language.
     */
    public function getTermId(int $termId): int;

    /**
     * Get the translated Term IDs for the current language.
     */
    public function getTermIds(array $termIds): array;

    /**
     * Get the translated Term IDs for all languages.
     */
    public function getTermIdsForAllLanguages(array $termIds): array;

    /**
     * Check if the multilingual plugin is activated.
     */
    public function isActive(): bool;

    /**
     * Check if the multilingual plugin integration is enabled.
     */
    public function isEnabled(): bool;

    /**
     * Check if the multilingual plugin version is supported.
     */
    public function isSupported(): bool;
}
