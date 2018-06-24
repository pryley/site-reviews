<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Controllers\Controller;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Notice;

class SettingsController extends Controller
{
	/**
	 * @param mixed $input
	 * @return array
	 * @callback register_setting
	 */
	public function callbackRegisterSettings( $input )
	{
		static $triggered = false;
		if( $triggered === true ) {
			return $input;
		}
		$triggered = true;
		if( !is_array( $input )) {
			$input = ['settings' => []];
		}
		if( key( $input ) == 'settings' ) {
			glsr( Notice::class )->addSuccess( __( 'Settings updated.', 'site-reviews' ));
		}
		$options = array_replace_recursive( glsr( OptionManager::class )->all(), $input );
		$options = $this->sanitizeGeneral( $input, $options );
		$options = $this->sanitizeSubmissions( $input, $options );
		$options = $this->sanitizeTranslations( $input, $options );
		return $options;
	}

	/**
	 * @return void
	 * @action admin_init
	 */
	public function registerSettings()
	{
		register_setting( Application::ID.'-settings', OptionManager::databaseKey(), [
			'sanitize_callback' => [$this, 'callbackRegisterSettings'],
		]);
	}

	/**
	 * @return array
	 */
	protected function sanitizeGeneral( array $input, array $options )
	{
		if( trim( $input['settings']['general']['notification_message'] ) == '' ) {
			$options['settings']['general']['notification_message'] = glsr()->defaults['settings']['general']['notification_message'];
		}
		return $options;
	}

	/**
	 * @return array
	 */
	protected function sanitizeSubmissions( array $input, array $options )
	{
		$inputForm = $input['settings']['submissions'];
		$options['settings']['submissions']['required'] = isset( $inputForm['required'] )
			? $inputForm['required']
			: [];
		return $options;
	}

	/**
	 * @return array
	 */
	protected function sanitizeTranslations( array $input, array $options )
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
