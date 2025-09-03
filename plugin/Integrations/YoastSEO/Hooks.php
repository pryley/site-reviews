<?php

namespace GeminiLabs\SiteReviews\Integrations\YoastSEO;

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
            ['filterSchema', 'wpseo_schema_graph'],
        ]);
    }

    protected function isEnabled(): bool
    {
        return 'yoast_seo' === $this->option('schema.integration.plugin');
    }

    protected function isInstalled(): bool
    {
        return defined('WPSEO_VERSION')
            && function_exists('wpseo_init')
            && class_exists('Yoast\WP\SEO\Main');
    }
}
