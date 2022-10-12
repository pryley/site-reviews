<?php

namespace GeminiLabs\SiteReviews\Integrations\SASWP;

use GeminiLabs\SiteReviews\Contracts\HooksContract;

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
        if (!defined('SASWP_VERSION')) {
            return;
        }
        if ('saswp' !== glsr_get_option('schema.integration.plugin')) {
            return;
        }
        add_action('admin_head', [$this->controller, 'displaySettingNotice']);
        add_filter('site-reviews/settings/sanitize', [$this->controller, 'filterSettingsSanitize'], 10, 2);
        add_filter('saswp_modify_schema_output', [$this->controller, 'filterSchema'], 20);
    }
}
