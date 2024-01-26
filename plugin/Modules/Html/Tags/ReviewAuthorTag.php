<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

class ReviewAuthorTag extends ReviewTag
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
        return $this->review->author();
    }
}
