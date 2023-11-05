<?php

namespace GeminiLabs\SiteReviews\Contracts;

use GeminiLabs\SiteReviews\Review;

interface ColumnValueContract
{
    public function handle(Review $review): string;
}
