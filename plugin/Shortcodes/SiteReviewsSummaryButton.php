<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsButton;

class SiteReviewsSummaryButton extends SiteReviewsButton
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
				'type' => 'checkbox',
				'name' => 'hide_bars',
				'text' => esc_html__( 'Bars', 'site-reviews' ),
				'tooltip' => esc_attr__( 'Hide the percentage bars?', 'site-reviews' ),
			],[
				'type' => 'checkbox',
				'name' => 'hide_if_empty',
				'text' => esc_html__( 'If Empty', 'site-reviews' ),
				'tooltip' => esc_attr__( 'Hide the summary if no reviews are found?', 'site-reviews' ),
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
			]],
			'layout' => 'grid',
			'label' => esc_html__( 'Hide', 'site-reviews' ),
			'spacing' => 5,
			'type' => 'container',
		]];
	}
}
