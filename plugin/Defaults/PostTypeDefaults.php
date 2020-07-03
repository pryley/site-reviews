<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Controllers\RestReviewController;
use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class PostTypeDefaults extends Defaults
{
    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'capabilities' => [
                'create_posts' => 'create_'.glsr()->post_type,
            ],
            'capability_type' => glsr()->post_type,
            'exclude_from_search' => true,
            'has_archive' => false,
            'hierarchical' => false,
            'labels' => [],
            'menu_icon' => 'dashicons-star-half',
            'menu_position' => 25,
            'map_meta_cap' => true,
            'public' => false,
            'query_var' => true,
            'rest_controller_class' => RestReviewController::class,
            'rewrite' => ['with_front' => false],
            'show_in_menu' => true,
            'show_in_rest' => true,
            'show_ui' => true,
            'supports' => ['title', 'editor', 'revisions'],
            'taxonomies' => [],
        ];
    }
}
