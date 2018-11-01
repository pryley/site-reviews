<?php

namespace GeminiLabs\SiteReviews\Widgets;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;
use GeminiLabs\SiteReviews\Widgets\Widget;

class SiteReviewsSummaryWidget extends Widget
{
	/**
	 * @param array $instance
	 * @return void
	 */
	public function form( $instance )
	{
		$this->widgetArgs = glsr( SiteReviewsSummaryShortcode::class )->normalize( $instance );
		$terms = glsr( Database::class )->getTerms();
		$this->renderField( 'text', [
			'class' => 'widefat',
			'label' => __( 'Title', 'site-reviews' ),
			'name' => 'title',
		]);
		if( count( glsr()->reviewTypes ) > 1 ) {
			$this->renderField( 'select', [
				'class' => 'widefat',
				'label' => __( 'Which type of review would you like to use?', 'site-reviews' ),
				'name' => 'type',
				'options' => ['' => __( 'All review types', 'site-reviews' )] + glsr()->reviewTypes,
			]);
		}
		if( !empty( $terms )) {
			$this->renderField( 'select', [
				'class' => 'widefat',
				'label' => __( 'Limit summary to this category', 'site-reviews' ),
				'name' => 'category',
				'options' => ['' => __( 'All Categories', 'site-reviews' )] + $terms,
			]);
		}
		$this->renderField( 'text', [
			'class' => 'widefat',
			'default' => '',
			'description' => sprintf( __( "Separate multiple ID's with a comma. You may also enter %s to automatically represent the current page/post ID.", 'site-reviews' ), '<code>post_id</code>' ),
			'label' => __( 'Limit summary to reviews assigned to a page/post ID', 'site-reviews' ),
			'name' => 'assigned_to',
		]);
		$this->renderField( 'text', [
			'class' => 'widefat',
			'label' => __( 'Enter any custom CSS classes here', 'site-reviews' ),
			'name' => 'class',
		]);
		$this->renderField( 'checkbox', [
			'name' => 'hide',
			'options' => [
				'bars' => __( 'Hide the percentage bars?', 'site-reviews' ),
				'rating' => __( 'Hide the rating?', 'site-reviews' ),
				'stars' => __( 'Hide the stars?', 'site-reviews' ),
				'summary' => __( 'Hide the summary text?', 'site-reviews' ),
				'if_empty' => __( 'Hide the summary if no reviews are found?', 'site-reviews' ),
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
		echo glsr( SiteReviewsSummaryShortcode::class )->build( $instance, $args, 'widget' );
	}
}
