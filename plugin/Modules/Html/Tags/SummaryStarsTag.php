<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Modules\Rating;

class SummaryStarsTag extends SummaryTag
{
    protected function handle(): string
    {
        if ($this->isHidden()) {
            return '';
        }
        return $this->wrap($this->value());
    }

    protected function value(): string
    {
        $rating = glsr(Rating::class)->average($this->ratings);
        return glsr_star_rating($rating, 0, $this->args->toArray());
    }
}
