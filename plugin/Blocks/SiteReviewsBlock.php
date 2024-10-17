<?php

namespace GeminiLabs\SiteReviews\Blocks;

use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class SiteReviewsBlock extends Block
{
    public function attributes(): array
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
            'post_id' => [ // This is used to store the Post Id of the page that we get with jQuery.
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

    public function render(array $attributes): string
    {
        $attributes['class'] = $attributes['className'];
        $shortcode = glsr(SiteReviewsShortcode::class);
        if ('edit' === filter_input(INPUT_GET, 'context')) {
            $attributes = $this->normalize($attributes);
            if (!$shortcode->hasVisibleFields($attributes)) {
                return $this->buildEmptyBlock(
                    _x('You have hidden all of the fields for this block.', 'admin-text', 'site-reviews')
                );
            }
            $this->filterShowMoreLinks('content');
            $this->filterShowMoreLinks('response');
        }
        return $shortcode->buildBlock($attributes);
    }

    protected function filterShowMoreLinks(string $field): void
    {
        add_filter("site-reviews/review/wrap/{$field}", function ($value) { // @phpstan-ignore-line
            $pattern = '/(.*)(<span class="glsr-hidden)(.*)(<\/span>)(.*)/us';
            $replace = '$1... <a href="#" tabindex="-1">'.__('Show more', 'site-reviews').'</a>$5';
            $value = preg_replace($pattern, $replace, $value);
            return $value;
        });
    }
}
