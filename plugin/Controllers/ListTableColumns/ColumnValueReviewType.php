<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Rating;

class ColumnValueReviewType implements ColumnValue
{
    /**
     * {@inheritdoc}
     */
    public function handle(Rating $rating)
    {
        return array_key_exists($rating->type, glsr()->reviewTypes)
            ? glsr()->reviewTypes[$rating->type]
            : _x('Unsupported Type', 'admin-text', 'site-reviews');
    }
}
