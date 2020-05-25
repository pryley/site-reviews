<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Rating;

class ColumnValuePinned implements ColumnValue
{
    /**
     * {@inheritdoc}
     */
    public function handle(Rating $rating)
    {
        $pinned = $rating->is_pinned ? 'pinned ' : '';
        if (glsr()->can('edit_others_posts')) {
            $pinned .= 'pin-review ';
        }
        return glsr(Builder::class)->i([
            'class' => $pinned.'dashicons dashicons-sticky',
            'data-id' => $rating->review_id,
        ]);
    }
}
