<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Rating;

interface ColumnValue
{
    /**
     * @return string|void
     */
    public function handle(Rating $rating);
}
