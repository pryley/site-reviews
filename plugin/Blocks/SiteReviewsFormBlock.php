<?php

namespace GeminiLabs\SiteReviews\Blocks;

use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class SiteReviewsFormBlock extends Block
{
    /**
     * @return array
     */
    public function attributes()
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

    /**
     * @return string
     */
    public function render(array $attributes)
    {
        $attributes['class'] = $attributes['className'];
        $shortcode = glsr(SiteReviewsFormShortcode::class);
        if ('edit' === filter_input(INPUT_GET, 'context')) {
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
        add_filter('site-reviews/interpolate/reviews-form', function ($context) {
            $context['class'] = 'block-editor-warning';
            $context['fields'] = glsr(Builder::class)->p([
                'class' => 'block-editor-warning__message',
                'text' => _x('You have hidden all of the fields for this block.', 'admin-text', 'site-reviews'),
            ]);
            $context['response'] = '';
            $context['submit_button'] = '';
            return $context;
        });
    }
}
