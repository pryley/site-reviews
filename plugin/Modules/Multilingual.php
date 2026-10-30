<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Contracts\MultilingualContract;

class Multilingual implements MultilingualContract
{
    protected ?MultilingualContract $integration = null;

    public function getPost(int $postId): ?\WP_Post
    {
        if ($this->integration) {
            return $this->integration->getPost($postId);
        }
        $post = get_post($postId);
        return $post instanceof \WP_Post ? $post : null;
    }

    public function getPostId(int $postId): int
    {
        return $this->integration?->getPostId($postId) ?? $postId;
    }

    public function getPostIds(array $postIds): array
    {
        return $this->integration?->getPostIds($postIds) ?? $postIds;
    }

    public function getPostIdsForAllLanguages(array $postIds): array
    {
        return $this->integration?->getPostIdsForAllLanguages($postIds) ?? $postIds;
    }

    public function getTerm(int $termId, string $taxonomy = ''): ?\WP_Term
    {
        if ($this->integration) {
            return $this->integration->getTerm($termId, $taxonomy);
        }
        $term = get_term($termId, '' !== $taxonomy ? $taxonomy : glsr()->taxonomy);
        return $term instanceof \WP_Term ? $term : null;
    }

    public function getTermId(int $termId, string $taxonomy = ''): int
    {
        return $this->integration?->getTermId($termId, $taxonomy) ?? $termId;
    }

    public function getTermIds(array $termIds, string $taxonomy = ''): array
    {
        return $this->integration?->getTermIds($termIds, $taxonomy) ?? $termIds;
    }

    public function getTermIdsForAllLanguages(array $termIds, string $taxonomy = ''): array
    {
        return $this->integration?->getTermIdsForAllLanguages($termIds, $taxonomy) ?? $termIds;
    }

    public function register(MultilingualContract $integration): void
    {
        $this->integration = $integration;
    }
}
