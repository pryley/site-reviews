<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\App;
use GeminiLabs\SiteReviews\Database\Options;
use GeminiLabs\SiteReviews\Database\OptionsContract;
use GeminiLabs\SiteReviews\Commands\SubmitReview;
use WP_Query;

class Database implements OptionsContract
{
	use Options;

	/**
	 * @var App
	 */
	protected $app;

	public function __construct( App $app )
	{
		$this->app = $app;
	}

	/**
	 * Save a review to the database
	 *
	 * @param SubmitReview $command
	 * @return int|bool
	 */
	public function createReview( array $meta, $command )
	{
		// make sure we set post_meta fallback defaults
		$meta = wp_parse_args( $meta, [
			'author'      => '',
			'assigned_to' => '',
			'avatar'      => '',
			'content'     => '',
			'date'        => current_time( 'mysql' ),
			'email'       => '',
			'ip_address'  => '',
			'pinned'      => false,
			'rating'      => '',
			'review_id'   => md5( time().serialize( $meta )),
			'review_type' => 'local',
			'title'       => '',
			'url'         => '',
		]);
		if( $post_id = $this->getReviewId( $meta['review_id'] )) {
			return $post_id;
		}
		$post_data = [
			'comment_status' => 'closed',
			'ID'             => $post_id,
			'ping_status'    => 'closed',
			'post_content'   => $meta['content'],
			'post_date'      => $meta['date'],
			'post_date_gmt'  => get_gmt_from_date( $meta['date'] ),
			'post_name'      => sprintf( '%s-%s', $meta['review_type'], $meta['review_id'] ),
			'post_status'    => 'publish',
			'post_title'     => $meta['title'],
			'post_type'      => App::POST_TYPE,
		];
		if( $meta['review_type'] == 'local' ) {
			if( $this->getOption( 'settings.general.require.approval' ) == 'yes' || $command->blacklisted ) {
				$post_data['post_status'] = 'pending';
			}
		}
		$post_id = wp_insert_post( $post_data, true );
		if( is_wp_error( $post_id )) {
			glsr_resolve( 'Log\Logger' )->error( sprintf( '%s (%s)', $post_id->get_error_message(), $meta['review_id'] ));
			return false;
		}
		// add post_meta
		foreach( $meta as $field => $value ) {
			update_post_meta( $post_id, $field, $value );
		}
		do_action( 'site-reviews/local/review/create', $post_data, $meta, $post_id );
		return $post_id;
	}

	/**
	 * Delete review based on a review_id meta value
	 *
	 * @param string $metaReviewId
	 * @return void
	 */
	public function deleteReview( $metaReviewId )
	{
		$postId = $this->getReviewId( $metaReviewId );
		if( !empty( $postId )) {
			wp_delete_post( $postId, true );
		}
	}

	/**
	 * @param \WP_Post|null $post
	 * @return null|object
	 */
	public function getReview( $post )
	{
		if( !isset( $post->ID )
			|| $post->post_type != App::POST_TYPE
		)return;
		$meta = $this->getReviewMeta( $post->ID );
		$modified = $post->post_title != $meta->title
			|| $post->post_content != $meta->content
			|| $post->post_date != $meta->date;
		$review = (object) [
			'assigned_to' => $meta->assigned_to,
			'author'      => $meta->author,
			'avatar'      => $meta->avatar,
			'content'     => $post->post_content,
			'date'        => $post->post_date,
			'email'       => $meta->email,
			'ID'          => $post->ID,
			'ip_address'  => $meta->ip_address,
			'modified'    => $modified,
			'pinned'      => $meta->pinned,
			'post_id'     => $post->ID, // provided for backwards compatability
			'rating'      => $meta->rating,
			'response'    => $meta->response,
			'review_id'   => $meta->review_id,
			'review_type' => $meta->review_type,
			'status'      => $post->post_status,
			'title'       => $post->post_title,
			'url'         => $meta->url,
			'user_id'     => $post->post_author,
		];
		return apply_filters( 'site-reviews/get/review', $review, $post );
	}

