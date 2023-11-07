<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

class ReviewTitleTag extends ReviewTag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
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
