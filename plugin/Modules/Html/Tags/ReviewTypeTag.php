<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

class ReviewTypeTag extends ReviewTag
{
    protected function handle(): string
    {
        return $this->wrap($this->value());
    }

    protected function value(): string
    {
        if ('local' === $this->value) {
            return '';
        }
        return $this->review->type();
    }
}
