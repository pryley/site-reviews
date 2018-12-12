<?php

namespace GeminiLabs\SiteReviews\Blocks;

use GeminiLabs\SiteReviews\Blocks\BlockGenerator;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode as Shortcode;

class SiteReviewsSummaryBlock extends BlockGenerator
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
			'hide' => [
				'default' => '',
				'type' => 'string',
			],
			'post_id' => [
				'default' => '',
				'type' => 'string',
			],
			'rating' => [
				'default' => '1',
				'type' => 'number',
			],
			'schema' => [
				'default' => false,
				'type' => 'boolean',
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
		$attributes['class'] = $attributes['className'];
		if( filter_input( INPUT_GET, 'context' ) == 'edit' ) {
			$this->filterShortcodeClass();
			if( $attributes['assigned_to'] == 'post_id' ) {
				$attributes['assigned_to'] = $attributes['post_id'];
			}
		}
		return glsr( Shortcode::class )->buildShortcode( $attributes );
	}

	/**
	 * @return void
	 */
	protected function filterShortcodeClass()
	{
		add_filter( 'site-reviews/style', function() {
			return 'default';
		});
	}
}
