<?php

namespace GeminiLabs\SiteReviews\Blocks;

use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class SiteReviewsFormBlock extends Block
{
    public function attributes(): array
    {
        return [
            'assign_to' => [
                'default' => '',
                'type' => 'string',
            ],
            'assigned_posts' => [
                'default' => '',
                'type' => 'string',
            ],
            'assigned_terms' => [
                'default' => '',
                'type' => 'string',
            ],
            'assigned_users' => [
                'default' => '',
                'type' => 'string',
            ],
            'category' => [
                'default' => '',
                'type' => 'string',
            ],
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
            'reviews_id' => [
                'default' => '',
                'type' => 'string',
            ],
            'user' => [
                'default' => '',
                'type' => 'string',
            ],
        ];
    }

    public function render(array $attributes): string
    {
        $attributes['class'] = $attributes['className'];
        $shortcode = glsr(SiteReviewsFormShortcode::class);
        if ('edit' === filter_input(INPUT_GET, 'context')) {
            if (!$this->hasVisibleFields($shortcode, $attributes)) {
                return $this->buildEmptyBlock(
                    _x('You have hidden all of the fields for this block.', 'admin-text', 'site-reviews')
                );
            }
        }
        return $shortcode->buildBlock($attributes);
    }
}
