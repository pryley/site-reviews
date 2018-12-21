<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Defaults\CreateReviewDefaults;
use WP_Post;

class Review
{
	public $assigned_to;
	public $author;
	public $avatar;
	public $content;
	public $custom;
	public $date;
	public $email;
	public $ID;
	public $ip_address;
	public $modified;
	public $pinned;
	public $rating;
	public $response;
	public $review_id;
	public $review_type;
	public $status;
	public $term_ids;
	public $title;
	public $url;
	public $user_id;

	public function __construct( WP_Post $post )
	{
		$this->content = $post->post_content;
		$this->date = $post->post_date;
		$this->ID = intval( $post->ID );
		$this->status = $post->post_status;
		$this->title = $post->post_title;
		$this->user_id = intval( $post->post_author );
		$this->setProperties( $post );
		$this->setTermIds( $post );
	}

	/**
	 * @return mixed
	 */
	public function __get( $key )
	{
		if( property_exists( $this, $key )) {
			return $this->$key;
		}
		if( is_array( $this->custom ) && array_key_exists( $key, $this->custom )) {
			return $this->custom[$key];
		}
		return '';
	}

	/**
	 * @return bool
	 */
	protected function isModified( array $properties )
	{
		return $this->date != $properties['date']
			|| $this->content != $properties['content']
			|| $this->title != $properties['title'];
	}

	/**
	 * @return void
	 */
	protected function setProperties( WP_Post $post )
	{
		$defaults = [
			'author' => __( 'Anonymous', 'site-reviews' ),
			'date' => '',
			'review_id' => '',
			'review_type' => 'local',
		];
		$meta = array_filter(
			array_map( 'array_shift', (array)get_post_meta( $post->ID )),
			'strlen'
		);
		$properties = glsr( CreateReviewDefaults::class )->restrict( array_merge( $defaults, $meta ));
		$this->modified = $this->isModified( $properties );
		array_walk( $properties, function( $value, $key ) {
			if( !property_exists( $this, $key ) || isset( $this->$key ))return;
			$this->$key = maybe_unserialize( $value );
		});
	}

	/**
	 * @return void
	 */
	protected function setTermIds( WP_Post $post )
	{
		$this->term_ids = [];
		if( !is_array( $terms = get_the_terms( $post, Application::TAXONOMY )))return;
		foreach( $terms as $term ) {
			$this->term_ids[] = $term->term_id;
		}
	}
}
