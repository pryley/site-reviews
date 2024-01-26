<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

class ReviewAssignedPostsTag extends ReviewTag
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
        $titles = wp_list_pluck($this->review->assignedPosts(), 'post_title');
        $titles = Arr::unique($titles);
        return !empty($titles)
            ? sprintf(__('Review of %s', 'site-reviews'), Str::naturalJoin($titles))
            : '';
    }
}
