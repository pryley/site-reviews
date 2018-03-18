<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\App;
use GeminiLabs\SiteReviews\Commands\RegisterPostType as Command;
use WP_Screen;
use WP_Query;

class RegisterPostType
{
	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @var array
	 */
	protected $columns;

	/**
	 * @var \GeminiLabs\SiteReviews\Database
	 */
	protected $db;

	public function __construct( App $app )
	{
		$this->app = $app;
		$this->db  = $app->make( 'Database' );
	}

	/**
	 * @return void
	 */
	public function handle( Command $command )
	{
		extract( $command->args );

		$post_type = App::POST_TYPE;

		if( in_array( $post_type, get_post_types( ['_builtin' => true ] )))return;

		$this->columns = $columns;

		$args = [
			'description'         => '',
			'labels'              => $labels,
			'taxonomies'          => $taxonomies,
			'supports'            => $supports,
			'map_meta_cap'        => $map_meta_cap,
			'menu_position'       => $menu_position,
			'menu_icon'           => $menu_icon,
			'has_archive'         => $has_archive,
			'public'              => $public,
			'show_in_nav_menus'   => $show_in_nav_menus,
			'show_ui'             => $show_ui,
			'exclude_from_search' => $exclude_from_search,
			'publicly_queryable'  => $publicly_queryable,
			'capabilities'        => ['create_posts' => "create_{$post_type}"],
			'hierarchical'        => $hierarchical,
			'rewrite'             => $rewrite,
			'query_var'           => $query_var,
		];

		register_post_type( $post_type, $args );

		add_action( 'restrict_manage_posts',                   [ $this, 'printColumnFilters'] );
		add_action( "manage_{$post_type}_posts_custom_column", [ $this, 'printColumnValues'] );
		add_action( 'pre_get_posts',                           [ $this, 'setColumnQuery'] );

		add_filter( "manage_{$post_type}_posts_columns",         [ $this, 'modifycolumns'] );
		add_filter( 'default_hidden_columns',                    [ $this, 'modifyColumnsHidden'], 10, 2 );
		add_filter( "manage_edit-{$post_type}_sortable_columns", [ $this, 'modifyColumnsSortable'] );
	}

	/**
	 * Creates the custom post_type columns
	 *
	 * @return array
	 *
	 * @filter manage_{$post_type}_posts_columns
	 */
	public function modifyColumns( array $columns )
	{
		$this->columns = ['cb' => ''] + $this->columns;

		array_walk( $this->columns, function( &$value, $key ) use ( $columns ) {
			if( array_key_exists( $key, $columns ) && empty( $value )) {
				$value = $columns[ $key ];
			}
			else if( $key === 'sticky' ) {
				// wrap in <span> so we can replace with a dashicon in CSS @media
				$value = "<span class=\"pinned-icon\">{$value}</span>";
				// WP < 4.4 support
				global $wp_version;
				if( version_compare( $wp_version, '4.4', '<' )) {
					$value .= file_get_contents( "{$this->app->path}assets/img/pinned.svg" );
				}
			}
		});

		$types = $this->db->getReviewsMeta( 'type' );

		if( count( $types ) < 1 || ( count( $types ) == 1 && $types[0] == 'local' )) {
			unset( $this->columns['type'] );
		}

		// remove all keys with null, false, or empty values
		return array_filter( $this->columns, 'strlen' );
	}

	/**
	 * Filters the default list of hidden columns
	 *
	 * @return array
	 *
	 * @filter default_hidden_columns
	 */
	public function modifyColumnsHidden( array $hidden, WP_Screen $screen )
	{
		if( $screen->id == sprintf( 'edit-%s', App::POST_TYPE )) {
			$hidden = ['reviewer'];
		}

		return $hidden;
	}

	/**
	 * Sets which custom post_type columns are sortable
	 *
	 * @return array
	 *
	 * @filter manage_edit-{$post_type}_sortable_columns
	 */
	public function modifyColumnsSortable( array $columns )
	{
		$columns['assigned_to'] = 'assigned_to';
		$columns['reviewer']    = 'reviewer';
		$columns['stars']       = 'rating';
		$columns['sticky']      = 'pinned';
		$columns['type']        = 'review_type';

		return $columns;
	}

	/**
	 * Prints the column filters
	 *
	 * @param string $post_type
	 *
	 * @return void
	 *
	 * @action restrict_manage_posts
	 */
	public function printColumnFilters( $post_type )
	{
		// WP < 4.4 support
		if( !$post_type ) {
			$post_type = get_current_screen()->post_type;
		}

		if( $post_type !== App::POST_TYPE )return;

		$status = filter_input( INPUT_GET, 'post_status' );
		$status ?: $status = 'publish';

		$ratings = $this->db->getReviewsMeta( 'rating', $status );
		$types   = $this->db->getReviewsMeta( 'type', $status );

		$this->renderFilterRatings( $ratings );
		$this->renderFilterTypes( $types );
	}

