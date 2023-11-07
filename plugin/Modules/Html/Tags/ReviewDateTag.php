<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

class ReviewDateTag extends ReviewTag
{
    protected function handle(string $value = ''): string
    {
        if ($this->isHidden()) {
            return '';
        }
        return $this->wrap($this->review->date(), 'span');
    }
}
