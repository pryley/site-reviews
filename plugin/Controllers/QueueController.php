<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\CountManager;
use GeminiLabs\SiteReviews\Modules\Migrate;
use GeminiLabs\SiteReviews\Modules\Notification;

class QueueController extends AbstractController
{
    /**
     * @return void
     * @action site-reviews/queue/export/cleanup
     */
    public function cleanupAfterExport()
    {
        glsr(Database::class)->deleteMeta(glsr()->export_key);
    }

    /**
     * @return void
     * @action site-reviews/queue/recalculate-meta
     */
    public function recalculateAssignmentMeta()
    {
        glsr(CountManager::class)->recalculate();
    }

    /**
     * @return void
     * @action site-reviews/queue/migration
     */
    public function runMigration()
    {
        glsr(Migrate::class)->run();
    }

    /**
     * @param int $reviewId
     * @return void
     * @action site-reviews/queue/notification
     */
    public function sendNotification($reviewId)
    {
        $review = glsr_get_review($reviewId);
        if ($review->isValid()) {
            glsr(Notification::class)->send($review);
        }
    }
}
