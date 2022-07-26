<?php

namespace GeminiLabs\SiteReviews\Integrations\DuplicatePost;

use GeminiLabs\SiteReviews\Contracts\HooksContract;

class Hooks implements HooksContract
{
    /**
     * @var Controller
     */
    public $controller;

    public function __construct()
    {
        $this->controller = glsr(Controller::class);
    }

    /**
     * @return void
     */
    public function run()
    {
        add_action('duplicate_post_post_copy', [$this->controller, 'duplicateReview'], 10, 2);
        add_filter('bulk_actions-edit-'.glsr()->post_type, [$this->controller, 'removeRewriteBulkAction'], 100);
        add_action('post_submitbox_start', [$this->controller, 'removeRewriteEditorLink'], 1);
        add_filter('post_row_actions', [$this->controller, 'removeRewriteRowAction'], 100, 2);
    }
}
