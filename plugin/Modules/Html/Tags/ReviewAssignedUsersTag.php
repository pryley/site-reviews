<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Str;

class ReviewAssignedUsersTag extends ReviewTag
{
    protected function handle(): string
    {
        return $this->wrap($this->value(), 'span');
    }

    protected function value(): string
    {
        $users = wp_list_pluck($this->review->assignedUsers(), 'display_name');
        return !empty($users)
            ? sprintf(__('Review of %s', 'site-reviews'), Str::naturalJoin($users))
            : '';
    }
}
