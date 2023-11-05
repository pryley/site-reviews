<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Contracts\ColumnValueContract;
use GeminiLabs\SiteReviews\Review;

class ColumnValueResponse implements ColumnValueContract
{
    public function handle(Review $review): string
    {
        return empty($review->response)
            ? _x('No', 'admin-text', 'site-reviews')
            : _x('Yes', 'admin-text', 'site-reviews');
    }
}
