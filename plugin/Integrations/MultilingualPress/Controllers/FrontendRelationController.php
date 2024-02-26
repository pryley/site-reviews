<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress\Controllers;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Integrations\MultilingualPress\ReviewCopier;
use GeminiLabs\SiteReviews\Review;

class FrontendRelationController extends AbstractController
{
    /**
     * @todo We need to pass an arg to these type of hooks if it is a frontend or backend action...
     * @action site-reviews/review/created
     */
    public function onCreatedReview(Review $review): void
    {
        global $pagenow;
        error_log(print_r(['is_admin' => is_admin(), 'isAdmin' => glsr()->isAdmin()], true));
        error_log(print_r(glsr_current_screen(), true));
        error_log(print_r($pagenow, true));
        error_log(print_r($_POST, true));
        error_log(print_r(glsr('Modules\Backtrace')->trace(30), true));

        if ($this->isReviewEditor()) {
            return;
        }
        if (glsr()->retrieve('glsr_create_review', false)) {
            return;
        }
        error_log(print_r('onCreatedReview', true));
        // $sourcePostId = $review->ID;
        // $sourceSiteId = get_current_blog_id();
        // update_post_meta($sourcePostId, '_trash_the_other_posts', 1); // sync review deletion
        // $copier = new ReviewCopier($sourcePostId, $sourceSiteId);
        // $copier->copy();
    }

    /**
     * @action site-reviews/review/transitioned
     */
    public function onTransitioned(Review $review, string $status, string $prevStatus): void
    {
        if ($this->isReviewEditor()) {
            return;
        }
        error_log(print_r('onTransitioned', true));
        if (!empty(array_diff([$status, $prevStatus], ['pending', 'publish']))) {
            return;
        }
        $sourcePostId = $review->ID;
        $sourceSiteId = get_current_blog_id();
        $copier = new ReviewCopier($sourcePostId, $sourceSiteId);
        $copier->run(function ($context) use ($prevStatus, $status) {
            if ($prevStatus !== get_post_status($context->remotePostId())) {
                return;
            }
            wp_update_post([
                'ID' => $context->remotePostId(),
                'post_status' => $status,
            ]);
        });
    }

    /**
     * @action site-reviews/review/updated
     */
    public function onUpdatedReview(Review $review): void
    {
        if (glsr()->isAdmin()) {
            return;
        }
        if (glsr()->retrieve('glsr_update_review', false)) {
            return;
        }
        error_log(print_r('onUpdatedReview', true));
        $sourcePostId = $review->ID;
        $sourceSiteId = get_current_blog_id();
        $copier = new ReviewCopier($sourcePostId, $sourceSiteId);
        $copier->sync();
    }
}
