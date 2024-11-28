<?php

namespace GeminiLabs\SiteReviews\Blocks;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Review;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class SiteReviewBlock extends SiteReviewsBlock
{
    public function render(array $attributes): string
    {
        $attributes['class'] = $attributes['className'];
        if ('edit' === filter_input(INPUT_GET, 'context')) {
            $attributes = $this->normalize($attributes);
            if (0 !== Cast::toInt($attributes['post_id']) && !Review::isReview($attributes['post_id'])) {
                return $this->buildEmptyBlock(
                    _x('Enter a valid Review Post ID to display a review.', 'admin-text', 'site-reviews')
                );
            }
            if (!$this->hasVisibleFields($attributes)) {
                return $this->buildEmptyBlock(
                    _x('You have hidden all of the fields for this block.', 'admin-text', 'site-reviews')
                );
            }
        }
        return $this->shortcode()->buildBlock($attributes);
    }

    public function shortcode(): ShortcodeContract
    {
        return glsr(SiteReviewShortcode::class);
    }
}
