<?php

namespace GeminiLabs\SiteReviews\Integrations\UltimateMember;

use GeminiLabs\SiteReviews\Controllers\Controller as BaseController;
use GeminiLabs\SiteReviews\Review;

class Controller extends BaseController
{
    /**
     * @action site-reviews/avatar/generate
     */
    public function filterAvatarUrl(string $url, Review $review): string
    {
        $type = glsr_get_option('reviews.avatars_fallback');
        $defaultUrl = um_get_default_avatar_uri(); // @phpstan-ignore-line
        if ('none' === $type && !$review->author_id) {
            return $defaultUrl;
        }
        if ('none' !== $type && $url === $defaultUrl) {
            return '';
        }
        return $url;
    }
}
