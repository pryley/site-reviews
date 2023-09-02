<?php

namespace GeminiLabs\SiteReviews\Integrations\WooRewards;

use GeminiLabs\SiteReviews\Compatibility;
use GeminiLabs\SiteReviews\Contracts\HooksContract;

class Hooks implements HooksContract
{
    /**
     * @var Controller
     */
    public $controller;

    public function __construct()
    {
        glsr()->singleton(Controller::class);
        $this->controller = glsr(Controller::class);
    }

    public function run(): void
    {
        if (!class_exists('\LWS_WooRewards') || !class_exists('\LWS\WOOREWARDS\Core\Trace')) {
            return;
        }
        add_action('lws_woorewards_abstracts_event_installed', function () {
            glsr(Compatibility::class)->removeHook('comment_post', 'trigger', '\LWS\WOOREWARDS\Events\ProductReview');
            glsr(Compatibility::class)->removeHook('comment_unapproved_to_approved', 'delayedApproval', '\LWS\WOOREWARDS\Events\ProductReview');
            glsr(Compatibility::class)->removeHook('wp_insert_comment', 'review', '\LWS\WOOREWARDS\Events\ProductReview');
            if ($this->controller->event) {
                add_action('site-reviews/review/approved', [$this->controller, 'onApprovedReview'], 20);
                add_action('site-reviews/review/created', [$this->controller, 'onCreatedReview'], 20);
            }
        });
    }
}
