<?php

/**
 * Site Reviews Form widget
 *
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Widgets;

use GeminiLabs\SiteReviews\Traits\SiteReviewsForm as Common;
use GeminiLabs\SiteReviews\Widget;

/**
 * @property bool|string $id
 */
class SiteReviewsForm extends Widget
{
	use Common;

	/**
	 * Display the widget form
	 *
	 * @param array $instance
	 *
	 * @return void
	 */
	public function form( $instance )
	{
		$args = $this->normalize( $instance, [
			'description' => sprintf( __( 'Your email address will not be published. Required fields are marked %s*%s', 'site-reviews' ), '<span>', '</span>' ),
		]);

		$this->create_field([
			'type'  => 'text',
			'name'  => 'title',
			'label' => __( 'Title', 'site-reviews' ),
			'value' => $args['title'],
		]);

		$this->create_field([
			'type'  => 'textarea',
			'name'  => 'description',
			'class' => 'widefat',
			'label' => __( 'Description', 'site-reviews' ),
			'value' => $args['description'],
		]);

		$this->create_field([
			'type'  => 'select',
			'name'  => 'category',
			'label' => __( 'Automatically assign a category', 'site-reviews' ),
			'value' => $args['category'],
			'options' => ['' => __( 'Do not assign a category', 'site-reviews' ) ] + glsr_resolve( 'Database' )->getTerms(),
			'class' => 'widefat',
		]);

		$this->create_field([
			'type'    => 'text',
			'name'    => 'assign_to',
			'label'   => __( 'Assign reviews to a custom page/post ID', 'site-reviews' ),
			'value'   => $args['assign_to'],
			'default' => '',
			'description' => sprintf( __( 'You may also enter %s to assign to the current post.', 'site-reviews' ), '<code>post_id</code>' ),
		]);

		$this->create_field([
			'type'  => 'text',
			'name'  => 'class',
			'label' => __( 'Enter any custom CSS classes here', 'site-reviews' ),
			'value' => $args['class'],
		]);

		$this->create_field([
			'type'  => 'checkbox',
			'name'  => 'hide',
			'value' => $args['hide'],
			'options' => [
				'email' => __( 'Hide the email field', 'site-reviews' ),
				'name'  => __( 'Hide the name field', 'site-reviews' ),
				'terms' => __( 'Hide the terms field', 'site-reviews' ),
				'title' => __( 'Hide the title field', 'site-reviews' ),
			],
		]);
	}

	/**
	 * Display the widget Html
	 *
	 * @param array $args
	 * @param array $instance
	 *
	 * @return void
	 */
	public function widget( $args, $instance )
	{
		$instance = $this->normalize( $instance );
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		if( $instance['assign_to'] == 'post_id' ) {
			$instance['assign_to'] = intval( get_the_ID() );
		}

		echo $args['before_widget'];
		if( !empty( $title )) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		if( !$this->renderRequireLogin() ) {
			echo $this->renderForm( $instance );
		}
		echo $args['after_widget'];
	}
}
