<?php

namespace GeminiLabs\SiteReviews\Integrations\WooRewards;

use GeminiLabs\SiteReviews\Compatibility;
use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        if (!class_exists('\LWS_WooRewards') || !class_exists('\LWS\WOOREWARDS\Core\Trace')) {
            return;
        }
        add_action('lws_woorewards_abstracts_event_installed', function () {
            $callback = glsr(Compatibility::class)->findCallback('comment_post', 'trigger', '\LWS\WOOREWARDS\Events\ProductReview');
            if ($callback) {
                glsr()->store('\LWS\WOOREWARDS\Events\ProductReview', $callback);
                glsr(Compatibility::class)->removeHook('comment_post', 'trigger', '\LWS\WOOREWARDS\Events\ProductReview');
                glsr(Compatibility::class)->removeHook('comment_unapproved_to_approved', 'delayedApproval', '\LWS\WOOREWARDS\Events\ProductReview');
                glsr(Compatibility::class)->removeHook('wp_insert_comment', 'review', '\LWS\WOOREWARDS\Events\ProductReview');
                add_action('site-reviews/review/approved', [$this->controller, 'onApprovedReview'], 20);
                add_action('site-reviews/review/created', [$this->controller, 'onCreatedReview'], 20);
            }
        });
    }
}
