<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Modules\Rating;

class SummaryRatingTag extends SummaryTag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        if (!$this->isHidden()) {
            $rating = glsr(Rating::class)->average($this->ratings);
            $rating = glsr(Rating::class)->format($rating);
            return $this->wrap($rating, 'span');
        }
    }
}
