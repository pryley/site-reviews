<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Modules\Html\TemplateTags;

class ReviewAssignedLinksTag extends ReviewTag
{
    protected function handle(): string
    {
        if ($this->isHidden('reviews.assigned_links')) {
            return '';
        }
        return $this->wrap($this->value(), 'span');
    }

    protected function value(): string
    {
        $links = glsr(TemplateTags::class)->tagReviewAssignedLinks($this->review);
        return !empty($links)
            ? sprintf(__('Review of %s', 'site-reviews'), $links)
            : '';
    }
}
