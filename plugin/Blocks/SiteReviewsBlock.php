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
			'id' => [
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
			$this->filterReviewLinks();
			$this->filterShortcodeClass();
			$this->filterShowMoreLinks( 'content' );
			$this->filterShowMoreLinks( 'response' );
			if( $attributes['assigned_to'] == 'post_id' ) {
				$attributes['assigned_to'] = $attributes['post_id'];
			}
		}
		return glsr( Shortcode::class )->buildShortcode( $attributes );
	}

	/**
	 * @return void
	 */
	protected function filterReviewLinks()
	{
		add_filter( 'site-reviews/rendered/template/reviews', function( $template ) {
			return str_replace( '<a', '<a tabindex="-1"', $template );
		});
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

	/**
	 * @param string $field
	 * @return void
	 */
	protected function filterShowMoreLinks( $field )
	{
		add_filter( 'site-reviews/review/wrap/'.$field, function( $value ) {
			$value = preg_replace(
				'/(.*)(<span class="glsr-hidden)(.*)(<\/span>)(.*)/s',
				'$1... <a href="#" class="glsr-read-more" tabindex="-1">'.__( 'Show more', 'site-reviews' ).'</a>$5',
				$value
			);
			return $value;
		});
	}
}
