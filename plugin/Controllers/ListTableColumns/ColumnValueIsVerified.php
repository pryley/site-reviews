<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Contracts\ColumnValueContract;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Review;

class ColumnValueIsVerified implements ColumnValueContract
{
    /**
     * {@inheritdoc}
     */
    public function handle(Review $review)
    {
        $classes = $review->is_verified ? 'verified ' : '';
        if (glsr()->can('edit_others_posts') && glsr()->filterBool('verification/enabled', false)) {
            $classes .= 'verify-review ';
        }
        return glsr(Builder::class)->i([
            'class' => $classes.'dashicons dashicons-yes-alt',
            'data-id' => $review->ID,
        ]);
    }
}
