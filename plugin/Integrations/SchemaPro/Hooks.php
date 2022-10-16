<?php

namespace GeminiLabs\SiteReviews\Integrations\SchemaPro;

use GeminiLabs\SiteReviews\Contracts\HooksContract;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

class Hooks implements HooksContract
{
    /**
     * @var Controller
     */
    public $controller;

    public function __construct()
    {
        $this->controller = glsr(Controller::class);
    }

    /**
     * @return void
     */
    public function run()
    {
        if (!defined('BSF_AIOSRS_PRO_CACHE_KEY') || !class_exists('BSF_AIOSRS_Pro_Helper')) {
            return;
        }
        if ('schema_pro' !== glsr_get_option('schema.integration.plugin')) {
            return;
        }
        $types = Arr::consolidate(glsr_get_option('schema.integration.types'));
        foreach ($types as $type) {
            $type = Str::snakeCase($type);
            add_filter('wp_schema_pro_schema_'.$type, [$this->controller, 'filterSchema']);
        }
        add_action('admin_head', [$this->controller, 'displaySettingNotice']);
        add_filter('site-reviews/settings/sanitize', [$this->controller, 'filterSettingsSanitize'], 10, 2);
        add_action('site-reviews/review/created', [$this->controller, 'onReviewCreated']);
        add_action('site-reviews/settings/updated', [$this->controller, 'onSettingsUpdated']);
    }
}
