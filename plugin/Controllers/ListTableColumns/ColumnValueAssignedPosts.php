<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Contracts\ColumnValueContract;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Review;

class ColumnValueAssignedPosts implements ColumnValueContract
{
    public function handle(Review $review): string
    {
        $links = [];
        $posts = $review->assignedPosts(false); // don't translate the assigned post titles
        foreach ($posts as $post) {
            $title = trim(get_the_title($post->ID));
            $title = $title ?: $post->post_name ?: $post->ID;
            $links[$post->ID] = glsr(Builder::class)->a([
                'href' => (string) get_the_permalink($post->ID),
                'text' => $title,
            ]);
        }
        return Str::naturalJoin($links);
    }
}
