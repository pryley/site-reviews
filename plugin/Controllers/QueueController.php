<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\CountManager;
use GeminiLabs\SiteReviews\Modules\Migrate;
use GeminiLabs\SiteReviews\Modules\Notification;

class QueueController extends AbstractController
{
    /**
     * @action site-reviews/queue/export/cleanup
     */
    public function cleanupAfterExport(): void
    {
        glsr(Database::class)->deleteMeta(glsr()->export_key);
    }

    /**
     * @action site-reviews/queue/recalculate-meta
     */
    public function recalculateAssignmentMeta(): void
    {
        glsr(CountManager::class)->recalculate();
    }

    /**
     * @action site-reviews/queue/migration
     */
    public function runMigration(): void
    {
        glsr(Migrate::class)->run();
    }

    /**
     * @action site-reviews/queue/notification
     */
    public function sendNotification(int $reviewId): void
    {
        $review = glsr_get_review($reviewId);
        if ($review->isValid()) {
            glsr(Notification::class)->send($review);
        }
    }
}
