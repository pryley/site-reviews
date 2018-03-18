<?php

/**
 * Shared shortcode/widget methods
 *
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Traits;

trait SiteReviews
{
	/**
	 * @param array|string $args
	 * @return array
	 */
	public function normalize( $args, array $defaults = [] )
	{
		$defaults = wp_parse_args( $defaults, [
			'assigned_to' => '',
			'category' => '',
			'class' => '',
			'count' => 5,
			'display' => '',
			'hide' => [],
			'id' => '',
			'rating' => 1,
			'title' => '',
			'type' => '',
		]);
		$args = shortcode_atts( $defaults, wp_parse_args( $args ));
		$args = $this->makeCompatible( $args );
		$args = $this->normalizeHiddenFields( $args );
		// "type" takes precedence over "display"
		if( empty( $args['type'] )) {
			$args['type'] = $args['display'];
		}
		if( $args['id'] ) {
			$args['id'] = sanitize_title( $args['id'] );
		}
		return $args;
	}

	/**
	 * @return
	 */
	public function renderReviews( array $args )
	{
		echo glsr_resolve( 'Html' )->renderPartial( 'reviews', $args );
	}

	/**
	 * Maintain backwards compatibility with version <= v1.2.1
	 *
	 * @return array
	 */
	protected function makeCompatible( array $args )
	{
		$hide = ['author','avatar','date','excerpt','rating','response','title'];
		$display = array_map( 'trim', explode( ',', $args['display'] ));

		if( count( array_intersect( $hide, $display )) > 0 ) {
			$args['hide'] = array_diff( $hide, $display );
			$args['display'] = '';
		}
		return $args;
	}

	/**
	 * @return array
	 */
	protected function normalizeHiddenFields( array $args )
	{
		if( !is_array( $args['hide'] )) {
			$args['hide'] = array_map( 'trim', array_filter( explode( ',', $args['hide'] )));
		}
		foreach( $args['hide'] as $key ) {
			$args['hide_'.$key] = true;
		}
		return $args;
	}
}
