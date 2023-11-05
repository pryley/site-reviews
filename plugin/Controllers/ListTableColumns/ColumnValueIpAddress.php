<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Contracts\ColumnValueContract;
use GeminiLabs\SiteReviews\Review;

class ColumnValueIpAddress implements ColumnValueContract
{
    public function handle(Review $review): string
    {
        return $review->ip_address;
    }
}
