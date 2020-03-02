<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Commands\RegisterPostType;
use GeminiLabs\SiteReviews\Commands\RegisterShortcodes;
use GeminiLabs\SiteReviews\Commands\RegisterTaxonomy;
use GeminiLabs\SiteReviews\Commands\RegisterWidgets;

class MainController extends Controller
{
    /**
     * @return void
     * @action admin_footer
     * @action wp_footer
     */
    public function logOnce()
    {
        glsr_log()->logOnce();
    }

    /**
     * @return void
     * @action init
     */
    public function registerPostType()
    {
        if (!glsr()->hasPermission()) {
            return;
        }
        $command = new RegisterPostType([
            'capabilities' => ['create_posts' => 'create_'.Application::POST_TYPE],
            'capability_type' => Application::POST_TYPE,
            'columns' => [
                'title' => '',
                'category' => '',
                'assigned_to' => _x('Assigned To', 'admin-text', 'site-reviews'),
                'reviewer' => _x('Author', 'admin-text', 'site-reviews'),
                'email' => _x('Email', 'admin-text', 'site-reviews'),
                'ip_address' => _x('IP Address', 'admin-text', 'site-reviews'),
                'response' => _x('Response', 'admin-text', 'site-reviews'),
                'review_type' => _x('Type', 'admin-text', 'site-reviews'),
                'rating' => _x('Rating', 'admin-text', 'site-reviews'),
                'pinned' => _x('Pinned', 'admin-text', 'site-reviews'),
                'date' => '',
            ],
            'menu_icon' => 'dashicons-star-half',
            'menu_name' => glsr()->name,
            'map_meta_cap' => true,
            'plural' => _x('Reviews', 'admin-text', 'site-reviews'),
            'post_type' => Application::POST_TYPE,
            'rest_controller_class' => RestReviewController::class,
            'show_in_rest' => true,
            'single' => _x('Review', 'admin-text', 'site-reviews'),
        ]);
        $this->execute($command);
    }

    /**
     * @return void
     * @action init
     */
    public function registerShortcodes()
    {
        $command = new RegisterShortcodes([
            'site_reviews',
            'site_reviews_form',
            'site_reviews_summary',
        ]);
        $this->execute($command);
    }

    /**
     * @return void
     * @action init
     */
    public function registerTaxonomy()
    {
        $command = new RegisterTaxonomy([
            'hierarchical' => true,
            'meta_box_cb' => [glsr(EditorController::class), 'renderTaxonomyMetabox'],
            'public' => false,
            'rest_controller_class' => RestCategoryController::class,
            'show_admin_column' => true,
            'show_in_rest' => true,
            'show_ui' => true,
        ]);
        $this->execute($command);
    }

    /**
     * @return void
     * @action widgets_init
     */
    public function registerWidgets()
    {
        $command = new RegisterWidgets([
            'site-reviews' => [
                'description' => _x('Site Reviews: Display your recent reviews.', 'admin-text', 'site-reviews'),
                'name' => _x('Recent Reviews', 'admin-text', 'site-reviews'),
            ],
            'site-reviews-form' => [
                'description' => _x('Site Reviews: Display a form to submit reviews.', 'admin-text', 'site-reviews'),
                'name' => _x('Submit a Review', 'admin-text', 'site-reviews'),
            ],
            'site-reviews-summary' => [
                'description' => _x('Site Reviews: Display a summary of your reviews.', 'admin-text', 'site-reviews'),
                'name' => _x('Summary of Reviews', 'admin-text', 'site-reviews'),
            ],
        ]);
        $this->execute($command);
    }
}
