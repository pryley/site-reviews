<?php

namespace GeminiLabs\SiteReviews\Integrations\Cache\Plugins;

/**
 * @see https://docs.wp-rocket.me/article/93-rocketcleanpost
 */
class WpRocket extends AbstractPlugin
{
    public function isActive(): bool
    {
        return function_exists('rocket_clean_home')
            && function_exists('rocket_clean_post');
    }

    public function purgeAll(): void
    {
        rocket_clean_home();
    }

    public function purgePost(int $postId): void
    {
        rocket_clean_post($postId);
    }
}
