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
	public $response;
	public $terms;
	public $title;
	public $url;

	protected $request;

	public function __construct( $input )
	{
		$this->request = $input;
		$this->ajax_request = isset( $input['_ajax_request'] );
		$this->assigned_to = $this->getNumeric( 'assign_to' );
		$this->author = sanitize_text_field( $this->getUser( 'name' ));
		$this->avatar = $this->getAvatar();
		$this->blacklisted = isset( $input['blacklisted'] );
		$this->category = sanitize_key( $this->get( 'category' ));
		$this->content = sanitize_textarea_field( $this->get( 'content' ));
		$this->custom = $this->getCustom();
		$this->date = $this->getDate( 'date' );
		$this->email = sanitize_email( $this->getUser( 'email' ));
		$this->form_id = sanitize_key( $this->get( 'form_id' ));
		$this->ip_address = $this->get( 'ip_address' );
		$this->post_id = intval( $this->get( '_post_id' ));
		$this->rating = intval( $this->get( 'rating' ));
		$this->referer = $this->get( '_referer' );
		$this->response = sanitize_textarea_field( $this->get( 'response' ));
		$this->terms = !empty( $input['terms'] );
		$this->title = sanitize_text_field( $this->get( 'title' ));
		$this->url = esc_url_raw( $this->get( 'url' ));
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
	 * @return string
	 */
	protected function getAvatar()
	{
		$avatar = $this->get( 'avatar' );
		return !filter_var( $avatar, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED | FILTER_FLAG_PATH_REQUIRED )
			? (string)get_avatar_url( $this->get( 'email' ))
			: $avatar;
	}

	/**
	 * @return array
	 */
	protected function getCustom()
	{
		$unset = [
			'_action', '_ajax_request', '_counter', '_nonce', '_post_id', '_recaptcha-token',
			'_referer', 'assign_to', 'category', 'content', 'date', 'email', 'excluded', 'form_id',
			'gotcha', 'ip_address', 'name', 'rating', 'response', 'terms', 'title', 'url',
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
	protected function getUser( $key )
	{
		$value = $this->get( $key );
		if( empty( $value )) {
			$user = wp_get_current_user();
			$userValues = [
				'email' => 'user_email',
				'name' => 'display_name',
			];
			if( $user->exists() && array_key_exists( $key, $userValues )) {
				return $user->{$userValues[$key]};
			}
		}
		return $value;
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
