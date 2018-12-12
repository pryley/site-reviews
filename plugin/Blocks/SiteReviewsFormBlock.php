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
		if( filter_input( INPUT_GET, 'context' ) == 'edit' ) {
			$this->filterFormFields();
			$this->filterRatingField();
			$this->filterShortcodeClass();
			$this->filterSubmitButton();
		}
		return glsr( Shortcode::class )->buildShortcode( $attributes );
	}

	/**
	 * @return void
	 */
	protected function filterFormFields()
	{
		add_filter( 'site-reviews/config/forms/submission-form', function( array $config ) {
			array_walk( $config, function( &$field ) {
				$field['disabled'] = true;
				$field['tabindex'] = '-1';
			});
			return $config;
		});
	}

	/**
	 * @return void
	 */
	protected function filterRatingField()
	{
		add_filter( 'site-reviews/rendered/field', function( $html, $type, $args ) {
			if( $args['path'] == 'rating' ) {
				$stars = '<span class="glsr-stars">';
				$stars.= str_repeat( '<span class="glsr-star glsr-star-empty" aria-hidden="true"></span>', 5 );
				$stars.= '</span>';
				$html = preg_replace( '/(.*)(<select.*)(<\/select>)(.*)/', '$1'.$stars.'$4', $html );
			}
			return $html;
		}, 10, 3 );
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
	 * @return void
	 */
	protected function filterSubmitButton()
	{
		add_filter( 'site-reviews/rendered/template/form/submit-button', function( $template ) {
			return str_replace( 'type="submit"', 'tabindex="-1"', $template );
		});
	}
}



