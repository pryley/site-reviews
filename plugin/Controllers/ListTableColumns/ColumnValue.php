<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Review;

interface ColumnValue
{
    /**
     * @return string|void
     */
    public function handle(Review $review);
}
