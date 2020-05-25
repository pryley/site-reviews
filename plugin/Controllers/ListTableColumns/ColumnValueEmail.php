<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Rating;

class ColumnValueEmail implements ColumnValue
{
    /**
     * {@inheritdoc}
     */
    public function handle(Rating $rating)
    {
        return $rating->email;
    }
}
