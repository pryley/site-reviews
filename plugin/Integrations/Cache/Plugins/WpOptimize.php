<?php

namespace GeminiLabs\SiteReviews\Integrations\Cache\Plugins;

/**
 * @see https://getwpo.com/documentation/#Purging-the-cache-from-an-other-plugin-or-theme
 */
class WpOptimize extends AbstractPlugin
{
    public function isActive(): bool
    {
        return function_exists('WP_Optimize')
            && class_exists('WPO_Page_Cache');
    }

    public function purgeAll(): void
    {
        $optimize = WP_Optimize();
        if (!is_object($optimize)) {
            return;
        }
        $optimize->get_page_cache()->purge();
    }

    public function purgePost(int $postId): void
    {
        \WPO_Page_Cache::delete_single_post_cache($postId);
    }
}
