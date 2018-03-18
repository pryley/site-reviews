<?php

/**
 * Site Reviews widget
 *
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Widgets;

use GeminiLabs\SiteReviews\Traits\SiteReviews as Common;
use GeminiLabs\SiteReviews\Widget;

class SiteReviews extends Widget
{
	use Common;

	/**
	 * Display the widget form
	 *
	 * @param array $instance
	 * @return void
	 */
	public function form( $instance )
	{
		$args = $this->normalize( $instance );
		$types = glsr_resolve( 'Database' )->getReviewTypes();
		$terms = glsr_resolve( 'Database' )->getTerms();

		$this->create_field([
			'type'  => 'text',
			'name'  => 'title',
			'label' => __( 'Title', 'site-reviews' ),
			'value' => $args['title'],
		]);

		$this->create_field([
			'type'    => 'number',
			'name'    => 'count',
			'label'   => __( 'How many reviews would you like to display? ', 'site-reviews' ),
			'value'   => $args['count'],
			'default' => 5,
			'max'     => 100,
		]);

		$this->create_field([
			'type'  => 'select',
			'name'  => 'rating',
			'label' => __( 'What is the minimum rating to display? ', 'site-reviews' ),
			'value' => $args['rating'],
			'options' => [
				'5' => sprintf( _n( '%s star', '%s stars', 5, 'site-reviews' ), 5 ),
				'4' => sprintf( _n( '%s star', '%s stars', 4, 'site-reviews' ), 4 ),
				'3' => sprintf( _n( '%s star', '%s stars', 3, 'site-reviews' ), 3 ),
				'2' => sprintf( _n( '%s star', '%s stars', 2, 'site-reviews' ), 2 ),
				'1' => sprintf( _n( '%s star', '%s stars', 1, 'site-reviews' ), 1 ),
			],
		]);

		if( count( $types ) > 1 ) {
			$this->create_field([
				'type'  => 'select',
				'name'  => 'display',
				'label' => __( 'Which reviews would you like to display? ', 'site-reviews' ),
				'class' => 'widefat',
				'value' => $args['display'],
				'options' => ['' => __( 'All Reviews', 'site-reviews' ) ] + $types,
			]);
		}

		if( !empty( $terms )) {
			$this->create_field([
				'type'  => 'select',
				'name'  => 'category',
				'label' => __( 'Limit reviews to this category', 'site-reviews' ),
				'class' => 'widefat',
				'value' => $args['category'],
				'options' => ['' => __( 'All Categories', 'site-reviews' ) ] + glsr_resolve( 'Database' )->getTerms(),
			]);
		}

		$this->create_field([
			'type'    => 'text',
			'name'    => 'assigned_to',
			'label'   => __( 'Limit reviews to those assigned to this page/post ID', 'site-reviews' ),
			'value'   => $args['assigned_to'],
			'default' => '',
			'placeholder' => __( "Separate multiple ID's with a comma", 'site-reviews' ),
			'description' => sprintf( __( 'You may also enter %s to limit assigned reviews to the current page.', 'site-reviews' ), '<code>post_id</code>' ),
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
				'author' => __( 'Hide the review author?', 'site-reviews' ),
				'avatar' => __( 'Hide the reviewer avatar if shown?', 'site-reviews' ),
				'date' => __( 'Hide the review date?', 'site-reviews' ),
				'excerpt' => __( 'Hide the review excerpt?', 'site-reviews' ),
				'rating' => __( 'Hide the review rating?', 'site-reviews' ),
				'response' => __( 'Hide the review response?', 'site-reviews' ),
				'title' => __( 'Hide the review title?', 'site-reviews' ),
			],
		]);
	}

	/**
	 * Update the widget form
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance )
	{
		if( $new_instance['count'] < 0 ) {
			$new_instance['count'] = 0;
		}
		if( $new_instance['count'] > 100 ) {
			$new_instance['count'] = 100;
		}
		if( !is_numeric( $new_instance['count'] )) {
			$new_instance['count'] = 5;
		}
		return parent::update( $new_instance, $old_instance );
	}

	/**
	 * Display the widget Html
	 *
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance )
	{
		$instance = $this->normalize( $instance );
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		if( $instance['assigned_to'] == 'post_id' ) {
			$instance['assigned_to'] = intval( get_the_ID() );
		}

		echo $args['before_widget'];
		if( !empty( $title )) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		$this->renderReviews( $instance );
		echo $args['after_widget'];
	}
}
