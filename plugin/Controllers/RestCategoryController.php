<?php

namespace GeminiLabs\SiteReviews\Controllers;

use WP_REST_Server as Server;
use WP_REST_Term_Meta_Fields;
use WP_REST_Terms_Controller as RestController;

class RestCategoryController extends RestController
{
    public function __construct()
    {
        $this->meta = new WP_REST_Term_Meta_Fields(glsr()->taxonomy);
        $this->namespace = glsr()->id.'/v1';
        $this->rest_base = 'categories';
        $this->taxonomy = glsr()->taxonomy;
    }

    /**
     * @return void
     */
    public function register_routes()
    {
        register_rest_route($this->namespace, '/'.$this->rest_base, [
            [
                'args' => $this->get_collection_params(),
                'callback' => [$this, 'get_items'],
                'methods' => Server::READABLE,
                'permission_callback' => [$this, 'get_items_permissions_check'],
            ],
            'schema' => [$this, 'get_public_item_schema'],
        ]);
    }
}
