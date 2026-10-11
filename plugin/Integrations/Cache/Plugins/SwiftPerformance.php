<?php

namespace GeminiLabs\SiteReviews\Integrations\Cache\Plugins;

/**
 * @see https://swiftperformance.io/
 */
class SwiftPerformance extends AbstractPlugin
{
    public function isActive(): bool
    {
        return class_exists('Swift_Performance_Cache');
    }

    public function purge(array $postIds): void
    {
        if (empty($postIds)) {
            $this->purgeAll();
            return;
        }
        \Swift_Performance_Cache::clear_post_cache_array($postIds);
    }

    public function purgeAll(): void
    {
        \Swift_Performance_Cache::clear_all_cache();
    }

    public function purgePost(int $postId): void
    {
        \Swift_Performance_Cache::clear_post_cache_array([$postId]);
    }
}
