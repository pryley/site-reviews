<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Contracts\ColumnValueContract;
use GeminiLabs\SiteReviews\Modules\Html\Tags\ReviewAssignedLinksTag;
use GeminiLabs\SiteReviews\Review;

class ColumnValueAssignedPosts implements ColumnValueContract
{
    public function handle(Review $review): string
    {
        $links = ReviewAssignedLinksTag::assignedLinks($review->assigned_posts);
        return implode(', ', $links);
    }
}
