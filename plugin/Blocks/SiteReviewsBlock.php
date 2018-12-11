<?php

namespace GeminiLabs\SiteReviews\Blocks;

use GeminiLabs\SiteReviews\Blocks\BlockGenerator;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode as Shortcode;

class SiteReviewsBlock extends BlockGenerator
{
	/**
	 * @return array
	 */
	public function attributes()
	{

		return [
			'assigned_to' => [
				'default' => '',
				'type' => 'string',
			],
			'category' => [
				'default' => '',
				'type' => 'string',
			],
			'className' => [
				'default' => '',
				'type' => 'string',
			],
			'count' => [
				'default' => 5,
				'type' => 'number',
			],
			'hide' => [
				'default' => '',
				'type' => 'string',
			],
			'pagination' => [
				'default' => '',
				'type' => 'string',
			],
			'post_id' => [
				'default' => '',
				'type' => 'string',
			],
			'rating' => [
				'default' => 1,
				'type' => 'number',
			],
			'schema' => [
				'default' => false,
				'type' => 'boolean',
			],
			'title' => [
				'default' => '',
				'type' => 'string',
			],
			'type' => [
				'default' => '',
				'type' => 'string',
			],
		];
	}

	/**
	 * @return void
	 */
	public function render( array $attributes )
	{
		if( filter_input( INPUT_GET, 'context' ) == 'edit' && $attributes['assigned_to'] == 'post_id' ) {
			$attributes['assigned_to'] = $attributes['post_id'];
		}
		$attributes['class'] = $attributes['className'];
		return glsr( Shortcode::class )->buildShortcode( $attributes );
	}
}



