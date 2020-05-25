<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Rating;

class ColumnValueReviewer implements ColumnValue
{
    /**
     * {@inheritdoc}
     */
    public function handle(Rating $rating)
    {
        if ($userId = get_post($rating->review_id)->post_author) {
            return glsr(Builder::class)->a([
                'href' => get_author_posts_url($userId),
                'text' => $rating->name,
            ]);
        }
        return $rating->name;
    }
}
