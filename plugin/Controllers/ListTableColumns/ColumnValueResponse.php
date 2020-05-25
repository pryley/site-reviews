<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Rating;

class ColumnValueResponse implements ColumnValue
{
    /**
     * {@inheritdoc}
     */
    public function handle(Rating $rating)
    {
        return glsr(Database::class)->get($rating->review_id, 'response')
            ? _x('Yes', 'admin-text', 'site-reviews')
            : _x('No', 'admin-text', 'site-reviews');
    }
}