	/**
	 * @param string $metaKey
	 * @param string $metaValue
	 * @return int|array
	 */
	public function getReviewCount( $metaKey = '', $metaValue = '' )
	{
		$metaKey = $this->normalizeMetaKey( $metaKey );
		if( !$metaKey ) {
			return (array) wp_count_posts( App::POST_TYPE );
		}
		$counts = wp_cache_get( $this->app->id, $metaKey . '_count' );
		if( $counts === false ) {
			global $wpdb;
			$results = (array) $wpdb->get_results( $wpdb->prepare(
				"SELECT m.meta_value AS name, COUNT( * ) num_posts " .
				"FROM {$wpdb->posts} AS p " .
				"INNER JOIN {$wpdb->postmeta} AS m ON p.ID = m.post_id " .
				"WHERE p.post_type = '%s' " .
					"AND m.meta_key = '%s' " .
				"GROUP BY name",
				App::POST_TYPE,
				$metaKey
			));
			$counts = [];
			foreach( $results as $result ) {
				$counts[$result->name] = $result->num_posts;
			}
			wp_cache_set( $this->app->id, $counts, $metaKey . '_count' );
		}
		if( !$metaValue ) {
			return $counts;
		}
		return isset( $counts[$metaValue] ) ? $counts[$metaValue] : 0;
	}

	/**
	 * Get the review post ID from the review_id meta value
	 *
	 * @param string $metaReviewId
	 * @return int
	 */
	public function getReviewId( $metaReviewId )
	{
		global $wpdb;
		$query = $wpdb->prepare(
			"SELECT p.ID " .
			"FROM {$wpdb->posts} AS p " .
			"INNER JOIN {$wpdb->postmeta} AS m1 ON p.ID = m1.post_id " .
			"WHERE p.post_type = '%s' " .
				"AND m1.meta_key = 'review_id' " .
				"AND m1.meta_value = '%s'",
			App::POST_TYPE,
			$metaReviewId
		);
		return intval( $wpdb->get_var( $query ));
	}

	/**
	 * Gets an array of all saved review IDs by review type
	 *
	 * @param string $reviewType
	 * @return array
	 */
	public function getReviewIds( $reviewType )
	{
		global $wpdb;
		$query = $wpdb->prepare(
			"SELECT m1.meta_value AS review_id " .
			"FROM {$wpdb->posts} AS p " .
			"INNER JOIN {$wpdb->postmeta} AS m1 ON p.ID = m1.post_id " .
			"INNER JOIN {$wpdb->postmeta} AS m2 ON p.ID = m2.post_id " .
			"WHERE p.post_type = '%s' " .
				"AND m1.meta_key = 'review_id' " .
				"AND m2.meta_key = 'review_type' " .
				"AND m2.meta_value = '%s'",
			App::POST_TYPE,
			$reviewType
		);
		return array_keys( array_flip( $wpdb->get_col( $query )));
	}

	/**
	 * Get an object of meta values for a review
	 *
	 * @param int $postId
	 * @return object
	 */
	public function getReviewMeta( $postId )
	{
		$meta = get_post_type( $postId ) == App::POST_TYPE
			? array_map( 'array_shift', (array) get_post_meta( $postId ))
			: [];
		return (object) $this->normalizeMeta( array_filter( $meta, 'strlen' ));
	}

	/**
	 * Gets a object of saved review objects
	 *
	 * @return object
	 */
	public function getReviews( array $args = [] )
	{
		$defaults = [
			'assigned_to'  => '',
			'category'     => '',
			'count'        => 10,
			'offset'       => '',
			'order'        => 'DESC',
			'orderby'      => 'date',
			'pagination'   => false,
			'post__in'     => [],
			'post__not_in' => [],
			'rating'       => '',
			'type'         => '',
		];
		$args = shortcode_atts( $defaults, $args );
		extract( $args );
		$meta_query = $this->app->make( 'Query' )->buildMeta([
			'assigned_to' => [
				'key' => 'assigned_to',
				'value' => array_filter( array_map( 'trim', explode( ',', $assigned_to )), 'is_numeric' ),
				'compare' => 'IN',
			],
			'type' => [
				'key' => 'review_type',
				'value' => $type,
			],
			'rating' => [
				'key' => 'rating',
				'value' => $rating,
				'compare' => '>=',
			],
		]);
		$query = [
			'meta_key'       => 'pinned',
			'meta_query'     => $meta_query,
			'offset'         => $offset ? $offset : '',
			'order'          => $order,
			'orderby'        => "meta_value $orderby",
			'paged'          => $pagination ? $this->app->make( 'Query' )->getPaged() : 1,
			'post__in'       => $post__in,
			'post__not_in'   => $post__not_in,
			'post_status'    => 'publish',
			'post_type'      => App::POST_TYPE,
			'posts_per_page' => $count ? $count : -1,
			'tax_query'      => $this->app->make( 'Query' )->buildTerms( $this->normalizeTerms( $category )),
		];
		$reviews = new WP_Query( $query );
		return (object) [
			'reviews' => array_map( [$this, 'getReview'], $reviews->posts ),
			'max_num_pages' => $reviews->max_num_pages,
		];
	}

