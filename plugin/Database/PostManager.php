<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Multilingual;

class PostManager
{
    /**
     * @param int|string $postId
     * @return int
     */
    public function normalizeId($postId)
    {
        if ('parent_id' == $postId) {
            $postId = wp_get_post_parent_id(intval(get_the_ID()));
        }
        elseif ('post_id' == $postId) {
            $postId = get_the_ID();
        }
        return Helper::getPostId($postId);
    }

    /**
     * @param int[]|string $postIds
     * @return array
     */
    public function normalizeIds($postIds)
    {
        $postIds = array_filter(Cast::toArray($postIds), function ($postId) {
            return is_numeric($postId) || in_array($postId, ['post_id', 'parent_id']);
        });
        foreach ($postIds as &$postId) {
            $postId = $this->normalizeId($postId);
        }
        return Arr::uniqueInt(glsr(Multilingual::class)->getPostIds($postIds));
    }
}
