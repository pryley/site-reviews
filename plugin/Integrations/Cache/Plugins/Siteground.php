<?php

namespace GeminiLabs\SiteReviews\Integrations\Cache\Plugins;

/**
 * @see https://wordpress.org/plugins/sg-cachepress/
 */
class Siteground extends AbstractPlugin
{
    public function isActive(): bool
    {
        return function_exists('sg_cachepress_purge_everything')
            && function_exists('sg_cachepress_purge_cache');
    }

    public function purgeAll(): void
    {
        sg_cachepress_purge_everything();
    }

    public function purgePost(int $postId): void
    {
        sg_cachepress_purge_cache(get_permalink($postId));
    }
}
