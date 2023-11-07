<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

class ReviewTitleTag extends ReviewTag
{
    protected function handle(string $value = ''): string
    {
        if ($this->isHidden()) {
            return '';
        }
        $title = trim($value);
        if (empty($title)) {
            $title = __('No Title', 'site-reviews');
        }
        return $this->wrap($title, 'h4');
    }
}
