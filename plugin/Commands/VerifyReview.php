<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database;
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
        $verifiedOn = glsr(Database::class)->meta($this->review->ID, 'verified_on');
        if (glsr(Date::class)->isTimestamp($verifiedOn)) {
            $this->fail();
            return;
        }
        $result = (bool) glsr(ReviewManager::class)->updateRating($this->review->ID, [
            'is_verified' => true,
        ]);
        if ($result) {
            glsr(Database::class)->metaSet($this->review->ID, 'verified_on', current_datetime()->getTimestamp());
            glsr()->action('review/verified', $this->review);
        } else {
            $this->fail();
        }
    }
}
