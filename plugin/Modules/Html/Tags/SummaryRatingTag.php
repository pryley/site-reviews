<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Modules\Rating;

class SummaryRatingTag extends SummaryTag
{
    protected function handle(): string
    {
        if ($this->isHidden()) {
            return '';
        }
        return $this->wrap($this->value(), 'span');
    }

    protected function value(): string
    {
        $rating = glsr(Rating::class)->average($this->ratings);
        $rating = glsr(Rating::class)->format($rating);
        return $rating;
    }
}
