<?php

namespace GeminiLabs\SiteReviews\Integrations\CloudFlare;

use GeminiLabs\SiteReviews\Controllers\AbstractController;

class Controller extends AbstractController
{
    /**
     * @filter cloudflare_purge_url_actions
     */
    public function filterPurgeActions(array $actions): array
    {
        $actions[] = 'site-reviews/cloudflare/purge';
        return $actions;
    }

    /**
     * @filter cloudflare_purge_everything_actions
     */
    public function filterPurgeEverythingActions(array $actions): array
    {
        $actions[] = 'site-reviews/cloudflare/purge_all';
        return $actions;
    }
}
