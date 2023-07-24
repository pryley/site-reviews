<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Str;

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
        $users = wp_list_pluck($this->review->assignedUsers(), 'display_name');
        return !empty($users)
            ? sprintf(__('Review of %s', 'site-reviews'), Str::naturalJoin($users))
            : '';
    }
}
