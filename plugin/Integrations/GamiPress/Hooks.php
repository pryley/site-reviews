<?php

namespace GeminiLabs\SiteReviews\Integrations\GamiPress;

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
        if (glsr()->addon('site-reviews-gamipress') || !defined('GAMIPRESS_VER')) {
            return;
        }
        add_filter('gamipress_activity_trigger_label', [$this->controller, 'filterActivityTriggerLabel'], 10, 3);
        add_filter('gamipress_activity_triggers', [$this->controller, 'filterActivityTriggers']);
        add_filter('user_has_access_to_achievement', [$this->controller, 'filterUserHasAccessToAchievement'], 10, 4);
        add_action('site-reviews/review/created', [$this->controller, 'onReviewCreated'], 20);
    }
}