	/**
	 * Get array of meta values for all of a post_type
	 *
	 * @param string|array $keys
	 * @param string $status
	 * @return array
	 */
	public function getReviewsMeta( $keys, $status = 'publish' )
	{
		global $wpdb;
		$query = $this->app->make( 'Query' );
		$keys = array_map( [$this, 'normalizeMetaKey'], (array) $keys );
		if( $status == 'all' || empty( $status )) {
			$status = get_post_stati( ['exclude_from_search' => false ] );
		}
		$keys = $query->buildSqlOr( $keys, "pm.meta_key = '%s'" );
		$status = $query->buildSqlOr( $status, "p.post_status = '%s'" );
		$query = $wpdb->prepare(
			"SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm " .
			"LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id " .
			"WHERE p.post_type = '%s' " .
				"AND ({$keys}) " .
				"AND ({$status}) " .
			"ORDER BY pm.meta_value",
			App::POST_TYPE
		);
		return $wpdb->get_col( $query );
	}

	/**
	 * Gets the review types (default review_type is "local")
	 *
	 * @return array
	 */
	public function getReviewTypes()
	{
		global $wpdb;
		$types = $wpdb->get_col(
			"SELECT DISTINCT(meta_value) " .
			"FROM {$wpdb->postmeta} " .
			"WHERE meta_key = 'review_type' " .
			"ORDER BY meta_value ASC"
		);
		$types = array_flip( $types );
		$labels = $this->app->make( 'Strings' )->review_types();
		array_walk( $types, function( &$value, $key ) use( $labels ) {
			$type = array_key_exists( $key, $labels )
				? $labels[$key]
				: ucfirst( $key );

			$value = sprintf( __( '%s reviews', 'site-reviews' ), $type );
		});
		return $types;
	}

	/**
	 * Get an array of taxonomy terms
	 *
	 * @param string $taxonomy
	 * @return array
	 */
	public function getTerms( $taxonomy = '', array $args = [] )
	{
		!empty( $taxonomy ) ?: $taxonomy = App::TAXONOMY;
		$terms = get_terms( $taxonomy, wp_parse_args( $args, [
			'fields'     => 'id=>name',
			'hide_empty' => false,
		]));
		return is_array( $terms )
			? $terms
			: [];
	}

	/**
	 * Get all meta values for a review
	 *
	 * @return array
	 */
	public function normalizeMeta( array $meta )
	{
		$defaults = [
			'author'      => __( 'Anonymous', 'site-reviews' ),
			'assigned_to' => '',
			'avatar'      => '',
			'content'     => '',
			'date'        => '',
			'email'       => '',
			'ip_address'  => '',
			'pinned'      => '',
			'rating'      => '',
			'response'    => '',
			'review_id'   => '',
			'review_type' => '',
			'status'      => '',
			'title'       => '',
			'url'         => '',
		];
		return !empty( $meta )
			? shortcode_atts( $defaults, $meta )
			: [];
	}

	/**
	 * Normalize a review meta key
	 *
	 * @return string
	 */
	public function normalizeMetaKey( $metaKey )
	{
		$metaKey = strtolower( $metaKey );
		if( in_array( $metaKey, ['id', 'type'] )) {
			$metaKey = 'review_' . $metaKey;
		}
		return $metaKey;
	}

