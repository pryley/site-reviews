<?php

namespace GeminiLabs\SiteReviews\Blocks;

use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Review;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class SiteReviewBlock extends SiteReviewsBlock
{
    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'className' => [
                'default' => '',
                'type' => 'string',
            ],
            'hide' => [
                'default' => '',
                'type' => 'string',
            ],
            'id' => [
                'default' => '',
                'type' => 'string',
            ],
            'post_id' => [
                'default' => '',
                'type' => 'string',
            ],
        ];
    }

    /**
     * @return string
     */
    public function render(array $attributes)
    {
        $attributes['class'] = $attributes['className'];
        $shortcode = glsr(SiteReviewShortcode::class);
        if ('edit' === filter_input(INPUT_GET, 'context')) {
            $attributes = $this->normalize($attributes);
            $this->filterShowMoreLinks('content');
            $this->filterShowMoreLinks('response');
            if (0 !== Cast::toInt($attributes['post_id']) && !Review::isReview($attributes['post_id'])) {
                $this->filterInterpolationForPostId();
            } elseif (!$this->hasVisibleFields($shortcode, $attributes)) {
                $this->filterInterpolation();
            }
        }
        return $shortcode->buildBlock($attributes);
    }

    protected function filterInterpolationForPostId(): void
    {
        add_filter('site-reviews/interpolate/reviews', function ($context) {
            $context['class'] = 'block-editor-warning';
            $context['reviews'] = glsr(Builder::class)->p([
                'class' => 'block-editor-warning__message',
                'text' => _x('Enter a valid Review Post ID to display a review.', 'admin-text', 'site-reviews'),
            ]);
            return $context;
        });
    }
}
