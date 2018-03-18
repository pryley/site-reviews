<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\App;

class RegisterPostType
{
	public $args;

	public function __construct( $input )
	{
		$columns = [
			'title' => __( 'Title', 'site-reviews' ),
			'date'  => __( 'Date', 'site-reviews' ),
		];

		$defaults = [
			'capability_type' => '',
			'columns'         => $columns,
			'has_archive'     => false,
			'hierarchical'    => false,
			'labels'           => [],
			'map_meta_cap'    => true,
			'menu_icon'       => null,
			'menu_name'       => '',
			'menu_position'   => 25,
			'public'          => true,
			'query_var'       => true,
			'rewrite'         => ['slug' => App::POST_TYPE, 'with_front' => false ],
			'show_in_menu'    => true, //'edit.php?post_type=post'
			'supports'        => ['title', 'editor'],
			'taxonomies'      => [],
		];

		$args = wp_parse_args( $input, $defaults );

		$defaults = [
			'exclude_from_search' => !$args['public'],
			'menu_name'           => sanitize_title( $args['plural'] ),
			'post_type'           => sanitize_title( $args['single'] ),
			'publicly_queryable'  => $args['public'],
			'show_in_nav_menus'   => $args['public'],
			'show_ui'             => true,
		];

		$args = wp_parse_args( $args, $defaults );

		$args['labels']['singular_name'] = $args['single'];
		$args['labels']['name'] = $args['plural'];
		$args['labels']['menu_name'] = $args['menu_name'];

		$args['columns'] = $this->normalizeColumns( $args['columns'] );

		$this->args = $args;
	}

	/**
	 * @return array
	 */
	protected function normalizeColumns( array $columns )
	{
		if( array_key_exists( 'category', $columns )) {

			$keys = array_keys( $columns );

			$keys[ array_search( 'category', $keys ) ] = sprintf( 'taxonomy-%s', App::TAXONOMY );

			$columns = array_combine( $keys, $columns );
		}

		return $columns;
	}
}
