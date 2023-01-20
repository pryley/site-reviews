<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Contracts\ColumnValueContract;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Review;

class ColumnValueRating implements ColumnValueContract
{
    /**
     * {@inheritdoc}
     */
    public function handle(Review $review)
    {
        $maxRating = glsr()->constant('MAX_RATING', Rating::class);
        if (5 === (int) $maxRating) {
            return wp_star_rating([
                'echo' => false,
                'rating' => $review->rating,
            ]);
        }
        return sprintf('<span style="background:#787c82;border-radius:4px;color:#fff;padding:4px 8px;">%s / %s',
            $review->rating,
            $maxRating
        );
    }
}
