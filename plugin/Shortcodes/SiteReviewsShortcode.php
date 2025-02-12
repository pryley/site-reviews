<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\ReviewsHtml;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Modules\Schema;
use GeminiLabs\SiteReviews\Reviews;

class SiteReviewsShortcode extends Shortcode
{
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

    public function buildTemplate(): string
    {
        return (string) $this->buildReviewsHtml();
    }

    public function description(): string
    {
        return esc_html_x('Display your reviews', 'admin-text', 'site-reviews');
    }

    public function generateSchema(Reviews $reviews): void
    {
        if (Cast::toBool($this->args['schema'])) {
            glsr(Schema::class)->store(
                glsr(Schema::class)->build($this->args, $reviews)
            );
        }
    }

    public function name(): string
    {
        return esc_html_x('Latest Reviews', 'admin-text', 'site-reviews');
    }

    protected function config(): array
    {
        return [
            'assigned_posts' => [
                'label' => esc_html_x('Limit Reviews by Assigned Pages', 'admin-text', 'site-reviews'),
                'multiple' => true,
                'placeholder' => esc_html_x('Select a Page...', 'admin-text', 'site-reviews'),
                'type' => 'select',
            ],
            'assigned_terms' => [
                'label' => esc_html_x('Limit Reviews by Assigned Categories', 'admin-text', 'site-reviews'),
                'multiple' => true,
                'placeholder' => esc_html_x('Select a Category...', 'admin-text', 'site-reviews'),
                'type' => 'select',
            ],
            'assigned_users' => [
                'label' => esc_html_x('Limit Reviews by Assigned Users', 'admin-text', 'site-reviews'),
                'multiple' => true,
                'placeholder' => esc_html_x('Select a User...', 'admin-text', 'site-reviews'),
                'type' => 'select',
            ],
            'terms' => [
                'label' => esc_html_x('Limit Reviews by terms accepted', 'admin-text', 'site-reviews'),
                'options' => $this->options('terms'),
                'placeholder' => esc_html_x('Select Review Terms...', 'admin-text', 'site-reviews'),
                'type' => 'select',
            ],
            'type' => [
                'label' => esc_html_x('Limit Reviews by Type', 'admin-text', 'site-reviews'),
                'options' => $this->options('type'),
                'placeholder' => esc_html_x('Select a Review Type...', 'admin-text', 'site-reviews'),
                'type' => 'select',
            ],
            'rating' => [
                'default' => (string) Rating::min(),
                'group' => 'display',
                'label' => esc_html_x('Minimum Rating', 'admin-text', 'site-reviews'),
                'max' => Rating::max(),
                'min' => Rating::min(),
                'placeholder' => (string) Rating::min(),
                'type' => 'number',
            ],
            'display' => [
                'default' => 10,
                'group' => 'display',
                'label' => esc_html_x('Reviews Per Page', 'admin-text', 'site-reviews'),
                'max' => 50,
                'min' => 1,
                'type' => 'number',
            ],
            'pagination' => [
                'label' => esc_html_x('Pagination Type', 'admin-text', 'site-reviews'),
                'group' => 'display',
                'options' => $this->options('pagination'),
                'placeholder' => esc_attr_x('No Pagination', 'admin-text', 'site-reviews'),
                'type' => 'select',
            ],
            'schema' => [
                'description' => esc_html_x('The schema should only be enabled once on your page.', 'admin-text', 'site-reviews'),
                'group' => 'schema',
                'label' => esc_html_x('Enable the schema?', 'admin-text', 'site-reviews'),
                'type' => 'checkbox',
            ],
            'hide' => [
                'group' => 'hide',
                'options' => $this->options('hide'),
                'type' => 'checkbox',
            ],
            'id' => [
                'description' => esc_html_x('This should be a unique value.', 'admin-text', 'site-reviews'),
                'group' => 'advanced',
                'label' => esc_html_x('Custom ID', 'admin-text', 'site-reviews'),
                'type' => 'text',
            ],
            'class' => [
                'description' => esc_html_x('Separate multiple classes with spaces.', 'admin-text', 'site-reviews'),
                'group' => 'advanced',
                'label' => esc_html_x('Additional CSS classes', 'admin-text', 'site-reviews'),
                'type' => 'text',
            ],
        ];
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
