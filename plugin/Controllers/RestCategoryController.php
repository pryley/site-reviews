<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use WP_REST_Term_Meta_Fields;
use WP_REST_Terms_Controller as Controller;

class RestCategoryController extends Controller
{
	public function __construct() {
		$this->meta = new WP_REST_Term_Meta_Fields( Application::TAXONOMY );
		$this->namespace = Application::ID.'/v1';
		$this->rest_base = 'categories';
		$this->taxonomy = Application::TAXONOMY;
	}
}
