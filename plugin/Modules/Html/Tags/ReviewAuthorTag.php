<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

class ReviewAuthorTag extends ReviewTag
{
    protected function handle(string $value = ''): string
    {
        if ($this->isHidden()) {
            return '';
        }
        return $this->wrap($this->review->author(), 'span');
    }
}
