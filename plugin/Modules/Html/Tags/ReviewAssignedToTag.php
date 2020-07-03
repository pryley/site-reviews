<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Multilingual;

class ReviewAssignedToTag extends ReviewTag
{
    /**
     * @param mixed $value
     * @return array
     */
    public static function assignedLinks($value)
    {
        $links = [];
        $usedIds = [];
        foreach (Arr::consolidate($value) as $postId) {
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
    protected function handle($value = null)
    {
        if (!$this->isHidden('reviews.assigned_links')) {
            $links = static::assignedLinks($value);
            $tagValue = !empty($links)
                ? sprintf(__('Review of %s', 'site-reviews'), Str::naturalJoin($links))
                : '';
            return $this->wrap($tagValue, 'span');
        }
    }
}
