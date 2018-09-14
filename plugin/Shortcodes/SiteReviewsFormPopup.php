<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Shortcodes\TinymcePopupGenerator;

class SiteReviewsFormPopup extends TinymcePopupGenerator
{
	/**
	 * @return array
	 */
	public function fields()
	{
		return [[
			'type' => 'container',
			'html' => '<p class="strong">'.esc_html__( 'All settings are optional.', 'site-reviews' ).'</p>',
		],[
			'label' => esc_html__( 'Title', 'site-reviews' ),
			'name' => 'title',
			'tooltip' => __( 'Enter a custom shortcode heading.', 'site-reviews' ),
			'type' => 'textbox',
		],[
			'label' => esc_html__( 'Description', 'site-reviews' ),
			'minHeight' => 60,
			'minWidth' => 240,
			'multiline' => true,
			'name' => 'description',
			'tooltip' => __( 'Enter a custom shortcode description.', 'site-reviews' ),
			'type' => 'textbox',
		],
		$this->getTerms(),
		[
			'label' => esc_html__( 'Assign To', 'site-reviews' ),
			'name' => 'assign_to',
			'tooltip' => __( 'Assign submitted reviews to a custom page/post ID. You can also enter "post_id" to assign reviews to the ID of the current page.', 'site-reviews' ),
			'type' => 'textbox',
		],[
			'label' => esc_html__( 'Classes', 'site-reviews' ),
			'name' => 'class',
			'tooltip' => __( 'Add custom CSS classes to the shortcode.', 'site-reviews' ),
			'type' => 'textbox',
		],[
			'columns' => 2,
			'items' => [[
				'name' => 'hide_content',
				'text' => esc_html__( 'Content', 'site-reviews' ),
				'tooltip' => __( 'Hide the content field?', 'site-reviews' ),
				'type' => 'checkbox',
			],[
				'name' => 'hide_email',
				'text' => esc_html__( 'Email', 'site-reviews' ),
				'tooltip' => __( 'Hide the email field?', 'site-reviews' ),
				'type' => 'checkbox',
			],[
				'name' => 'hide_name',
				'text' => esc_html__( 'Name', 'site-reviews' ),
				'tooltip' => __( 'Hide the name field?', 'site-reviews' ),
				'type' => 'checkbox',
			],[
				'name' => 'hide_rating',
				'text' => esc_html__( 'Rating', 'site-reviews' ),
				'tooltip' => __( 'Hide the rating field?', 'site-reviews' ),
				'type' => 'checkbox',
			],[
				'name' => 'hide_terms',
				'text' => esc_html__( 'Terms', 'site-reviews' ),
				'tooltip' => __( 'Hide the terms field?', 'site-reviews' ),
				'type' => 'checkbox',
			],[
				'name' => 'hide_title',
				'text' => esc_html__( 'Title', 'site-reviews' ),
				'tooltip' => __( 'Hide the title field?', 'site-reviews' ),
				'type' => 'checkbox',
			]],
			'label' => esc_html__( 'Hide', 'site-reviews' ),
			'layout' => 'grid',
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
			'tooltip' => __( 'Automatically assign a category to reviews submitted with this shortcode.', 'site-reviews' ),
			'type' => 'listbox',
		];
	}
}
