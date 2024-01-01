<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Database\Cache;
use GeminiLabs\SiteReviews\Metaboxes\DashboardMetabox;

class DashboardController extends AbstractController
{
    /**
     * @param string $newStatus
     * @param string $oldStatus
     * @param \WP_Post $post
     * @action transition_post_status:5
     */
    public function flushMonthlyCountCache($newStatus, $oldStatus, $post): void
    {
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
