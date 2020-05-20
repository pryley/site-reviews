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
            $fallback = __('No Title', 'site-reviews');
            $title = trim($value);
            return empty($title) ? $fallback : '<h3>'.$title.'</h3>';
        }
    }
}
