<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Multilingual;

class ReviewAssignedPostsTag extends ReviewTag
{
    protected function handle(string $value = ''): string
    {
        return $this->wrap($value, 'span');
    }

    /**
     * @param mixed $value
     */
    protected function value($value = ''): string
    {
        $postIds = glsr(Multilingual::class)->getPostIds(Arr::consolidate($value));
        if (empty($postIds)) {
            return '';
        }
        $posts = get_posts([
            'post__in' => $postIds,
            'post_type' => 'any',
            'posts_per_page' => -1,
        ]);
        $titles = wp_list_pluck($posts, 'post_title');
        $titles = Arr::unique($titles);
        return !empty($titles)
            ? sprintf(__('Review of %s', 'site-reviews'), Str::naturalJoin($titles))
            : '';
    }
}
