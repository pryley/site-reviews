<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2017, Paul Ryley
 * @license   GPLv3
 * @since     2.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Database;

interface OptionsContract
{
	/**
	 * Delete a plugin option using dot notation
	 * @param string      $path
	 * @param bool|string $isPluginSetting
	 * @return bool
	 */
	public function deleteOption( $path, $isPluginSetting = false );

	/**
	 * Get an option from the plugin options using dot notation
	 *
	 * @param string      $path
	 * @param mixed       $fallback
	 * @param bool|string $isPluginSetting
	 *
	 * @return mixed
	 */
	public function getOption( $path, $fallback = '', $isPluginSetting = false );

	/**
	 * Get the plugin options database key
	 *
	 * @return string
	 */
	public function getOptionName();

	/**
	 * Get the plugin options array
	 *
	 * @param bool|string $isPluginSetting
	 *
	 * @return array
	 */
	public function getOptions( $isPluginSetting = false );

	/**
	 * Get a value from an array of values using a dot-notation path as reference
	 *
	 * @param string $path
	 * @param mixed  $fallback
	 *
	 * @return null|mixed
	 */
	public function getValueFromPath( $path, $fallback, array $values );

	/**
	 * Removes empty values from an array
	 *
	 * @return array
	 */
	public function removeEmptyValuesFrom( array $array );

	/**
	 * Resets an option to the provided value and returns the old value
	 *
	 * @param string      $path
	 * @param mixed       $value
	 * @param bool|string $isPluginSetting
	 *
	 * @return mixed
	 */
	public function resetOption( $path = '', $value, $isPluginSetting = false );

	/**
	 * Sets an option to the plugin settings array using dot notation
	 *
	 * @param string      $path
	 * @param mixed       $value
	 * @param bool|string $isPluginSetting
	 *
	 * @return bool
	 */
	public function setOption( $path, $value, $isPluginSetting = false );

	/**
	 * Set options array to the plugin settings
	 *
	 * @param array       $options
	 * @param bool|string $isPluginSetting
	 * @return bool
	 */
	public function setOptions( $options, $isPluginSetting = false );

	/**
	 * Set a value to an array of values using a dot-notation path as reference
	 *
	 * @param mixed  $value
	 * @param string $path
	 *
	 * @return array
	 */
	public function setValueToPath( $value, $path, array $values );
}
