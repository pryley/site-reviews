<?php

namespace GeminiLabs\SiteReviews\Blocks;

use GeminiLabs\SiteReviews\Blocks\BlockGenerator;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode as Shortcode;

class SiteReviewsFormBlock extends BlockGenerator
{
	/**
	 * @return array
	 */
	public function attributes()
	{
		return [
			'assign_to' => [
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
			'hide' => [
				'default' => '',
				'type' => 'string',
			],
			'id' => [
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
		$attributes['class'] = $attributes['className'];
		return glsr( Shortcode::class )->buildShortcode( $attributes );
	}
}



