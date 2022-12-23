<?php

namespace GeminiLabs\SiteReviews\Integrations\SchemaPro;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        if (!defined('BSF_AIOSRS_PRO_CACHE_KEY') || !class_exists('BSF_AIOSRS_Pro_Helper')) {
            return;
        }
        if ('schema_pro' !== glsr_get_option('schema.integration.plugin')) {
            return;
        }
        $hooks = [
            ['displaySettingNotice', 'admin_head'],
            ['filterSettingsSanitize', 'site-reviews/settings/sanitize', 10, 2],
            ['onReviewCreated', 'site-reviews/review/created'],
            ['onSettingsUpdated', 'site-reviews/settings/updated'],
        ];
        $types = Arr::consolidate(glsr_get_option('schema.integration.types'));
        foreach ($types as $type) {
            $type = Str::snakeCase($type);
            $hooks[] = ['filterSchema', 'wp_schema_pro_schema_'.$type];
        }
        $this->hook(Controller::class, $hooks);
    }
}
