<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Controllers\Controller;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Polylang;

class SettingsController extends Controller
{
	/**
	 * @param mixed $input
	 * @return array
	 * @callback register_setting
	 */
	public function callbackRegisterSettings( $input )
	{
		if( !is_array( $input )) {
			$input = ['settings' => []];
		}
		if( key( $input ) == 'settings' ) {
			$options = array_replace_recursive( glsr( OptionManager::class )->all(), $input );
			$options = $this->sanitizeGeneral( $input, $options );
			$options = $this->sanitizeSubmissions( $input, $options );
			$options = $this->sanitizeTranslations( $input, $options );
			if( filter_input( INPUT_POST, 'option_page' ) == Application::ID.'-settings' ) {
				glsr( Notice::class )->addSuccess( __( 'Settings updated.', 'site-reviews' ));
			}
			return $options;
		}
		return $input;
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
		$inputForm = $input['settings']['general'];
		if( $inputForm['support']['polylang'] == 'yes' && !$this->isPolylangActiveAndSupported() ) {
			$options['settings']['general']['support']['polylang'] = 'no';
		}
		if( trim( $inputForm['notification_message'] ) == '' ) {
			$options['settings']['general']['notification_message'] = glsr()->defaults['settings']['general']['notification_message'];
		}
		$options['settings']['general']['notifications'] = isset( $inputForm['notifications'] )
			? $inputForm['notifications']
			: [];
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
			$allowedTags = ['a' => ['class' => [], 'href' => [], 'target' => []]];
			array_walk( $options['settings']['strings'], function( &$string ) use( $allowedTags ) {
				if( isset( $string['s2'] )) {
					$string['s2'] = wp_kses( $string['s2'], $allowedTags );
				}
				if( isset( $string['p2'] )) {
					$string['p2'] = wp_kses( $string['p2'], $allowedTags );
				}
			});
		}
		return $options;
	}

	/**
	 * @return bool
	 */
	protected function isPolylangActiveAndSupported()
	{
		if( !glsr( Polylang::class )->isActive() ) {
			glsr( Notice::class )->addError( __( 'Please install/activate the Polylang plugin to enable integration.', 'site-reviews' ));
			return false;
		}
		else if( !glsr( Polylang::class )->isSupported() ) {
			glsr( Notice::class )->addError( __( 'Please update the Polylang plugin to v2.3.0 or greater to enable integration.', 'site-reviews' ));
			return false;
		}
		return true;
	}
}
