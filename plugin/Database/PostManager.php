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
        elseif (!is_numeric($postId) && is_string($postId)) {
            $parts = explode(':', $postId);
            $type = Arr::get($parts, 0);
            $slug = Arr::get($parts, 1);
            if (!empty($slug) && !empty($type)) {
                $args = [
                    'fields' => 'ids',
                    'post_name__in' => [$slug],
                    'post_type' => $type,
                    'posts_per_page' => 1,
                ];
                $postId = Arr::get(get_posts($args), 0);
            }
        }
        return Helper::getPostId($postId);
    }

    /**
     * @param array|string $postIds
     * @return array
     */
    public function normalizeIds($postIds)
    {
        $postIds = Cast::toArray($postIds);
        foreach ($postIds as &$postId) {
            $postId = $this->normalizeId($postId);
        }
        return Arr::uniqueInt(glsr(Multilingual::class)->getPostIds($postIds));
    }
}
