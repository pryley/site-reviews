<?php

namespace GeminiLabs\SiteReviews\Integrations\Cache\Plugins;

/**
 * @see https://wordpress.org/plugins/cloudflare/
 * @see https://developers.cloudflare.com/automatic-platform-optimization/
 */
class Cloudflare extends AbstractPlugin
{
    public function isActive(): bool
    {
        return class_exists('CF\WordPress\Hooks');
    }

    public function purge(array $postIds): void
    {
        if (empty($postIds)) {
            $this->purgeAll();
            return;
        }
        do_action('site-reviews/cloudflare/purge', $postIds);
    }

    public function purgeAll(): void
    {
        do_action('site-reviews/cloudflare/purge_all');
    }

    public function purgePost(int $postId): void
    {
        do_action('site-reviews/cloudflare/purge', [$postId]);
    }
}
