<?php

namespace GeminiLabs\SiteReviews\Blocks;

use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Review;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class SiteReviewBlock extends SiteReviewsBlock
{
    public function attributes(): array
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

    public function render(array $attributes): string
    {
        $attributes['class'] = $attributes['className'];
        $shortcode = glsr(SiteReviewShortcode::class);
        if ('edit' === filter_input(INPUT_GET, 'context')) {
            $attributes = $this->normalize($attributes);
            $this->filterShowMoreLinks('content');
            $this->filterShowMoreLinks('response');
            if (0 !== Cast::toInt($attributes['post_id']) && !Review::isReview($attributes['post_id'])) {
                return $this->buildEmptyBlock(
                    _x('Enter a valid Review Post ID to display a review.', 'admin-text', 'site-reviews')
                );
            } elseif (!$this->hasVisibleFields($shortcode, $attributes)) {
                return $this->buildEmptyBlock(
                    _x('You have hidden all of the fields for this block.', 'admin-text', 'site-reviews')
                );
            }
        }
        return $shortcode->buildBlock($attributes);
    }
}
