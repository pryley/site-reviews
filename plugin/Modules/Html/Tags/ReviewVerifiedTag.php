<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

class ReviewVerifiedTag extends ReviewTag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        if (!$this->isHidden() && $this->review->is_verified) {
            $icon = file_get_contents(glsr()->path('assets/images/icons/verified.svg'));
            $text = esc_attr__('Verified', 'site-reviews');
            $value = sprintf('%s <span>%s</span>', $icon, $text);
            return $this->wrap($value);
        }
    }
}
