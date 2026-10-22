<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

/**
 * A message for the person viewing the review, added through the site-reviews/review/value/notice filter.
 */
class ReviewNoticeTag extends ReviewTag
{
    protected function handle(): string
    {
        if ($this->isHidden()) {
            return '';
        }
        return $this->wrap($this->value());
    }
}
