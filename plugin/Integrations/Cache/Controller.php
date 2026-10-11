<?php

namespace GeminiLabs\SiteReviews\Integrations\Cache;

use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Integrations\Cache\Commands\Flush;
use GeminiLabs\SiteReviews\Review;

class Controller extends AbstractController
{
    /**
     * @action site-reviews/cache/flush
     */
    public function flush(string $loggedMessage, Review $review): void
    {
        $loggedMessage = $loggedMessage ?: "flushed_review_{$review->ID}";
        $this->execute(new Flush($loggedMessage, $review->assigned_posts));
    }

    /**
     * @action site-reviews/review/created
     */
    public function flushAfterCreated(Review $review, CreateReview $command): void
    {
        if (defined('WP_IMPORTING')) {
            return;
        }
        if (!$review->is_approved) {
            return;
        }
        $postIds = array_merge($command->assigned_posts, [$command->post_id]);
        $this->execute(new Flush("review_{$review->ID}_created", Arr::uniqueInt($postIds)));
    }

    /**
     * @action site-reviews/migration/end
     */
    public function flushAfterMigrated(): void
    {
        $this->execute(new Flush('plugin_migrated'));
    }

    /**
     * @action site-reviews/review/transitioned
     */
    public function flushAfterTransitioned(Review $review, string $new, string $old): void
    {
        if (did_action('site-reviews/review/updated')) {
            return;
        }
        if (!in_array('publish', [$new, $old])) {
            return;
        }
        $status = [
            'publish' => 'approved',
            'pending' => 'unapproved',
            'trash' => 'trashed',
        ][$new] ?? $new;
        $this->execute(new Flush("review_{$review->ID}_{$status}", $review->assigned_posts));
    }

    /**
     * @action site-reviews/review/updated
     */
    public function flushAfterUpdated(Review $review): void
    {
        if (did_action('site-reviews/review/transitioned')) {
            return;
        }
        $this->execute(new Flush("review_{$review->ID}_updated", $review->assigned_posts));
    }

    /**
     * @action site-reviews/cache/flush_all
     */
    public function flushAll(string $loggedMessage): void
    {
        $this->execute(new Flush($loggedMessage));
    }
}
