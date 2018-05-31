<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Database\DefaultsManager;

class OptionManager
{
	/**
	 * @return string
	 */
	public static function databaseKey()
	{
		return glsr( Helper::class )->snakeCase(
			Application::ID.'-v'.explode( '.', glsr()->version )[0]
		);
	}

	/**
	 * @return array
	 */
	public function all()
	{
		$options = get_option( static::databaseKey(), [] );
		if( !is_array( $options )) {
			delete_option( static::databaseKey() );
			$options = ['settings' => []];
		}
		return $options;
	}

	/**
	 * @param string $path
	 * @return bool
	 */
	public function delete( $path )
	{
		$keys = explode( '.', $path );
		$last = array_pop( $keys );
		$options = $this->all();
		$pointer = &$options;
		foreach( $keys as $key ) {
			if( !isset( $pointer[$key] ) || !is_array( $pointer[$key] ))continue;
			$pointer = &$pointer[$key];
		}
		unset( $pointer[$last] );
		return $this->set( $options );
	}

	/**
	 * @param string $path
	 * @param mixed $fallback
	 * @return mixed
	 */
	public function get( $path = '', $fallback = '' )
	{
		return glsr( Helper::class )->getPathValue( $path, $this->all(), $fallback );
	}

	/**
	 * @param string|array $pathOrOptions
	 * @param mixed $value
	 * @return bool
	 */
	public function set( $pathOrOptions, $value = '' )
	{
		if( is_string( $pathOrOptions )) {
			$pathOrOptions = glsr( Helper::class )->setPathValue( $pathOrOptions, $value, $this->all() );
		}
		return update_option( static::databaseKey(), (array)$pathOrOptions );
	}
}
