<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\ReviewsHtml;
use GeminiLabs\SiteReviews\Modules\Schema;
use GeminiLabs\SiteReviews\Reviews;

class SiteReviewsShortcode extends Shortcode
{
    /**
     * @todo remove in v7.0
     */
    public function __call($method, $args)
    {
        if ('buildReviewsHtmlFromArgs' === $method) { // @compat for < 6.6.0
            call_user_func_array([$this, 'normalize'], $args);
            return $this->buildReviewsHtml();
        }
        return parent::__call($method, $args);
    }

    public function buildReviewsHtml(): ReviewsHtml
    {
        $reviews = glsr(ReviewManager::class)->reviews($this->args);
        $this->debug((array) $reviews);
        $this->generateSchema($reviews);
        if ('modal' === glsr_get_option('reviews.excerpts_action')) {
            glsr()->store('use_modal', true);
        }
        return new ReviewsHtml($reviews);
    }

    /**
     * @return string
     * @todo add return type hint and remove $args in v7.0
     */
    public function buildTemplate(array $args = [])
    {
        return (string) $this->buildReviewsHtml();
    }

    public function generateSchema(Reviews $reviews): void
    {
        if (Cast::toBool($this->args['schema'])) {
            glsr(Schema::class)->store(
                glsr(Schema::class)->build($this->args, $reviews)
            );
        }
    }

    protected function debug(array $data = []): void
    {
        if (!empty($this->args['debug'])) {
            $reviews = [];
            foreach ($data['reviews'] as $review) {
                $reviews[$review->ID] = get_class($review);
            }
            $data['reviews'] = $reviews;
            parent::debug($data);
        }
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
