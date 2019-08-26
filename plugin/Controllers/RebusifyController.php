<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Rebusify;
use GeminiLabs\SiteReviews\Review;

class RebusifyController extends Controller
{
    /**
     * Triggered when a review is created
     * @return void
     * @action site-reviews/review/created
     */
    public function onCreated(Review $review)
    {
        if ($this->canProceed($review) && 'publish' === $review->status) {
            $result = glsr(Rebusify::class)->sendReview($review);
            // @todo
        }
    }

    /**
     * Triggered when a review is reverted to its original title/content/date_timestamp
     * @return void
     * @action site-reviews/review/reverted
     */
    public function onReverted(Review $review)
    {
        if ($this->canProceed($review) && 'publish' === $review->status) {
            $result = glsr(Rebusify::class)->sendReview($review);
            // @todo
        }
    }

    /**
     * Triggered when an existing review is updated
     * @return void
     * @action site-reviews/review/saved
     */
    public function onSaved(Review $review)
    {
        if ($this->canProceed($review) && 'publish' === $review->status) {
            $result = glsr(Rebusify::class)->sendReview($review);
            // @todo
        }
    }

    /**
     * Triggered when a review's response is added or updated
     * @param int $metaId
     * @param int $postId
     * @param string $metaKey
     * @param mixed $metaValue
     * @return void
     * @action updated_postmeta
     */
    public function onUpdatedMeta($metaId, $postId, $metaKey, $metaValue)
    {
        if (!$this->isReviewPostId($postId) 
            || !$this->canProceed($review) 
            || '_response' !== $metaKey) {
            return;
        }
        $review = glsr_get_review($postId);
        $result = glsr(Rebusify::class)->sendReviewResponse($review);
        // @todo
    }

    /**
     * @return bool
     */
    protected function canProceed(Review $review)
    {
        return glsr(OptionManager::class)->getBool('settings.general.support.rebusify');
    }
}
