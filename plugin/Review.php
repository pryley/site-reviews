<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Defaults\CreateReviewDefaults;
use WP_Post;

class Review
{
	public $assigned_to;
	public $author;
	public $avatar;
	public $content;
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
	public $title;
	public $url;
	public $user_id;

	public function __construct( WP_Post $post )
	{
		$this->content = $post->post_content;
		$this->date = $post->post_date;
		$this->ID = $post->ID;
		$this->status = $post->post_status;
		$this->title = $post->post_title;
		$this->user_id = $post->post_author;
		$this->setProperties( $post );
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
			'review_type' => '',
		];
		$meta = array_filter(
			array_map( 'array_shift', (array)get_post_meta( $post->ID )),
			'strlen'
		);
		$properties = glsr( CreateReviewDefaults::class )->restrict( array_merge( $defaults, $meta ));
		$this->modified = $this->isModified( $properties );
		array_walk( $properties, function( $value, $key ) {
			if( !property_exists( $this, $key ) || isset( $this->$key ))return;
			$this->$key = $value;
		});
	}
}