	/**
	 * Prints the custom column values
	 *
	 * @param string $column
	 *
	 * @return void
	 *
	 * @action manage_{$post_type}_posts_custom_column
	 */
	public function printColumnValues( $column )
	{
		global $wp_version;
		$method = $this->app->make( 'Helper' )->buildMethodName( $column, 'buildColumn' );
		echo !method_exists( $this, $method )
			? apply_filters( 'site-reviews/columns/'.$column, '' )
			: call_user_func([ $this, $method ]);
	}

	/**
	 * Sets the WP_Query
	 *
	 * @return void
	 *
	 * @action pre_get_posts
	 */
	public function setColumnQuery( WP_Query $query )
	{
		if( !$this->hasPermission( $query ))return;

		$this->setMeta( $query, [
			'rating',
			'review_type',
		]);

		$this->setOrderby( $query );
	}

	/**
	 * @return string
	 */
	protected function buildColumnAssignedTo()
	{
		$value = $this->app->make( 'Html' )->renderPartial( 'link', [
			'post_id' => $this->db->getReviewMeta( get_post()->ID )->assigned_to,
		]);
		return $value ? $value : '&mdash;';
	}

	/**
	 * @return string
	 */
	protected function buildColumnReviewer()
	{
		return $this->db->getReviewMeta( get_post()->ID )->author;
	}

	/**
	 * @return string
	 */
	protected function buildColumnStars()
	{
		return $this->app->make( 'Html' )->renderPartial( 'star-rating', [
			'rating' => $this->db->getReviewMeta( get_post()->ID )->rating,
		]);
	}

	/**
	 * @return string
	 */
	protected function buildColumnSticky()
	{
		global $wp_version;

		$post = get_post();
		$pinned = $this->db->getReviewMeta( $post->ID )->pinned
			? ' pinned'
			: '';

		// WP < 4.4 support
		$fallback = version_compare( $wp_version, '4.4', '<' )
			? file_get_contents( "{$this->app->path}assets/img/pinned.svg" )
			: '';

		return sprintf( '<i class="dashicons dashicons-sticky%s" data-id="%s">%s</i>',
			$pinned,
			$post->ID,
			$fallback
		);
	}

	/**
	 * @return string
	 */
	protected function buildColumnType()
	{
		$meta  = $this->db->getReviewMeta( get_post()->ID );
		$types = $this->app->make( 'Strings' )->review_types();
		return isset( $types[ $meta->review_type ] )
			? $types[ $meta->review_type ]
			: $meta->review_type;
	}

	/**
	 * @return bool
	 */
	protected function hasPermission( WP_Query $query )
	{
		global $pagenow;
		return !( !is_admin()
			|| !$query->is_main_query()
			|| $query->query['post_type'] != App::POST_TYPE
			|| $pagenow != 'edit.php'
		);
	}

	/**
	 * @param array $ratings
	 *
	 * @return void
	 */
	protected function renderFilterRatings( $ratings )
	{
		if( empty( $ratings ) || apply_filters( 'site-reviews/disable/filter/ratings', false ))return;

		$ratings = array_flip( array_reverse( $ratings ));

		array_walk( $ratings, function( &$value, $key ) {
			$label = _n( '%s star', '%s stars', $key, 'site-reviews' );
			$value = sprintf( $label, $key );
		});

		$ratings = [ __( 'All ratings', 'site-reviews' ) ] + $ratings;

		printf( '<label class="screen-reader-text" for="rating">%s</label>', __( 'Filter by rating', 'site-reviews' ));

		echo $this->app->make( 'Html' )->renderPartial( 'filterby', [
			'name'   => 'rating',
			'values' => $ratings,
		]);
	}

	/**
	 * @param array $types
	 *
	 * @return void
	 */
	protected function renderFilterTypes( $types )
	{
		if( count( $types ) < 1
			|| ( count( $types ) == 1 && $types[0] == 'local' )
			|| apply_filters( 'site-reviews/disable/filter/types', false )
		)return;

		$reviewTypes = [ __( 'All types', 'site-reviews' ) ];

		foreach( $types as $type ) {
			$reviewTypes[ $type ] = $this->app->make( 'Strings' )->review_types( $type, ucfirst( $type ));
		}

		printf( '<label class="screen-reader-text" for="type">%s</label>', __( 'Filter by type', 'site-reviews' ));

		echo $this->app->make( 'Html' )->renderPartial( 'filterby', [
			'name'   => 'review_type',
			'values' => $reviewTypes,
		]);
	}

	/**
	 * Modifies the WP_Query meta_query value
	 *
	 * @return self
	 */
	protected function setMeta( WP_Query $query, array $meta_keys )
	{
		foreach( $meta_keys as $key ) {
			if( !( $value = filter_input( INPUT_GET, $key )))continue;

			$query->query_vars['meta_query'][] = [
				'key'   => $key,
				'value' => $value,
			];
		}

		return $this;
	}

	/**
	 * Modifies the WP_Query orderby value
	 *
	 * @return self
	 */
	protected function setOrderby( WP_Query $query )
	{
		$orderby = $query->get( 'orderby' );
		switch( $orderby ) {
			case 'assigned_to':
			case 'author':
			case 'pinned':
			case 'rating':
			case 'review_type':
				$query->set( 'meta_key', $orderby );
				$query->set( 'orderby', 'meta_value' );
				break;
		}
		return $this;
	}
}
