<?php

namespace GeminiLabs\SiteReviews\Integrations\LPFW;

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
        if (!function_exists('LPFW') || !class_exists('LPFW')) {
            return;
        }
        if ('yes' !== get_option(\LPFW()->Plugin_Constants->EARN_ACTION_PRODUCT_REVIEW, 'yes')) {
            return;
        }
        add_action('site-reviews/review/approved', [$this->controller, 'maybeEarnPoints']);
        add_action('site-reviews/review/created', [$this->controller, 'maybeEarnPoints']);
    }
}
