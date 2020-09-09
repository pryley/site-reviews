<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Review;

class ColumnValueResponse implements ColumnValue
{
    /**
     * {@inheritdoc}
     */
    public function handle(Review $review)
    {
        return empty($review->response)
            ? _x('No', 'admin-text', 'site-reviews')
            : _x('Yes', 'admin-text', 'site-reviews');
    }
}
