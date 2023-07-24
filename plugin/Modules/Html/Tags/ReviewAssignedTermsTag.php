<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Str;

class ReviewAssignedTermsTag extends ReviewTag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        return $this->wrap($value, 'span');
    }

    /**
     * {@inheritdoc}
     */
    protected function value($value = null)
    {
        $terms = wp_list_pluck($this->review->assignedTerms(), 'name');
        return !empty($terms)
            ? sprintf(__('Review of %s', 'site-reviews'), Str::naturalJoin($terms))
            : '';
    }
}
