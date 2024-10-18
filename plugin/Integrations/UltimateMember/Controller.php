<?php

namespace GeminiLabs\SiteReviews\Integrations\UltimateMember;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Review;

class Controller extends AbstractController
{
    /**
     * @filter site-reviews/avatar/generate
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

    /**
     * @filter site-reviews/assigned_users/profile_id
     */
    public function filterProfileId(int $profileId): int
    {
        if (empty($profileId)) {
            return (int) um_get_requested_user();
        }
        return $profileId;
    }
}
