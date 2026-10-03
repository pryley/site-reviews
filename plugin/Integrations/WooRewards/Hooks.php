<?php

namespace GeminiLabs\SiteReviews\Integrations\WooRewards;

use GeminiLabs\SiteReviews\Helpers\Hook;
use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;

class Hooks extends IntegrationHooks
{
    public function run(): void
    {
        if (!$this->isInstalled()) {
            return;
        }
        add_action('lws_woorewards_abstracts_event_installed', function () {
            $callback = Hook::find('wp_insert_comment', 'review', '\LWS\WOOREWARDS\Events\ProductReview');
            if (!isset($callback['function'][0])) {
                return;
            }
            glsr()->store('\LWS\WOOREWARDS\Events\ProductReview', $callback['function'][0]);
            Hook::remove('comment_post', 'trigger', '\LWS\WOOREWARDS\Events\ProductReview'); // this is commented out in WooRewards, but it's here just in case
            Hook::remove('comment_unapproved_to_approved', 'delayedApproval', '\LWS\WOOREWARDS\Events\ProductReview');
            Hook::remove('wp_insert_comment', 'review', '\LWS\WOOREWARDS\Events\ProductReview');
            $this->hook(Controller::class, [
                ['onApprovedReview', 'site-reviews/review/approved', 20],
                ['onCreatedReview', 'site-reviews/review/created', 20],
            ]);
        });
    }

    protected function isInstalled(): bool
    {
        return class_exists('\LWS_WooRewards')
            && class_exists('\LWS\WOOREWARDS\Core\Trace');
    }
}
