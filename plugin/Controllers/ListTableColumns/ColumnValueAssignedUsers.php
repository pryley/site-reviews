<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Contracts\ColumnValueContract;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Review;

class ColumnValueAssignedUsers implements ColumnValueContract
{
    public function handle(Review $review): string
    {
        $links = [];
        foreach ($review->assigned_users as $userId) {
            $user = get_user_by('id', $userId);
            if (!$user) {
                continue;
            }
            $links[] = glsr(Builder::class)->a([
                'href' => esc_url(get_author_posts_url($user->ID)),
                'text' => glsr(Sanitizer::class)->sanitizeUserName($user),
            ]);
        }
        return implode(', ', $links);
    }
}
