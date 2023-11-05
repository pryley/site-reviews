<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Modules\Html\ReviewsHtml;
use GeminiLabs\SiteReviews\Reviews;

class SiteReviewShortcode extends Shortcode
{
    public function buildTemplate(): string
    {
        $review = glsr(ReviewManager::class)->get($this->args['post_id']);
        $this->debug(['review' => $review]);
        if ($review->isValid()) {
            $reviews = new Reviews([$review], 1, $this->args);
            glsr()->action('get/reviews', $reviews, $this->args);
            if ('modal' === glsr_get_option('reviews.excerpts_action')) {
                glsr()->store('use_modal', true);
            }
        } else {
            $reviews = new Reviews([], 0, $this->args);
        }
        $html = new ReviewsHtml($reviews);
        return (string) $html;
    }

    protected function hideOptions(): array
    {
        return [ // order is intentional
            'title' => _x('Hide the title', 'admin-text', 'site-reviews'),
            'rating' => _x('Hide the rating', 'admin-text', 'site-reviews'),
            'date' => _x('Hide the date', 'admin-text', 'site-reviews'),
            'assigned_links' => _x('Hide the assigned links (if shown)', 'admin-text', 'site-reviews'),
            'content' => _x('Hide the content', 'admin-text', 'site-reviews'),
            'avatar' => _x('Hide the avatar (if shown)', 'admin-text', 'site-reviews'),
            'author' => _x('Hide the author', 'admin-text', 'site-reviews'),
            'verified' => _x('Hide the verified badge', 'admin-text', 'site-reviews'),
            'response' => _x('Hide the response', 'admin-text', 'site-reviews'),
        ];
    }
}
