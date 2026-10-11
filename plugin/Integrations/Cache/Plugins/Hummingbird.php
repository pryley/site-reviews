<?php

namespace GeminiLabs\SiteReviews\Integrations\Cache\Plugins;

/**
 * @see https://premium.wpmudev.org/docs/api-plugin-development/hummingbird-api-docs/#action-wphb_clear_page_cache
 */
class Hummingbird extends AbstractPlugin
{
    public function purgeAll(): void
    {
        do_action('wphb_clear_page_cache');
    }

    public function purgePost(int $postId): void
    {
        do_action('wphb_clear_page_cache', $postId);
    }
}
