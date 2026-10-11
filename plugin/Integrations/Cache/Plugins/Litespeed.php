<?php

namespace GeminiLabs\SiteReviews\Integrations\Cache\Plugins;

/**
 * @see https://wordpress.org/plugins/litespeed-cache/
 */
class Litespeed extends AbstractPlugin
{
    public function purgeAll(): void
    {
        do_action('litespeed_purge_all');
    }

    public function purgePost(int $postId): void
    {
        do_action('litespeed_purge_post', $postId);
    }
}
