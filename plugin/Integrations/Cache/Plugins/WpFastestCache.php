<?php

namespace GeminiLabs\SiteReviews\Integrations\Cache\Plugins;

/**
 * @see https://www.wpfastestcache.com/
 */
class WpFastestCache extends AbstractPlugin
{
    public function purgeAll(): void
    {
        do_action('wpfc_clear_all_cache');
    }

    public function purgePost(int $postId): void
    {
        do_action('wpfc_clear_post_cache_by_id', false, $postId);
    }
}
