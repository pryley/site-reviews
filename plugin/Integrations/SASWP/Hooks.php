<?php

namespace GeminiLabs\SiteReviews\Integrations\SASWP;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        if (!defined('SASWP_VERSION')) {
            return;
        }
        if ('saswp' !== $this->option('schema.integration.plugin')) {
            return;
        }
        $this->hook(Controller::class, [
            ['filterSchema', 'saswp_modify_reviews_schema', 20],
        ]);
    }
}
