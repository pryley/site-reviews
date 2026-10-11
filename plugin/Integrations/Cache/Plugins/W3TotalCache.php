<?php

namespace GeminiLabs\SiteReviews\Integrations\Cache\Plugins;

/**
 * @see https://wordpress.org/plugins/w3-total-cache/
 */
class W3TotalCache extends AbstractPlugin
{
    public function isActive(): bool
    {
        return function_exists('w3tc_flush_all')
            && function_exists('w3tc_flush_post');
    }

    public function purgeAll(): void
    {
        w3tc_flush_all();
    }

    public function purgePost(int $postId): void
    {
        w3tc_flush_post($postId);
    }
}
