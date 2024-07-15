<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Database\Cache;
use GeminiLabs\SiteReviews\Metaboxes\DashboardMetabox;

class DashboardController extends AbstractController
{
    /**
     * @action transition_post_status:5
     */
    public function flushMonthlyCountCache(string $newStatus, string $oldStatus, ?\WP_Post $post): void
    {
        if (is_null($post)) {
            return; // This should never happen, but some plugins are bad actors so...
        }
        if (glsr()->post_type !== $post->post_type) {
            return;
        }
        if ($newStatus === $oldStatus) {
            return;
        }
        glsr(Cache::class)->delete('monthly', 'count');
    }

    /**
     * @action wp_dashboard_setup
     */
    public function registerMetaBoxes(): void
    {
        glsr(DashboardMetabox::class)->register();
    }
}
