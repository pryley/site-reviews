<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use WP_REST_Post_Meta_Fields;
use WP_REST_Posts_Controller as RestController;

class RestReviewController extends RestController
{
	public function __construct() {
		$this->meta = new WP_REST_Post_Meta_Fields( Application::POST_TYPE );
		$this->namespace = Application::ID.'/v1';
		$this->post_type = Application::POST_TYPE;
		$this->rest_base = 'reviews';
	}

	public function register_routes()
	{
		parent::register_routes();
		register_rest_route( $this->namespace, '/types', [
			'methods' => 'GET',
			'callback' => [$this, 'getReviewTypes'],
		]);
	}

	/**
	 * @return array
	 */
	public function getReviewTypes()
	{
		$types = [];
		foreach( glsr()->reviewTypes as $slug => $name ) {
			$types[] = [
				'name' => $name,
				'slug' => $slug,
			];
		}
		return $types;
	}
}
