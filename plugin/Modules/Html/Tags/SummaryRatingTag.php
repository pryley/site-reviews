<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Modules\Rating;

class SummaryRatingTag extends SummaryTag
{
    protected function handle(string $value = ''): string
    {
        if ($this->isHidden()) {
            return '';
        }
        $rating = glsr(Rating::class)->average($this->ratings);
        $rating = glsr(Rating::class)->format($rating);
        return $this->wrap($rating, 'span');
    }
}
