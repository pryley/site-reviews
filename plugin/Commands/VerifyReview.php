<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\PostMeta;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Modules\Date;
use GeminiLabs\SiteReviews\Review;

class VerifyReview extends AbstractCommand
{
    /** @var Review */
    public $review;

    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    public function handle(): void
    {
        if ($this->review->is_verified) {
            $this->fail();
            return;
        }
        $result = glsr(ReviewManager::class)->updateRating($this->review->ID, [
            'is_verified' => true,
        ]);
        if ($result > 0) {
            $verifiedOn = glsr(PostMeta::class)->get($this->review->ID, 'verified_on', 'int');
            if (!glsr(Date::class)->isTimestamp($verifiedOn)) {
                glsr(PostMeta::class)->set($this->review->ID, 'verified_on', current_datetime()->getTimestamp());
            }
            glsr()->action('review/verified', $this->review);
        } else {
            $this->fail();
        }
    }
}
