<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Str;

class ReviewAssignedUsersTag extends ReviewTag
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
        $users = wp_list_pluck($this->review->assignedUsers(), 'display_name');
        return !empty($users)
            ? sprintf(__('Review of %s', 'site-reviews'), Str::naturalJoin($users))
            : '';
    }
}
