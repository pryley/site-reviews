<?php

namespace GeminiLabs\SiteReviews\Integrations\Cache\Plugins;

/**
 * @see https://github.com/bluehost/endurance-page-cache/
 */
class Endurance extends AbstractPlugin
{
    /**
     * The plugin can only purge everything.
     */
    public function purge(array $postIds): void
    {
        $this->purgeAll();
    }

    public function purgeAll(): void
    {
        do_action('epc_purge');
    }

    public function purgePost(int $postId): void
    {
        $this->purgeAll();
    }
}