	/**
	 * Normalize a string of comma-separated terms into an array
	 *
	 * @param string $terms
	 * @param string $taxonomy
	 * @return array
	 */
	public function normalizeTerms( $terms, $taxonomy = '' )
	{
		!empty( $taxonomy ) ?: $taxonomy = App::TAXONOMY;
		$terms = array_map( 'trim', explode( ',', $terms ));
		$terms = array_map( function( $term ) use( $taxonomy ) {
			!is_numeric( $term ) ?: $term = intval( $term );
			$term = term_exists( $term, $taxonomy );
			if( isset( $term['term_id'] )) {
				return intval( $term['term_id'] );
			}
		}, $terms );
		return array_filter( $terms );
	}

	/**
	 * Reverts a review title, date, and content to the originally submitted values
	 *
	 * @param string $postId
	 * @return int
	 */
	public function revertReview( $postId )
	{
		$post = get_post( $postId );
		if( !isset( $post->post_type ) || $post->post_type != App::POST_TYPE ) {
			return 0;
		}
		delete_post_meta( $post->ID, '_edit_last' );
		return wp_update_post([
			'ID'           => $post->ID,
			'post_content' => get_post_meta( $post->ID, 'content', true ),
			'post_date'    => get_post_meta( $post->ID, 'date', true ),
			'post_title'   => get_post_meta( $post->ID, 'title', true ),
		]);
	}

	/**
	 * @param string $searchTerm
	 * @return string
	 */
	public function searchPosts( $searchTerm )
	{
		$args = [
			'post_type' => 'any',
			'post_status' => 'publish',
		];
		if( is_numeric( $searchTerm )) {
			$args['post__in'] = [$searchTerm];
		}
		else {
			$args['s'] = $searchTerm;
			$args['posts_per_page'] = 10;
			$args['orderby'] = 'relevance';
		}
		$results = '';
		$query = $this->app->make( 'Query' );
		add_filter( 'posts_search', [$query, 'filterSearchByTitle'], 500, 2 );
		$search = new WP_Query( $args );
		remove_filter( 'posts_search', [$query, 'filterSearchByTitle'], 500 );
		if( $search->have_posts() ) {
			while( $search->have_posts() ) {
				$search->the_post();
				ob_start();
				$this->app->make( 'Controllers\MainController' )->render( 'edit/search-result', [
					'ID' => get_the_ID(),
					'permalink' => esc_url( (string) get_permalink() ),
					'title' => esc_attr( get_the_title() ),
				]);
				$results .= ob_get_clean();
			}
			wp_reset_postdata();
		}
		return $results;
	}

	/**
	 * Set the default settings
	 *
	 * @return array
	 */
	public function setDefaults( array $args = [] )
	{
		$defaultSettings = $currentSettings = [];
		$defaults = [
			'data'   => null, // provide custom data instead of using defaults
			'merge'  => true, // merge defaults with existing saved settings
			'update' => true, // save generated defaults to database
		];
		$args = shortcode_atts( $defaults, $args );
		if( !is_array( $args['data'] )) {
			$args['data'] = $this->app->getDefaultSettings();
		}
		if( $args['merge'] ) {
			$currentSettings = $this->removeEmptyValuesFrom( $this->getOptions() );
		}
		foreach( $args['data'] as $path => $value ) {
			// Don't save the default selector values as they are used anyway by default.
			if( !!$args['update'] && strpos( $path, '.selectors.' ) !== false ) {
				$value = '';
			}
			$defaultSettings = $this->setValueToPath( $value, $path, $defaultSettings );
		}
		$settings = array_replace_recursive( $defaultSettings, $currentSettings );
		if( $args['update'] ) {
			$option = get_option( $this->getOptionName(), [] );
			$option = array_replace_recursive( (array) $option, $settings );
			update_option( $this->getOptionName(), $option );
		}
		return $settings;
	}

	/**
	 * Set one or more taxonomy terms to a post
	 *
	 * @param int $post_id
	 * @param string $terms
	 * @param string $taxonomy
	 * @return void
	 */
	public function setReviewMeta( $post_id, $terms, $taxonomy = '' )
	{
		!empty( $taxonomy ) ?: $taxonomy = App::TAXONOMY;
		$terms = $this->normalizeTerms( $terms, $taxonomy );
		if( !empty( $terms )) {
			$result = wp_set_object_terms( $post_id, $terms, $taxonomy );
			if( is_wp_error( $result )) {
				glsr_resolve( 'Log\Logger' )->error( sprintf( '%s (%s)', $result->get_error_message(), $taxonomy ));
			}
		}
	}
}
