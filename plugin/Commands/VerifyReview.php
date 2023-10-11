<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Review;

class VerifyReview implements Contract
{
    public $review;

    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    /**
     * @return bool
     */
    public function handle()
    {
        if ($this->review->is_verified) {
            return false;
        }
        if (Cast::toInt(get_post_meta($this->review->ID, '_verified_on', true))) {
            return false;
        }
        $result = (bool) glsr(ReviewManager::class)->updateRating($this->review->ID, [
            'is_verified' => true,
        ]);
        if ($result) {
            update_post_meta($this->review->ID, '_verified_on', current_datetime()->getTimestamp());
        }
        return $result;
    }
}
