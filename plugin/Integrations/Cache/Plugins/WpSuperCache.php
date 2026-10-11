<?php

namespace GeminiLabs\SiteReviews\Integrations\Cache\Plugins;

/**
 * @see https://wordpress.org/plugins/wp-super-cache/
 */
class WpSuperCache extends AbstractPlugin
{
    public function isActive(): bool
    {
        return function_exists('wp_cache_clear_cache')
            && function_exists('wp_cache_post_change');
    }

    public function purgeAll(): void
    {
        wp_cache_clear_cache();
    }

    public function purgePost(int $postId): void
    {
        wp_cache_post_change($postId);
    }
}
