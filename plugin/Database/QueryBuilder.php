<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Helper;
use WP_Query;

class QueryBuilder
{
	/**
	 * Build a WP_Query meta_query/tax_query
	 * @return array
	 */
	public function buildQuery( array $keys = [], array $values = [] )
	{
		$queries = [];
		foreach( $keys as $key ) {
			if( !array_key_exists( $key, $values ))continue;
			$methodName = glsr( Helper::class )->buildMethodName( $key, __FUNCTION__ );
			if( !method_exists( $this, $methodName ))continue;
			$query = call_user_func( [$this, $methodName], $values[$key] );
			if( is_array( $query )) {
				$queries[] = $query;
			}
		}
		return $queries;
	}

	/**
	 * Build a SQL 'OR' string from an array
	 * @param string|array $values
	 * @param string $sprintfFormat
	 * @return string
	 */
	public function buildSqlOr( $values, $sprintfFormat )
	{
		if( !is_array( $values )) {
			$values = explode( ',', $values );
		}
		$values = array_filter( array_map( 'trim', (array)$values ));
		$values = array_map( function( $value ) use( $sprintfFormat ) {
			return sprintf( $sprintfFormat, $value );
		}, $values );
		return implode( ' OR ', $values );
	}

	/**
	 * Search SQL filter for matching against post title only.
	 * @link http://wordpress.stackexchange.com/a/11826/1685
	 * @param string $search
	 * @return string
	 * @filter posts_search
	 */
	public function filterSearchByTitle( $search, WP_Query $query )
	{
		if( empty( $search ) || empty( $query->get( 'search_terms' ))) {
			return $search;
		}
		global $wpdb;
		$n = empty( $query->get( 'exact' ))
			? '%'
			: '';
		$search = [];
		foreach( (array)$query->get( 'search_terms' ) as $term ) {
			$search[] = $wpdb->prepare( "{$wpdb->posts}.post_title LIKE %s", $n.$wpdb->esc_like( $term ).$n );
		}
		if( !is_user_logged_in() ) {
			$search[] = "{$wpdb->posts}.post_password = ''";
		}
		return ' AND '.implode( ' AND ', $search );
	}

	/**
	 * Get the current page number from the global query
	 * @param bool $isEnabled
	 * @return int
	 */
	public function getPaged( $isEnabled = true )
	{
		$pagedQuery = !is_front_page()
			? Application::PAGED_QUERY_VAR
			: 'page';
		return $isEnabled
			? max( 1, intval( get_query_var( $pagedQuery )))
			: 1;
	}

	/**
	 * @param string $value
	 * @return void|array
	 */
	protected function buildQueryAssignedTo( $value )
	{
		if( empty( $value ))return;
		return [
			'compare' => 'IN',
			'key' => 'assigned_to',
			'value' => array_filter( array_map( 'trim', explode( ',', $value )), 'is_numeric' ),
		];
	}

	/**
	 * @param array $value
	 * @return void|array
	 */
	protected function buildQueryCategory( $value )
	{
		if( empty( $value ))return;
		return [
			'field' => 'term_id',
			'taxonomy' => Application::TAXONOMY,
			'terms' => $value,
		];
	}

	/**
	 * @param string $value
	 * @return void|array
	 */
	protected function buildQueryRating( $value )
	{
		if( !is_numeric( $value ) || !in_array( intval( $value ), range( 1, 5 )))return;
		return [
			'compare' => '>=',
			'key' => 'rating',
			'value' => $value,
		];
	}

	/**
	 * @param string $value
	 * @return void|array
	 */
	protected function buildQueryType( $value )
	{
		if( in_array( $value, ['','all'] ))return;
		return [
			'key' => 'review_type',
			'value' => $value,
		];
	}
}
