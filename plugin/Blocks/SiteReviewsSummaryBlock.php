<?php

namespace GeminiLabs\SiteReviews\Blocks;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode as Shortcode;

class SiteReviewsSummaryBlock extends BlockGenerator
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
            'schema' => [
                'default' => false,
                'type' => 'boolean',
            ],
            'type' => [
                'default' => 'local',
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
        $shortcode = glsr(Shortcode::class);
        if ('edit' == filter_input(INPUT_GET, 'context')) {
            $attributes = $this->normalize($attributes);
            $this->filterShortcodeClass();
            if (!$this->hasVisibleFields($shortcode, $attributes)) {
                $this->filterInterpolation();
            }
        }
        return $shortcode->buildShortcode($attributes);
    }

    /**
     * @return void
     */
    protected function filterInterpolation()
    {
        add_filter('site-reviews/interpolate/reviews-summary', function ($context) {
            $context['class'] = 'glsr-default glsr-block-disabled';
            $context['text'] = __('You have hidden all of the fields for this block.', 'site-reviews');
            return $context;
        });
    }

    /**
     * @return void
     */
    protected function filterShortcodeClass()
    {
        add_filter('site-reviews/style', function () {
            return 'default';
        });
    }
}
