<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Defaults\PostTypeDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class RegisterPostType
{
	public $args;
	public $columns;
	public $plural;
	public $postType;
	public $single;

	public function __construct( $input )
	{
		$args = glsr( PostTypeDefaults::class )->merge( $input );
		$this->normalize( $args );
		$this->normalizeColumns();
		$this->normalizeLabels();
	}

	/**
	 * @return void
	 */
	protected function normalize( array $args )
	{
		foreach( $args as $key => $value ) {
			$property = glsr( Helper::class )->buildPropertyName( $key );
			if( !property_exists( $this, $property ))continue;
			$this->$property = $value;
			unset( $args[$key] );
		}
		$this->args = wp_parse_args( $args, [
			'menu_name' => $this->plural,
		]);
	}

	/**
	 * @return void
	 */
	protected function normalizeLabels()
	{
		$this->args['labels'] = wp_parse_args( $this->args['labels'], [
			'add_new_item' => sprintf( _x( 'Add New %s', 'Add New Post', 'site-reviews' ), $this->plural ),
			'all_items' => sprintf( _x( 'All %s', 'All Posts', 'site-reviews' ), $this->plural ),
			'archives' => sprintf( _x( '%s Archives', 'Post Archives', 'site-reviews' ), $this->single ),
			'edit_item' => sprintf( _x( 'Edit %s', 'Edit Post', 'site-reviews' ), $this->single ),
			'insert_into_item' => sprintf( _x( 'Insert into %s', 'Insert into Post', 'site-reviews' ), $this->single ),
			'menu_name' => $this->args['menu_name'],
			'name' => $this->plural,
			'new_item' => sprintf( _x( 'New %s', 'New Post', 'site-reviews' ), $this->single ),
			'not_found' => sprintf( _x( 'No %s found', 'No Posts found', 'site-reviews' ), $this->plural ),
			'not_found_in_trash' => sprintf( _x( 'No %s found in Trash', 'No Posts found in Trash', 'site-reviews' ), $this->plural ),
			'search_items' => sprintf( _x( 'Search %s', 'Search Posts', 'site-reviews' ), $this->plural ),
			'singular_name' => $this->single,
			'uploaded_to_this_item' => sprintf( _x( 'Uploaded to this %s', 'Uploaded to this Post', 'site-reviews' ), $this->single ),
			'view_item' => sprintf( _x( 'View %s', 'View Post', 'site-reviews' ), $this->single ),
		]);
		unset( $this->args['menu_name'] );
	}

	/**
	 * @return void
	 */
	protected function normalizeColumns()
	{
		$this->columns = ['cb' => ''] + $this->columns;
		if( array_key_exists( 'category', $this->columns )) {
			$keys = array_keys( $this->columns );
			$keys[array_search( 'category', $keys )] = 'taxonomy-'.Application::TAXONOMY;
			$this->columns = array_combine( $keys, $this->columns );
		}
		if( array_key_exists( 'pinned', $this->columns )) {
			$this->columns['pinned'] = glsr( Builder::class )->span( '<span>'.$this->columns['pinned'].'</span>',
				['class' => 'pinned-icon']
			);
		}
	}
}
