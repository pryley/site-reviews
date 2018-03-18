<?php

/**
 * Site Reviews Summary shortcode button
 *
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2017, Paul Ryley
 * @license   GPLv3
 * @since     2.3.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Shortcodes\Buttons;

use GeminiLabs\SiteReviews\Shortcodes\Buttons\Generator;

class SiteReviewsSummary extends Generator
{
	/**
	 * @return array
	 */
	public function fields()
	{
		$types = glsr_resolve( 'Database' )->getReviewTypes();
		$terms = glsr_resolve( 'Database' )->getTerms();

		if( count( $types ) > 1 ) {
			$display = [
				'type' => 'listbox',
				'name' => 'display',
				'label' => esc_html__( 'Display', 'site-reviews' ),
				'options' => $types,
				'tooltip' => __( 'Which reviews would you like to display?', 'site-reviews' ),
			];
		}
		if( !empty( $terms )) {
			$category = [
				'type' => 'listbox',
				'name' => 'category',
				'label' => esc_html__( 'Category', 'site-reviews' ),
				'options' => $terms,
				'tooltip' => __( 'Limit reviews to this category.', 'site-reviews' ),
			];
		}
		return [
			[
				'type' => 'container',
				'html' => sprintf( '<p class="strong">%s</p>', esc_html__( 'All settings are optional.', 'site-reviews' )),
				'minWidth' => 320,
			],[
				'type' => 'textbox',
				'name' => 'title',
				'label' => esc_html__( 'Title', 'site-reviews' ),
				'tooltip' => __( 'Enter a custom shortcode heading.', 'site-reviews' ),
			],[
				'type' => 'textbox',
				'name' => 'labels',
				'label' => esc_html__( 'Labels', 'site-reviews' ),
				'tooltip' => __( 'Enter custom labels for the 1-5 star rating levels (from high to low), and separate each with a comma. The defaults labels are: "Excellent, Very good, Average, Poor, Terrible".', 'site-reviews' ),
			],[
				'type' => 'listbox',
				'name' => 'rating',
				'label' => esc_html__( 'Rating', 'site-reviews' ),
				'options' => [
					'5' => esc_html( sprintf( _n( '%s star', '%s stars', 5, 'site-reviews' ), 5 )),
					'4' => esc_html( sprintf( _n( '%s star', '%s stars', 4, 'site-reviews' ), 4 )),
					'3' => esc_html( sprintf( _n( '%s star', '%s stars', 3, 'site-reviews' ), 3 )),
					'2' => esc_html( sprintf( _n( '%s star', '%s stars', 2, 'site-reviews' ), 2 )),
					'1' => esc_html( sprintf( _n( '%s star', '%s stars', 1, 'site-reviews' ), 1 )),
				],
				'tooltip' => __( 'What is the minimum rating? (default: 1 star)', 'site-reviews' ),
			],
			( isset( $display ) ? $display : [] ),
			( isset( $category ) ? $category : [] ),
			[
				'type' => 'textbox',
				'name' => 'assigned_to',
				'label' => esc_html__( 'Post ID', 'site-reviews' ),
				'tooltip' => __( "Limit reviews to those assigned to this post ID (separate multiple ID's with a comma). You can also enter 'post_id' to use the ID of the current page.", 'site-reviews' ),
			],[
				'type' => 'listbox',
				'name' => 'schema',
				'label' => esc_html__( 'Schema', 'site-reviews' ),
				'options' => [
					'true' => esc_html__( 'Enable rich snippets', 'site-reviews' ),
					'false' => esc_html__( 'Disable rich snippets', 'site-reviews' ),
				],
				'tooltip' => __( 'Rich snippets are disabled by default.', 'site-reviews' ),
			],[
				'type' => 'textbox',
				'name' => 'class',
				'label' => esc_html__( 'Classes', 'site-reviews' ),
				'tooltip' => __( 'Add custom CSS classes to the shortcode.', 'site-reviews' ),
			],[
				'type' => 'container',
				'label' => esc_html__( 'Hide', 'site-reviews' ),
				'layout' => 'grid',
				'columns' => 2,
				'spacing' => 5,
				'items' => [
					[
						'type' => 'checkbox',
						'name' => 'hide_bars',
						'text' => esc_html__( 'Bars', 'site-reviews' ),
						'tooltip' => esc_attr__( 'Hide the percentage bars?', 'site-reviews' ),
					],[
						'type' => 'checkbox',
						'name' => 'hide_rating',
						'text' => esc_html__( 'Rating', 'site-reviews' ),
						'tooltip' => esc_attr__( 'Hide the rating?', 'site-reviews' ),
					],[
						'type' => 'checkbox',
						'name' => 'hide_stars',
						'text' => esc_html__( 'Stars', 'site-reviews' ),
						'tooltip' => esc_attr__( 'Hide the stars?', 'site-reviews' ),
					],[
						'type' => 'checkbox',
						'name' => 'hide_summary',
						'text' => esc_html__( 'Summary', 'site-reviews' ),
						'tooltip' => esc_attr__( 'Hide the summary text?', 'site-reviews' ),
					],
				],
			],
		];
	}
}
