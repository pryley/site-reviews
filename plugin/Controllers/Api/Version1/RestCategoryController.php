<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1;

class RestCategoryController extends \WP_REST_Terms_Controller
{
    public function __construct()
    {
        $obj = get_taxonomy(glsr()->taxonomy);
        $this->meta = new \WP_REST_Term_Meta_Fields(glsr()->taxonomy);
        $this->namespace = !empty($obj->rest_namespace) ? $obj->rest_namespace : glsr()->id.'/v1';
        $this->rest_base = 'categories';
        $this->taxonomy = glsr()->taxonomy;
    }
}
