<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Str;

class ReviewAssignedTermsTag extends ReviewTag
{
    protected function handle(string $value = ''): string
    {
        return $this->wrap($value, 'span');
    }

    /**
     * @param mixed $value
     */
    protected function value($value = ''): string
    {
        $terms = wp_list_pluck($this->review->assignedTerms(), 'name');
        return !empty($terms)
            ? sprintf(__('Review of %s', 'site-reviews'), Str::naturalJoin($terms))
            : '';
    }
}
