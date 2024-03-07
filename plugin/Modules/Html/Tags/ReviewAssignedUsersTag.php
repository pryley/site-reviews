<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

class ReviewAssignedUsersTag extends ReviewTag
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
        $displayNames = wp_list_pluck($this->review->assignedUsers(), 'display_name');
        if (empty($displayNames)) {
            return '';
        }
        array_walk($displayNames, function (&$displayName) {
            $displayName = glsr(Sanitizer::class)->sanitizeUserName($displayName);
        });
        return sprintf(__('Review of %s', 'site-reviews'), Str::naturalJoin($displayNames));
    }
}
