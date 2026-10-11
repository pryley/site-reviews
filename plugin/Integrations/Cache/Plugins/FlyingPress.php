<?php

namespace GeminiLabs\SiteReviews\Integrations\Cache\Plugins;

/**
 * @see https://flyingpress.com/
 *
 * FlyingPress is closed source, so there is no stub for phpstan; isActive() is the guard.
 */
class FlyingPress extends AbstractPlugin
{
    public function isActive(): bool
    {
        return is_callable(['FlyingPress\Purge', 'purge_pages'])
            && is_callable(['FlyingPress\Purge', 'purge_url']);
    }

    public function purgeAll(): void
    {
        \FlyingPress\Purge::purge_pages(); // @phpstan-ignore class.notFound
    }

    public function purgePost(int $postId): void
    {
        \FlyingPress\Purge::purge_url(get_permalink($postId)); // @phpstan-ignore class.notFound
    }
}
