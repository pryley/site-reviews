<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Shortcodes\ButtonGenerator;

class SiteReviewsButton extends ButtonGenerator
{
	/**
	 * @return array
	 */
	public function fields()
	{
		return [[
			'html' => sprintf( '<p class="strong">%s</p>', esc_html__( 'All settings are optional.', 'site-reviews' )),
			'minWidth' => 320,
			'type' => 'container',
		],[
			'label' => esc_html__( 'Title', 'site-reviews' ),
			'name' => 'title',
			'tooltip' => esc_attr__( 'Enter a custom shortcode heading.', 'site-reviews' ),
			'type' => 'textbox',
		],[
			'label' => esc_html__( 'Count', 'site-reviews' ),
			'maxLength' => 5,
			'name' => 'count',
			'size' => 3,
			'text' => '10',
			'tooltip' => esc_attr__( 'How many reviews would you like to display (default: 10)?', 'site-reviews' ),
			'type' => 'textbox',
		],[
			'label' => esc_html__( 'Rating', 'site-reviews' ),
			'name' => 'rating',
			'options' => [
				'5' => esc_html( sprintf( _n( '%s star', '%s stars', 5, 'site-reviews' ), 5 )),
				'4' => esc_html( sprintf( _n( '%s star', '%s stars', 4, 'site-reviews' ), 4 )),
				'3' => esc_html( sprintf( _n( '%s star', '%s stars', 3, 'site-reviews' ), 3 )),
				'2' => esc_html( sprintf( _n( '%s star', '%s stars', 2, 'site-reviews' ), 2 )),
				'1' => esc_html( sprintf( _n( '%s star', '%s stars', 1, 'site-reviews' ), 1 )),
				'0' => esc_html( __( 'Unrated', 'site-reviews' )),
			],
			'tooltip' => esc_attr__( 'What is the minimum rating to display (default: 1 star)?', 'site-reviews' ),
			'type' => 'listbox',
		],[
			'label' => esc_html__( 'Pagination', 'site-reviews' ),
			'name' => 'pagination',
			'options' => [
				'true' => esc_html__( 'Enable', 'site-reviews' ),
				'ajax' => esc_html__( 'Enable (using ajax)', 'site-reviews' ),
				'false' => esc_html__( 'Disable', 'site-reviews' ),
			],
			'tooltip' => esc_attr__( 'When using pagination this shortcode can only be used once on a page. (default: disable)', 'site-reviews' ),
			'type' => 'listbox',
		],
		$this->getTypes(),
		$this->getTerms(),
		[
			'label' => esc_html__( 'Post ID', 'site-reviews' ),
			'name' => 'assigned_to',
			'tooltip' => esc_attr__( 'Limit reviews to those assigned to this post ID (separate multiple IDs with a comma). You can also enter "post_id" to use the ID of the current page.', 'site-reviews' ),
			'type' => 'textbox',
		],[
			'label' => esc_html__( 'Schema', 'site-reviews' ),
			'name' => 'schema',
			'options' => [
				'true' => esc_html__( 'Enable rich snippets', 'site-reviews' ),
				'false' => esc_html__( 'Disable rich snippets', 'site-reviews' ),
			],
			'tooltip' => esc_attr__( 'Rich snippets are disabled by default.', 'site-reviews' ),
			'type' => 'listbox',
		],[
			'label' => esc_html__( 'Classes', 'site-reviews' ),
			'name' => 'class',
			'tooltip' => esc_attr__( 'Add custom CSS classes to the shortcode.', 'site-reviews' ),
			'type' => 'textbox',
		],[
			'columns' => 2,
			'items' => [[
				'name' => 'hide_assigned_to',
				'text' => esc_html__( 'Assigned To', 'site-reviews' ),
				'tooltip' => esc_attr__( 'Hide the assigned to link?', 'site-reviews' ),
				'type' => 'checkbox',
			],[
				'name' => 'hide_author',
				'text' => esc_html__( 'Author', 'site-reviews' ),
				'tooltip' => esc_attr__( 'Hide the review author?', 'site-reviews' ),
				'type' => 'checkbox',
			],[
				'name' => 'hide_avatar',
				'text' => esc_html__( 'Avatar', 'site-reviews' ),
				'tooltip' => esc_attr__( 'Hide the reviewer avatar if shown?', 'site-reviews' ),
				'type' => 'checkbox',
			],[
				'name' => 'hide_content',
				'text' => esc_html__( 'Content', 'site-reviews' ),
				'tooltip' => esc_attr__( 'Hide the review content?', 'site-reviews' ),
				'type' => 'checkbox',
			],[
				'name' => 'hide_date',
				'text' => esc_html__( 'Date', 'site-reviews' ),
				'tooltip' => esc_attr__( 'Hide the review date?', 'site-reviews' ),
				'type' => 'checkbox',
			],[
				'name' => 'hide_rating',
				'text' => esc_html__( 'Rating', 'site-reviews' ),
				'tooltip' => esc_attr__( 'Hide the review rating?', 'site-reviews' ),
				'type' => 'checkbox',
			],[
				'name' => 'hide_response',
				'text' => esc_html__( 'Response', 'site-reviews' ),
				'tooltip' => esc_attr__( 'Hide the review response?', 'site-reviews' ),
				'type' => 'checkbox',
			],[
				'name' => 'hide_title',
				'text' => esc_html__( 'Title', 'site-reviews' ),
				'tooltip' => esc_attr__( 'Hide the review title?', 'site-reviews' ),
				'type' => 'checkbox',
			]],
			'layout' => 'grid',
			'label' => esc_html__( 'Hide', 'site-reviews' ),
			'spacing' => 5,
			'type' => 'container',
		],[
			'hidden' => true,
			'name' => 'id',
			'type' => 'textbox',
		]];
	}

	/**
	 * @return array
	 */
	public function getTerms()
	{
		$terms = glsr( Database::class )->getTerms();
		if( empty( $terms )) {
			return [];
		}
		return [
			'label' => esc_html__( 'Category', 'site-reviews' ),
			'name' => 'category',
			'options' => $terms,
			'tooltip' => esc_attr__( 'Limit reviews to this category.', 'site-reviews' ),
			'type' => 'listbox',
		];
	}

	/**
	 * @return array
	 */
	public function getTypes()
	{
		if( count( glsr()->reviewTypes ) < 2 ) {
			return [];
		}
		return [
			'label' => esc_html__( 'Display', 'site-reviews' ),
			'name' => 'display',
			'options' => glsr()->reviewTypes,
			'tooltip' => esc_attr__( 'Which reviews would you like to display?', 'site-reviews' ),
			'type' => 'listbox',
		];
	}
}
