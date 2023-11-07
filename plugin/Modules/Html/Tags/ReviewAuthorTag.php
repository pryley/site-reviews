<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

class ReviewAuthorTag extends ReviewTag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        if ($this->isHidden()) {
            return '';
        }
        return $this->wrap($this->review->author(), 'span');
    }
}
