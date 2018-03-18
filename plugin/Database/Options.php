<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2017, Paul Ryley
 * @license   GPLv3
 * @since     2.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Database;

/**
 * @property App $app
 */
trait Options
{
	/**
	 * Delete a plugin option using dot notation
	 * @param string      $path
	 * @param bool|string $isPluginSetting
	 * @return bool
	 */
	public function deleteOption( $path, $isPluginSetting = false )
	{
		$keys = explode( '.', $path );
		$last = array_pop( $keys );
		$options = $this->getOptions( $isPluginSetting );
		$pointer = &$options;
		foreach( $keys as $key ) {
			if( isset( $pointer[$key] ) && is_array( $pointer[$key] )) {
				$pointer = &$pointer[$key];
			}
		}
		unset( $pointer[$last] );
		return $this->setOptions( $options, $isPluginSetting );
	}

	/**
	 * Get an option from the plugin options using dot notation
	 *
	 * @param string      $path
	 * @param mixed       $fallback
	 * @param bool|string $isPluginSetting
	 *
	 * @return mixed
	 */
	public function getOption( $path, $fallback = '', $isPluginSetting = false )
	{
		$option = $this->getValueFromPath( $path, $fallback, $this->getOptions( $isPluginSetting ));

		// fallback to default settings
		if( $isPluginSetting && empty( $option )) {

			$defaultPaths = $this->app->getDefaultSettings();

			if( isset( $defaultPaths[ $path ] )) {
				$option = $defaultPaths[ $path ];
			}
		}

		return $option;
	}

	/**
	 * Get the plugin options database key
	 *
	 * @return string
	 */
	public function getOptionName()
	{
		return sprintf( '%s-v%d', $this->app->prefix, explode( '.', $this->app->version )[0] );
	}

	/**
	 * Get the plugin options array
	 *
	 * @param bool|string $isPluginSetting
	 * @return array
	 */
	public function getOptions( $isPluginSetting = false )
	{
		$options = get_option( $this->getOptionName(), [] );
		if( !is_array( $options )) {
			delete_option( $this->getOptionName() );
			$options = [];
		}
		if( $isPluginSetting == 'settings' ) {
			$options = isset( $options['settings'] ) && is_array( $options['settings'] )
				? $options['settings']
				: [];
		}
		return $options;
	}

	/**
	 * Get a value from an array of values using a dot-notation path as reference
	 *
	 * @param string $path
	 * @param mixed  $fallback
	 *
	 * @return null|mixed
	 */
	public function getValueFromPath( $path, $fallback, array $values )
	{
		if( empty( $path ))return;

		$keys = explode( '.', $path );

		foreach( $keys as $key ) {
			if( !isset( $values[ $key ] )) {
				return $fallback;
			}
			$values = $values[ $key ];
		}

		return $values;
	}

	/**
	 * Removes empty values from an array
	 *
	 * @return array
	 */
	public function removeEmptyValuesFrom( array $array )
	{
		$result = [];

		foreach( $array as $key => $value ) {
			if( !$value )continue;
			$result[ $key ] = is_array( $value )
				? $this->removeEmptyValuesFrom( $value )
				: $value;
		}

		return $result;
	}

	/**
	 * Resets an option to the provided value and returns the old value
	 *
	 * @param string      $path
	 * @param mixed       $value
	 * @param bool|string $isPluginSetting
	 *
	 * @return mixed
	 */
	public function resetOption( $path = '', $value, $isPluginSetting = false )
	{
		$option = $this->getOption( $path, '', $isPluginSetting );

		$this->setOption( $path, $value, $isPluginSetting );

		return $option;
	}

	/**
	 * Sets an option to the plugin settings array using dot notation
	 *
	 * @param string      $path
	 * @param mixed       $value
	 * @param bool|string $isPluginSetting
	 *
	 * @return bool
	 */
	public function setOption( $path, $value, $isPluginSetting = false )
	{
		$options = $this->getOptions();

		if( $isPluginSetting ) {
			$options['settings'] = $this->setValueToPath( $value, $path, $options['settings'] );
		}
		else {
			$options = $this->setValueToPath( $value, $path, $options );
		}

		return update_option( $this->getOptionName(), $options );
	}

	/**
	 * Set options array to the plugin settings
	 *
	 * @param array       $options
	 * @param bool|string $isPluginSetting
	 * @return bool
	 */
	public function setOptions( $options, $isPluginSetting = false )
	{
		$newOptions = $this->getOptions();
		if( $isPluginSetting ) {
			$newOptions['settings'] = $options;
		}
		else {
			$newOptions = $options;
		}
		return update_option( $this->getOptionName(), $newOptions );
	}

	/**
	 * Set a value to an array of values using a dot-notation path as reference
	 *
	 * @param mixed  $value
	 * @param string $path
	 *
	 * @return array
	 */
	public function setValueToPath( $value, $path, array $values )
	{
		$token = strtok( $path, '.' );

		$ref = &$values;

		while( $token !== false ) {
			$ref = is_array( $ref ) ? $ref : [];
			$ref = &$ref[ $token ];
			$token = strtok( '.' );
		}

		$ref = $value;

		return $values;
	}
}
