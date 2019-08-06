<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use WP_Error;
use WP_REST_Post_Meta_Fields;
use WP_REST_Posts_Controller as RestController;
use WP_REST_Request as Request;
use WP_REST_Response as Response;
use WP_REST_Server as Server;

class RestReviewController extends RestController
{
    public function __construct()
    {
        $this->meta = new WP_REST_Post_Meta_Fields(Application::POST_TYPE);
        $this->namespace = Application::ID.'/v1';
        $this->post_type = Application::POST_TYPE;
        $this->rest_base = 'reviews';
    }

    /**
     * @return void
     */
    public function register_routes()
    {
        // register_rest_route($this->namespace, '/'.$this->rest_base, [
        //  ['callback' => [$this, 'createReview'], 'methods' => Server::CREATABLE],
        //  ['callback' => [$this, 'getReviews'], 'methods' => Server::READABLE],
        // ]);
        register_rest_route($this->namespace, '/types', [
            ['callback' => [$this, 'getReviewTypes'], 'methods' => Server::READABLE],
        ]);
    }

    /**
     * @return WP_Error|Response|mixed
     */
    public function createReview()
    {
        $response = [];
        return rest_ensure_response($response);
    }

    /**
     * @return WP_Error|Response|mixed
     */
    public function getReviews()
    {
        $response = [];
        return rest_ensure_response($response);
    }

    /**
     * @return WP_Error|Response|mixed
     */
    public function getReviewTypes()
    {
        $response = [];
        foreach (glsr()->reviewTypes as $slug => $name) {
            $response[] = [
                'name' => $name,
                'slug' => $slug,
            ];
        }
        return rest_ensure_response($response);
    }
}
