<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

class ReviewAssignedUsersTag extends ReviewTag
{
    protected function handle(): string
    {
        return $this->wrap($this->value(), 'span');
    }

    protected function value(): string
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
