<?php

namespace GeminiLabs\SiteReviews\Blocks;

use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class SiteReviewsSummaryBlock extends Block
{
    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'assigned_to' => [
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
            'post_id' => [
                'default' => '',
                'type' => 'string',
            ],
            'rating' => [
                'default' => '1',
                'type' => 'number',
            ],
            'rating_field' => [
                'default' => '',
                'type' => 'string',
            ],
            'schema' => [
                'default' => false,
                'type' => 'boolean',
            ],
            'terms' => [
                'default' => '',
                'type' => 'string',
            ],
            'type' => [
                'default' => 'local',
                'type' => 'string',
            ],
            'user' => [
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
        $shortcode = glsr(SiteReviewsSummaryShortcode::class);
        if ('edit' == filter_input(INPUT_GET, 'context')) {
            $attributes = $this->normalize($attributes);
            if (!$this->hasVisibleFields($shortcode, $attributes)) {
                $this->filterInterpolation();
            }
        }
        return $shortcode->buildBlock($attributes);
    }

    /**
     * @return void
     */
    protected function filterInterpolation()
    {
        add_filter('site-reviews/interpolate/reviews-summary', function ($context) {
            $context['class'] = 'block-editor-warning';
            $context['text'] = glsr(Builder::class)->p([
                'class' => 'block-editor-warning__message',
                'text' => _x('You have hidden all of the fields for this block.', 'admin-text', 'site-reviews'),
            ]);
            return $context;
        });
    }
}
