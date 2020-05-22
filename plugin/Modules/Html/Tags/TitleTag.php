<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

class TitleTag extends Tag
{
    /**
     * {@inheritdoc}
     */
    public function handle($value)
    {
        if (!$this->isHidden()) {
            $title = trim($value);
            if (empty($title)) {
                $title = __('No Title', 'site-reviews');
            }
            return $this->wrap($title, 'h3');
        }
    }
}
