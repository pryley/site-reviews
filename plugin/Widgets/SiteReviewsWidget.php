<?php

namespace GeminiLabs\SiteReviews\Widgets;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;
use GeminiLabs\SiteReviews\Widgets\Widget;

class SiteReviewsWidget extends Widget
{
	/**
	 * @param array $instance
	 * @return void
	 */
	public function form( $instance )
	{
		$this->widgetArgs = glsr( SiteReviewsShortcode::class )->normalize( $instance );
		$terms = glsr( Database::class )->getTerms();
		$this->renderField( 'text', [
			'class' => 'widefat',
			'label' => __( 'Title', 'site-reviews' ),
			'name' => 'title',
		]);
		$this->renderField( 'number', [
			'class' => 'small-text',
			'default' => 5,
			'label' => __( 'How many reviews would you like to display?', 'site-reviews' ),
			'max' => 100,
			'name' => 'count',
		]);
		$this->renderField( 'select', [
			'label' => __( 'What is the minimum rating to display?', 'site-reviews' ),
			'name' => 'rating',
			'options' => [
				'5' => sprintf( _n( '%s star', '%s stars', 5, 'site-reviews' ), 5 ),
				'4' => sprintf( _n( '%s star', '%s stars', 4, 'site-reviews' ), 4 ),
				'3' => sprintf( _n( '%s star', '%s stars', 3, 'site-reviews' ), 3 ),
				'2' => sprintf( _n( '%s star', '%s stars', 2, 'site-reviews' ), 2 ),
				'1' => sprintf( _n( '%s star', '%s stars', 1, 'site-reviews' ), 1 ),
			],
		]);
		if( count( glsr()->reviewTypes ) > 1 ) {
			$this->renderField( 'select', [
				'class' => 'widefat',
				'label' => __( 'Which type of review would you like to display?', 'site-reviews' ),
				'name' => 'type',
				'options' => ['' => __( 'All Reviews', 'site-reviews' )] + glsr()->reviewTypes,
			]);
		}
		if( !empty( $terms )) {
			$this->renderField( 'select', [
				'class' => 'widefat',
				'label' => __( 'Limit reviews to this category', 'site-reviews' ),
				'name' => 'category',
				'options' => ['' => __( 'All Categories', 'site-reviews' )] + $terms,
			]);
		}
		$this->renderField( 'text', [
			'class' => 'widefat',
			'default' => '',
			'description' => sprintf( __( "Separate multiple ID's with a comma. You may also enter %s to automatically represent the current page/post ID.", 'site-reviews' ), '<code>post_id</code>' ),
			'label' => __( 'Limit reviews to those assigned to this page/post ID', 'site-reviews' ),
			'name' => 'assigned_to',
		]);
		$this->renderField( 'text', [
			'class' => 'widefat',
			'label' => __( 'Enter any custom CSS classes here', 'site-reviews' ),
			'name' => 'class',
		]);
		$this->renderField( 'checkbox', [
			'name' => 'hide',
			'options' => glsr( SiteReviewsShortcode::class )->getHideOptions(),
		]);
	}

	/**
	 * @param array $newInstance
	 * @param array $oldInstance
	 * @return array
	 */
	public function update( $newInstance, $oldInstance )
	{
		if( !is_numeric( $newInstance['count'] )) {
			$newInstance['count'] = 10;
		}
		$newInstance['count'] = min( 50, max( 0, intval( $newInstance['count'] )));
		return parent::update( $newInstance, $oldInstance );
	}

	/**
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance )
	{
		echo glsr( SiteReviewsShortcode::class )->build( $instance, $args, 'widget' );
	}
}
