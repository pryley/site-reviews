<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Helper;

class CreateReview
{
	public $ajax_request;
	public $assigned_to;
	public $author;
	public $avatar;
	public $blacklisted;
	public $category;
	public $content;
	public $custom;
	public $date;
	public $email;
	public $form_id;
	public $ip_address;
	public $post_id;
	public $rating;
	public $referer;
	public $terms;
	public $title;

	protected $request;

	public function __construct( $input )
	{
		$this->request = $input;
		$this->ajax_request = isset( $input['ajax_request'] );
		$this->assigned_to = $this->getNumeric( 'assign_to' );
		$this->author = sanitize_text_field( $this->get( 'name' ));
		$this->avatar = get_avatar_url( $this->get( 'email' ));
		$this->blacklisted = isset( $input['blacklisted'] );
		$this->category = sanitize_key( $this->get( 'category' ));
		$this->content = sanitize_textarea_field( $this->get( 'content' ));
		$this->custom = $this->getCustom();
		$this->date = $this->getDate( 'date' );
		$this->email = sanitize_email( $this->get( 'email' ));
		$this->form_id = sanitize_key( $this->get( 'form_id' ));
		$this->ip_address = $this->get( 'ip_address' );
		$this->post_id = intval( $this->get( 'post_id' ));
		$this->rating = intval( $this->get( 'rating' ));
		$this->referer = $this->get( 'referer' );
		$this->terms = !empty( $input['terms'] );
		$this->title = sanitize_text_field( $this->get( 'title' ));
	}

	/**
	 * @param string $key
	 * @return string
	 */
	protected function get( $key )
	{
		return isset( $this->request[$key] )
			? (string)$this->request[$key]
			: '';
	}

	/**
	 * @return array
	 */
	protected function getCustom()
	{
		$unset = [
			'action', 'ajax_request', 'assign_to', 'category', 'content', 'counter', 'email',
			'excluded', 'form_id', 'gotcha', 'ip_address', 'name', 'nonce', 'post_id', 'rating',
			'recaptcha-token', 'referer', 'terms', 'title',
		];
		$custom = $this->request;
		foreach( $unset as $value ) {
			unset( $custom[$value] );
		}
		return $custom;
	}

	/**
	 * @param string $key
	 * @return string
	 */
	protected function getDate( $key )
	{
		$date = strtotime( $this->get( $key ));
		if( $date === false ) {
			$date = time();
		}
		return get_date_from_gmt( gmdate( 'Y-m-d H:i:s', $date ));
	}

	/**
	 * @param string $key
	 * @return string
	 */
	protected function getNumeric( $key )
	{
		return is_numeric( $this->request[$key] )
			? (string)$this->request[$key]
			: '';
	}
}
