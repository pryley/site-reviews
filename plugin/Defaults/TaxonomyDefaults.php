<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Controllers\Api\Version1\RestCategoryController;
use GeminiLabs\SiteReviews\Metaboxes\TaxonomyMetabox;

class TaxonomyDefaults extends DefaultsAbstract
{
    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'hierarchical' => true,
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
