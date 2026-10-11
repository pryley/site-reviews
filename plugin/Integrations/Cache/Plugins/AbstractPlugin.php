<?php

namespace GeminiLabs\SiteReviews\Integrations\Cache\Plugins;

abstract class AbstractPlugin
{
    public function isActive(): bool
    {
        return true;
    }

    /**
     * No assigned posts means the whole cache.
     */
    public function purge(array $postIds): void
    {
        if (empty($postIds)) {
            $this->purgeAll();
            return;
        }
        foreach ($postIds as $postId) {
            $this->purgePost($postId);
        }
    }

    abstract public function purgeAll(): void;

    abstract public function purgePost(int $postId): void;
}
