<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\GeolocateReview;
use GeminiLabs\SiteReviews\Commands\GeolocateReviews;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\CountManager;
use GeminiLabs\SiteReviews\Modules\Migrate;
use GeminiLabs\SiteReviews\Modules\Notification;
use GeminiLabs\SiteReviews\Request;

class QueueController extends AbstractController
{
    /**
     * @action site-reviews/queue/export/cleanup
     */
    public function cleanupAfterExport(): void
    {
        delete_post_meta_by_key(glsr()->export_key);
    }

    /**
     * @action site-reviews/queue/geolocation
     */
    public function geolocateReview(int $reviewId): void
    {
        $request = new Request(['review_id' => $reviewId]);
        $this->execute(new GeolocateReview($request));
    }

    /**
     * @action site-reviews/queue/geolocations
     */
    public function geolocateReviews(int $offset): void
    {
        glsr(GeolocateReviews::class)->process($offset);
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
