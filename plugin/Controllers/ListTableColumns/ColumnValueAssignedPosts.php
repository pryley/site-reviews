<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Modules\Html\Tags\ReviewAssignedToTag;
use GeminiLabs\SiteReviews\Review;

class ColumnValueAssignedPosts implements ColumnValue
{
    /**
     * {@inheritdoc}
     */
    public function handle(Review $review)
    {
        $links = ReviewAssignedToTag::assignedLinks($review->assigned_posts);
        return implode(', ', $links);
    }
}
