<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\TemplateTags;

class ReviewAssignedLinksTag extends ReviewTag
{
    /**
     * @param mixed $value
     */
    public static function assignedLinks($value): array
    {
        return glsr(TemplateTags::class)->tagReviewAssignedLinks($this->review);
    }

    protected function handle(string $value = ''): string
    {
        return $this->wrap($value, 'span');
    }

    /**
     * @param mixed $value
     */
    protected function value($value = ''): string
    {
        if ($this->isHidden('reviews.assigned_links')) {
            return '';
        }
        $links = glsr(TemplateTags::class)->tagReviewAssignedLinks($this->review);
        return !empty($links)
            ? sprintf(__('Review of %s', 'site-reviews'), Str::naturalJoin($links))
            : '';
    }
}
