<?php

namespace GeminiLabs\SiteReviews\Blocks;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode as Shortcode;

class SiteReviewsBlock extends BlockGenerator
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
            'display' => [
                'default' => 5,
                'type' => 'number',
            ],
            'hide' => [
                'default' => '',
                'type' => 'string',
            ],
            'id' => [
                'default' => '',
                'type' => 'string',
            ],
            'pagination' => [
                'default' => '',
                'type' => 'string',
            ],
            'post_id' => [
                'default' => '',
                'type' => 'string',
            ],
            'rating' => [
                'default' => 0,
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
            $this->filterReviewLinks();
            $this->filterShortcodeClass();
            $this->filterShowMoreLinks('content');
            $this->filterShowMoreLinks('response');
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
        add_filter('site-reviews/interpolate/reviews', function ($context) {
            $context['class'] = 'glsr-default glsr-block-disabled';
            $context['reviews'] = __('You have hidden all of the fields for this block.', 'site-reviews');
            return $context;
        });
    }

    /**
     * @return void
     */
    protected function filterReviewLinks()
    {
        add_filter('site-reviews/rendered/template/reviews', function ($template) {
            return str_replace('<a', '<a tabindex="-1"', $template);
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

    /**
     * @param string $field
     * @return void
     */
    protected function filterShowMoreLinks($field)
    {
        add_filter('site-reviews/review/wrap/'.$field, function ($value) {
            $value = preg_replace(
                '/(.*)(<span class="glsr-hidden)(.*)(<\/span>)(.*)/us',
                '$1... <a href="#" class="glsr-read-more" tabindex="-1">'.__('Show more', 'site-reviews').'</a>$5',
                $value
            );
            return $value;
        });
    }
}
