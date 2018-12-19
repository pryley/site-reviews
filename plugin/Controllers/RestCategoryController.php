<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use WP_Error;
use WP_REST_Request as Request;
use WP_REST_Response as Response;
use WP_REST_Server as Server;
use WP_REST_Term_Meta_Fields;
use WP_REST_Terms_Controller as RestController;

class RestCategoryController extends RestController
{
	public function __construct()
	{
		$this->meta = new WP_REST_Term_Meta_Fields( Application::TAXONOMY );
		$this->namespace = Application::ID.'/v1';
		$this->rest_base = 'categories';
		$this->taxonomy = Application::TAXONOMY;
	}

	/**
	 * @return void
	 */
	public function register_routes()
	{
		register_rest_route( $this->namespace, '/'.$this->rest_base, [
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
