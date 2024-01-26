<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Cast;

class ReviewTitleTag extends ReviewTag
{
    protected function handle(): string
    {
        if ($this->isHidden()) {
            return '';
        }
        return $this->wrap($this->value(), 'h4');
    }

    protected function value(): string
    {
        return $this->value ?: __('No Title', 'site-reviews');
    }
}
