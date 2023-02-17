<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Defaults\SiteReviewDefaults;
use GeminiLabs\SiteReviews\Modules\Html\ReviewsHtml;
use GeminiLabs\SiteReviews\Reviews;

class SiteReviewShortcode extends Shortcode
{
    /**
     * @var array
     */
    public $args;

    /**
     * {@inheritdoc}
     */
    public function buildTemplate(array $args = [])
    {
        return (string) $this->buildReviewsHtml($args);
    }

    public function buildReviewsHtml(array $args = []): ReviewsHtml
    {
        $this->args = glsr(SiteReviewDefaults::class)->unguardedMerge($args);
        $review = glsr(ReviewManager::class)->get($args['post_id']);
        $this->debug(['review' => $review]);
        if ($review->isValid()) {
            $reviews = new Reviews([$review], 1, $args);
            if ('modal' === glsr_get_option('reviews.excerpts_action')) {
                glsr()->store('use_modal', true);
            }
        } else {
            $reviews = new Reviews([], 0, $args);
        }
        return new ReviewsHtml($reviews);
    }

    /**
     * @return array
     */
    protected function hideOptions()
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
