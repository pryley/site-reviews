<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Review;

class UnassignTerms extends AbstractCommand
{
    /** @var Review */
    public $review;
    public array $termIds = [];

    public function __construct(Review $review, array $termIds)
    {
        $this->review = $review;
        $this->termIds = Arr::uniqueInt($termIds);
    }

    public function handle(): void
    {
        foreach ($this->termIds as $termId) {
            glsr(ReviewManager::class)->unassignTerm($this->review, $termId);
        }
    }
}
