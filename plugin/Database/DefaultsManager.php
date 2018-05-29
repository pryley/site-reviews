<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;

class DefaultsManager
{
	/**
	 * @return array
	 */
	public function get()
	{
		$settings = $this->settings();
		$defaults = array_combine( array_keys( $settings ), array_column( $settings, 'default' ));
		return glsr( Helper::class )->convertDotNotationArray( $defaults );
	}

	/**
	 * @return array
	 */
	public function set()
	{
		$settings = glsr( OptionManager::class )->all();
		$currentSettings = glsr( Helper::class )->removeEmptyArrayValues( $settings );
		$defaultSettings = array_replace_recursive( $this->get(), $currentSettings );
		$updatedSettings = array_replace_recursive( $settings, $defaultSettings );
		update_option( OptionManager::databaseKey(), $updatedSettings );
		return $defaultSettings;
	}

	/**
	 * @return array
	 */
	public function settings()
	{
		$settings = include glsr()->path( 'config/settings.php' );
		$settings = apply_filters( 'site-reviews/addon/settings', $settings );
		return $this->normalize( $settings );
	}

	/**
	 * @return array
	 */
	protected function normalize( array $settings )
	{
		array_walk( $settings, function( &$setting ) {
			if( isset( $setting['default'] ))return;
			$setting['default'] = null;
		});
		return $settings;
	}
}
