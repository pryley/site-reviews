<?php

namespace GeminiLabs\SiteReviews\Integrations\CloudFlare;

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;

class Hooks extends IntegrationHooks
{
    public function run(): void
    {
        $this->hook(Controller::class, [
            ['filterPurgeActions', 'cloudflare_purge_url_actions'],
            ['filterPurgeEverythingActions', 'cloudflare_purge_everything_actions'],
        ]);
    }
}
