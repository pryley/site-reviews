<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Contracts\ColumnValueContract;
use GeminiLabs\SiteReviews\Review;

class ColumnValueRating implements ColumnValueContract
{
    /**
     * {@inheritdoc}
     */
    public function handle(Review $review)
    {
        return glsr_star_rating($review->rating);
    }
}
