<?php

namespace GeminiLabs\SiteReviews\Integrations\SchemaPro;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        if (!defined('BSF_AIOSRS_PRO_CACHE_KEY') || !class_exists('BSF_AIOSRS_Pro_Helper')) {
            return;
        }
        if ('schema_pro' !== $this->option('schema.integration.plugin')) {
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
}
