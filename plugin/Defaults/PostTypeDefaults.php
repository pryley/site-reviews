<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class PostTypeDefaults extends Defaults
{
	/**
	 * @return array
	 */
	protected function defaults()
	{
		return [
			'columns' => [
				'title' => __( 'Title', 'site-reviews' ),
				'date' => __( 'Date', 'site-reviews' ),
			],
			'has_archive' => false,
			'hierarchical' => false,
			'labels' => [],
			'menu_icon' => null,
			'menu_name' => '',
			'menu_position' => 25,
			'plural' => '', //Required
			'post_type' => '', //Required
			'public' => false,
			'query_var' => true,
			'rewrite' => ['with_front' => false],
			'show_in_menu' => true,
			'show_ui' => true,
			'single' => '', //Required
			'supports' => ['title', 'editor'],
			'taxonomies' => [],
		];
	}
}
