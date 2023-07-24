<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Multilingual;

class ReviewAssignedLinksTag extends ReviewTag
{
    /**
     * @param mixed $value
     * @return array
     */
    public static function assignedLinks($value)
    {
        $links = [];
        foreach (Arr::consolidate($value) as $postId) {
            $postId = glsr(Multilingual::class)->getPostId(Helper::getPostId($postId));
            if (!empty($postId) && !array_key_exists($postId, $links)) {
                $title = get_the_title($postId);
                if (empty(trim($title))) {
                    $title = _x('No title', 'admin-text', 'site-reviews');
                }
                $links[$postId] = glsr(Builder::class)->a([
                    'href' => get_the_permalink($postId),
                    'text' => $title,
                ]);
            }
        }
        return $links;
    }

    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        return $this->wrap($value, 'span');
    }

    /**
     * {@inheritdoc}
     */
    protected function value($value = null)
    {
        if ($this->isHidden('reviews.assigned_links')) {
            return '';
        }
        $links = static::assignedLinks($value);
        return !empty($links)
            ? sprintf(__('Review of %s', 'site-reviews'), Str::naturalJoin($links))
            : '';
    }
}
