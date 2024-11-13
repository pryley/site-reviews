<?php

namespace GeminiLabs\SiteReviews\Integrations\SASWP;

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;

class Hooks extends IntegrationHooks
{
    public function run(): void
    {
        if (!$this->isInstalled()) {
            return;
        }
        if (!$this->isEnabled()) {
            return;
        }
        $this->hook(Controller::class, [
            ['filterSchema', 'saswp_modify_reviews_schema', 20],
        ]);
    }

    protected function isEnabled(): bool
    {
        return 'saswp' === $this->option('schema.integration.plugin');
    }

    protected function isInstalled(): bool
    {
        return defined('SASWP_VERSION');
    }
}
