<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

class Strings
{
	/**
	 * @param string|null $key
	 * @param string      $fallback
	 *
	 * @return array|string
	 */
	public function post_type_labels( $key = null, $fallback = ''  )
	{
		return $this->result( $key, $fallback, [
			'add_new_item'          => __( 'Add New Review', 'site-reviews' ),
			'all_items'             => __( 'All Reviews', 'site-reviews' ),
			'archives'              => __( 'Review Archives', 'site-reviews' ),
			'edit_item'             => __( 'Edit Review', 'site-reviews' ),
			'insert_into_item'      => __( 'Insert into review', 'site-reviews' ),
			'new_item'              => __( 'New Review', 'site-reviews' ),
			'not_found'             => __( 'No Reviews found', 'site-reviews' ),
			'not_found_in_trash'    => __( 'No Reviews found in Trash', 'site-reviews' ),
			'search_items'          => __( 'Search Reviews', 'site-reviews' ),
			'uploaded_to_this_item' => __( 'Uploaded to this review', 'site-reviews' ),
			'view_item'             => __( 'View Review', 'site-reviews' ),
		]);
	}

	/**
	 * @param string|null $key
	 * @param string      $fallback
	 *
	 * @return array|string
	 */
	public function post_updated_messages( $key = null, $fallback = ''  )
	{
		return $this->result( $key, $fallback, [
			'approved'      => __( 'Review has been approved and published.', 'site-reviews' ),
			'draft_updated' => __( 'Review draft updated.', 'site-reviews' ),
			'preview'       => __( 'Preview review', 'site-reviews' ),
			'published'     => __( 'Review approved and published.', 'site-reviews' ),
			'restored'      => __( 'Review restored to revision from %s.', 'site-reviews' ),
			'reverted'      => __( 'Review has been reverted to its original submission state.', 'site-reviews' ),
			'saved'         => __( 'Review saved.', 'site-reviews' ),
			'scheduled'     => __( 'Review scheduled for: %s.', 'site-reviews' ),
			'submitted'     => __( 'Review submitted.', 'site-reviews' ),
			'unapproved'    => __( 'Review has been unapproved and is now pending.', 'site-reviews' ),
			'updated'       => __( 'Review updated.', 'site-reviews' ),
			'view'          => __( 'View review', 'site-reviews' ),
		]);
	}

	/**
	 * @param string|null $key
	 * @param string      $fallback
	 *
	 * @return array|string
	 *
	 * @since 2.0.0
	 */
	public function review_types( $key = null, $fallback = ''  )
	{
		return $this->result( $key, $fallback, apply_filters( 'site-reviews/addon/types', [
			'local' => __( 'Local', 'site-reviews' ),
		]));
	}

	/**
	 * @param string|null $key
	 * @param string      $fallback
	 *
	 * @return array|string
	 */
	public function validation( $key = null, $fallback = '' )
	{
		return $this->result( $key, $fallback, [
			'accepted' => __( 'This field must be accepted.', 'site-reviews' ),
			'between.numeric' => _x( 'This field must be between :min and :max.', ':min, and :max are placeholders and should not be translated.', 'site-reviews' ),
			'between.string' => _x( 'This field must be between :min and :max characters.', ':min, and :max are placeholders and should not be translated.', 'site-reviews' ),
			'email' => __( 'This must be a valid email address.', 'site-reviews' ),
			'max.numeric' => _x( 'This field may not be greater than :max.', ':max is a placeholder and should not be translated.', 'site-reviews' ),
			'max.string' => _x( 'This field may not be greater than :max characters.', ':max is a placeholder and should not be translated.', 'site-reviews' ),
			'min.numeric' => _x( 'This field must be at least :min.', ':min is a placeholder and should not be translated.', 'site-reviews' ),
			'min.string' => _x( 'This field must be at least :min characters.', ':min is a placeholder and should not be translated.', 'site-reviews' ),
			'regex' => __( 'The format is invalid.', 'site-reviews' ),
			'required' => __( 'This field is required.', 'site-reviews' ),
		]);
	}

	/**
	 * @param string|null $key
	 * @param string      $fallback
	 *
	 * @return array|string
	 *
	 * @since 2.0.0
	 */
	protected function result( $key, $fallback, array $values )
	{
		if( is_string( $key )) {
			return isset( $values[ $key ] )
				? $values[ $key ]
				: $fallback;
		}

		return $values;
	}
}
