<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

class ReviewRatingTag extends ReviewTag
{
    protected function handle(string $value = ''): string
    {
        if ($this->isHidden()) {
            return '';
        }
        $stars = glsr_star_rating($value, 0, $this->args->toArray());
        return $this->wrap($stars);
    }
}
