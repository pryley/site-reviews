<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

class ReviewVerifiedTag extends ReviewTag
{
    protected function handle(string $value = ''): string
    {
        if ($this->isHidden() || !$this->review->is_verified) {
            return '';
        }
        $icon = file_get_contents(glsr()->path('assets/images/icons/verified.svg'));
        $text = esc_attr__('Verified', 'site-reviews');
        $value = sprintf('%s <span>%s</span>', $icon, $text);
        return $this->wrap($value);
    }
}
