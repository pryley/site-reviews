<?php

namespace GeminiLabs\SiteReviews\Integrations\SchemaPro;

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
            ['filterSchema', 'wp_schema_pro_schema_article'],
            ['filterSchema', 'wp_schema_pro_schema_book'],
            ['filterSchema', 'wp_schema_pro_schema_course'],
            ['filterSchema', 'wp_schema_pro_schema_event'],
            ['filterSchema', 'wp_schema_pro_schema_faq'],
            ['filterSchema', 'wp_schema_pro_schema_how_to'],
            ['filterSchema', 'wp_schema_pro_schema_image_license'],
            ['filterSchema', 'wp_schema_pro_schema_local_business'],
            ['filterSchema', 'wp_schema_pro_schema_product'],
            ['filterSchema', 'wp_schema_pro_schema_recipe'],
            ['filterSchema', 'wp_schema_pro_schema_service'],
            ['filterSchema', 'wp_schema_pro_schema_software_application'],
            ['filterSchema', 'wp_schema_pro_schema_video_object'],
            ['onReviewCreated', 'site-reviews/review/created'],
            ['onSettingsUpdated', 'site-reviews/settings/updated'],
        ]);
    }

    protected function isEnabled(): bool
    {
        return 'schema_pro' === $this->option('schema.integration.plugin');
    }

    protected function isInstalled(): bool
    {
        return class_exists('BSF_AIOSRS_Pro_Helper')
            && defined('BSF_AIOSRS_PRO_CACHE_KEY');
    }
}
