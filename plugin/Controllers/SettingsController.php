<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Abstracts\Controller;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Settings;

class SettingsController extends Controller
{
	/**
	 * @param array $input
	 * @return array
	 * @callback register_setting
	 */
	public function callbackRegisterSettings( $input )
	{
		if( !is_array( $input )) {
			$input = ['settings' => []];
		}
		$key = key( $input );
		$message = '';
		if( $key == 'logging' ) {
			$message = _n( 'Logging disabled.', 'Logging enabled.', (int)empty( $input[$key] ), 'site-reviews' );
			glsr( Notice::class )->addSuccess( $message );
		}
		if( $key == 'settings' ) {
			$message = __( 'Settings updated.', 'site-reviews' );
			glsr( Notice::class )->addSuccess( $message );
		}
		$options = array_replace_recursive( glsr_db()->getOptions(), $input );
		$options = $this->sanitizeReviewsForm( $input, $options );
		$options = $this->sanitizeStrings( $input, $options );
		return $options;
	}

	/**
	 * @return void
	 * @action admin_init
	 */
	public function registerSettings()
	{
		$settings = apply_filters( 'site-reviews/settings', ['logging', 'settings'] );
		foreach( $settings as $setting ) {
			register_setting(
				glsr()->id.'-'.$setting,
				glsr_db()->getOptionName(),
				[$this, 'callbackRegisterSettings']
			);
		}
		glsr( Settings::class )->register();
	}

	/**
	 * @return array
	 */
	protected function sanitizeReviewsForm( array $input, array $options )
	{
		if( isset( $input['settings']['reviews-form'] )) {
			$inputForm = $input['settings']['reviews-form'];
			$options['settings']['reviews-form']['required'] = isset( $inputForm['required'] )
				? $inputForm['required']
				: [];
		}
		return $options;
	}

	/**
	 * @return array
	 */
	protected function sanitizeStrings( array $input, array $options )
	{
		if( isset( $input['settings']['strings'] )) {
			$options['settings']['strings'] = array_values( array_filter( $input['settings']['strings'] ));
			array_walk( $options['settings']['strings'], function( &$string ) {
				if( isset( $string['s2'] )) {
					$string['s2'] = wp_strip_all_tags( $string['s2'] );
				}
				if( isset( $string['p2'] )) {
					$string['p2'] = wp_strip_all_tags( $string['p2'] );
				}
			});
		}
		return $options;
	}
}
