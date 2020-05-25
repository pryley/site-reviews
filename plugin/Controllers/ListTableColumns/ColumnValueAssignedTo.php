<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Multilingual;
use GeminiLabs\SiteReviews\Rating;

class ColumnValueAssignedTo implements ColumnValue
{
    /**
     * @return array
     */
    public function assignedLinks(array $postIds)
    {
        $links = [];
        $usedIds = [];
        foreach ($postIds as $postId) {
            $post = get_post(glsr(Multilingual::class)->getPostId($postId));
            if (!empty($post->ID) && !in_array($post->ID, $usedIds)) {
                $links[] = glsr(Builder::class)->a([
                    'href' => get_the_permalink($post->ID),
                    'text' => get_the_title($post->ID),
                ]);
                $usedIds[] = $post->ID;
                $usedIds = Arr::unique($usedIds);
            }
        }
        return $links;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Rating $rating)
    {
        return Str::naturalJoin($this->assignedLinks($rating->post_ids));
    }
}
