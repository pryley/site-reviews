<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;

class PostManager
{
    /**
     * @param int|string $postId
     * @return int
     */
    public function normalizeId($postId)
    {
        if ('parent_id' == $postId) {
            return wp_get_post_parent_id(intval(get_the_ID()));
        }
        elseif ('post_id' == $postId) {
            $postId = get_the_ID();
        }
        elseif ($post = get_post($postId)) {
            $postId = $post->ID;
        }
        return Cast::toInt($postId);
    }

    /**
     * @param array[]|string $postIds
     * @return array
     */
    public function normalizeIds($postIds)
    {
        $postIds = Cast::toArray($postIds);
        $postIds = array_filter($postIds, function ($postId) {
            return is_numeric($postId) || in_array($postId, ['post_id', 'parent_id']);
        });
        foreach ($postIds as &$postId) {
            $postId = $this->normalizeId($postId);
        }
        return Arr::uniqueInt($postIds);
    }
}
