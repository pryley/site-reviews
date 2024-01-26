<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

class ReviewVerifiedTag extends ReviewTag
{
    protected function handle(): string
    {
        if ($this->isHidden() || !$this->review->is_verified) {
            return '';
        }
        return $this->wrap($this->value());
    }

    protected function value(): string
    {
        $icon = file_get_contents(glsr()->path('assets/images/icons/verified.svg'));
        $text = esc_attr__('Verified', 'site-reviews');
        return sprintf('%s <span>%s</span>', $icon, $text);
    }
}
