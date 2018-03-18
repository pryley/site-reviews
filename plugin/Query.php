<?php

/**
 * This class interacts with the global WP_Query and/or builds SQL/WP_Query strings
 *
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2017, Paul Ryley
 * @license   GPLv3
 * @since     2.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\App;
use WP_Query;

class Query
{
	/**
	 * @var App
	 */
	protected $app;

	public function __construct( App $app )
	{
		$this->app = $app;
	}

	/**
	 * Build a WP_Query meta_query
	 *
	 * @return array
	 */
	public function buildMeta( array $meta = [] )
	{
		$metaQuery = [];

		foreach( $meta as $key => $query ) {
			if( $key == 'assigned_to' && !empty( $query['value'] )) {
				$metaQuery[] = $query;
			}
			if( $key == 'type'
				&& !in_array( $query['value'], ['','all'] )) {
				$metaQuery[] = $query;
			}
			if( $key == 'rating'
				&& is_numeric( $query['value'] )
				&& in_array((int) $query['value'], range(1,5))) {
				$metaQuery[] = $query;
			}
		}

		return $metaQuery;
	}

	/**
	 * Build a SQL 'OR' string from an array
	 *
	 * $values can either be an array or a comma-separated string
	 *
	 * @param string|array $values
	 *
	 * @return string
	 */
	public function buildSqlOr( $values, $sprintfString )
	{
		if( !is_array( $values )) {
			$values = explode( ',', $values );
		}
		$values = array_filter( array_map( 'trim', (array) $values ));
		$values = array_map( function( $value ) use( $sprintfString ) {
			return sprintf( $sprintfString, $value );
		}, $values );
		return implode( ' OR ', $values );
	}

	/**
	 * Build a WP_Query tax_query from a term ID array
	 *
	 * @return array
	 */
	public function buildTerms( array $terms = [] )
	{
		$query = [];

		if( !empty( $terms )) {
			$query[] = [
				'taxonomy' => App::TAXONOMY,
				'field'    => 'term_id',
				'terms'    => $terms,
			];
		}

		return $query;
	}

	/**
	 * Search SQL filter for matching against post title only.
	 *
	 * @link http://wordpress.stackexchange.com/a/11826/1685
	 * @param string $search
	 * @return string
	 * @filter posts_search
	 */
	public function filterSearchByTitle( $search, WP_Query $query )
	{
		if( !empty( $search ) && !empty( $query->query_vars['search_terms'] )) {
			global $wpdb;
			$q = $query->query_vars;
			$n = !empty( $q['exact'] ) ? '' : '%';
			$search = [];
			foreach( (array) $q['search_terms'] as $term ) {
				$search[] = $wpdb->prepare( "$wpdb->posts.post_title LIKE %s", $n.$wpdb->esc_like( $term ).$n );
			}
			if( !is_user_logged_in() ) {
				$search[] = "$wpdb->posts.post_password = ''";
			}
			$search = ' AND '.implode( ' AND ', $search );
		}
		return $search;
	}

	/**
	 * Get the current page number from the global query
	 *
	 * @return int
	 */
	public function getPaged()
	{
		return max( 1, intval( get_query_var(( is_front_page() ? 'page' : App::PAGED_QUERY_VAR ))));
	}
}
