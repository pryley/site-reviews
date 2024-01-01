<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Controllers\Api\Version1\RestCategoryController;
use GeminiLabs\SiteReviews\Metaboxes\TaxonomyMetabox;

class TaxonomyDefaults extends DefaultsAbstract
{
    protected function defaults(): array
    {
        return [
            'hierarchical' => true,
            'labels' => [
                'item_link' => _x('Review Category Link', 'admin-text', 'site-reviews'),
                'item_link_description' => _x('A link to a review category.', 'admin-text', 'site-reviews'),
                'menu_name' => _x('Categories', 'Admin menu name (admin-text)', 'site-reviews'),
                'name' => _x('Review Categories', 'admin-text', 'site-reviews'),
            ],
            'meta_box_cb' => [glsr(TaxonomyMetabox::class), 'render'],
            'public' => false,
            'rest_controller_class' => RestCategoryController::class,
            'show_admin_column' => true,
            'show_in_rest' => true,
            'show_ui' => true,
            'capabilities' => [
                'assign_terms' => 'assign_site-review_terms',
                'delete_terms' => 'delete_site-review_terms',
                'edit_terms' => 'edit_site-review_terms',
                'manage_terms' => 'manage_site-review_terms',
            ],
        ];
    }
}
