<?php

namespace GeminiLabs\SiteReviews\Widgets;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;
use GeminiLabs\SiteReviews\Widgets\Widget;

class SiteReviewsFormWidget extends Widget
{
	/**
	 * @param array $instance
	 * @return void
	 */
	public function form( $instance )
	{
		$this->widgetArgs = glsr( SiteReviewsFormShortcode::class )->normalize( $instance );
		$terms = glsr( Database::class )->getTerms();
		$this->renderField( 'text', [
			'class' => 'widefat',
			'label' => __( 'Title', 'site-reviews' ),
			'name' => 'title',
		]);
		$this->renderField( 'textarea', [
			'class' => 'widefat',
			'label' => __( 'Description', 'site-reviews' ),
			'name' => 'description',
		]);
		$this->renderField( 'select', [
			'class' => 'widefat',
			'label' => __( 'Automatically assign a category', 'site-reviews' ),
			'name' => 'category',
			'options' => ['' => __( 'Do not assign a category', 'site-reviews' )] + $terms,
		]);
		$this->renderField( 'text', [
			'class' => 'widefat',
			'default' => '',
			'description' => sprintf( __( 'You may also enter %s to assign to the current post.', 'site-reviews' ), '<code>post_id</code>' ),
			'label' => __( 'Assign reviews to a custom page/post ID', 'site-reviews' ),
			'name' => 'assign_to',
		]);
		$this->renderField( 'text', [
			'class' => 'widefat',
			'label' => __( 'Enter any custom CSS classes here', 'site-reviews' ),
			'name' => 'class',
		]);
		$this->renderField( 'checkbox', [
			'name' => 'hide',
			'options' => [
				'email' => __( 'Hide the email field', 'site-reviews' ),
				'content' => __( 'Hide the content field', 'site-reviews' ),
				'name'  => __( 'Hide the name field', 'site-reviews' ),
				'rating'  => __( 'Hide the rating field', 'site-reviews' ),
				'terms' => __( 'Hide the terms field', 'site-reviews' ),
				'title' => __( 'Hide the title field', 'site-reviews' ),
			],
		]);
	}

	/**
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance )
	{
		echo glsr( SiteReviewsFormShortcode::class )->build( $instance, $args, 'widget' );
	}
}
