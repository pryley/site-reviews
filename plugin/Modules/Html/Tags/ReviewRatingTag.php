<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

class ReviewRatingTag extends ReviewTag
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
        return glsr_star_rating($this->value, 0, $this->args->toArray());
    }
}
