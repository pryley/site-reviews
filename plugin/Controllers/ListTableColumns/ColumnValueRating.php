<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Rating;

class ColumnValueRating implements ColumnValue
{
    /**
     * {@inheritdoc}
     */
    public function handle(Rating $rating)
    {
        return glsr_star_rating($rating->rating);
    }
}
