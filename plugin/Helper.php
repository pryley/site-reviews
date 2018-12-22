<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Database\Cache;
use Vectorface\Whip\Whip;

class Helper
{
	/**
	 * @param string $name
	 * @param string $path
	 * @return string
	 */
	public function buildClassName( $name, $path = '' )
	{
		$className = $this->camelCase( $name );
		$path = ltrim( str_replace( __NAMESPACE__, '', $path ), '\\' );
		return !empty( $path )
			? __NAMESPACE__.'\\'.$path.'\\'.$className
			: $className;
	}

	/**
	 * @param string $name
	 * @param string $prefix
	 * @return string
	 */
	public function buildMethodName( $name, $prefix = '' )
	{
		return lcfirst( $prefix.$this->buildClassName( $name ));
	}

	/**
	 * @param string $name
	 * @return string
	 */
	public function buildPropertyName( $name )
	{
		return lcfirst( $this->buildClassName( $name ));
	}

	/**
	 * @param string $string
	 * @return string
	 */
	public function camelCase( $string )
	{
		$string = ucwords( str_replace( ['-', '_'], ' ', trim( $string )));
		return str_replace( ' ', '', $string );
	}

	/**
	 * @return bool
	 */
	public function compareArrays( array $arr1, array $arr2 )
	{
		sort( $arr1 );
		sort( $arr2 );
		return $arr1 == $arr2;
	}

	/**
	 * @return array
	 */
	public function convertDotNotationArray( array $array )
	{
		$results = [];
		foreach( $array as $path => $value ) {
			$results = $this->setPathValue( $path, $value, $results );
		}
		return $results;
	}

	/**
	 * @param string $name
	 * @return string
	 */
	public function convertPathToId( $path, $prefix = '' )
	{
		return str_replace( ['[', ']'], ['-', ''], $this->convertPathToName( $path, $prefix ));
	}

	/**
	 * @param string $path
	 * @return string
	 */
	public function convertPathToName( $path, $prefix = '' )
	{
		$levels = explode( '.', $path );
		return array_reduce( $levels, function( $result, $value ) {
			return $result.= '['.$value.']';
		}, $prefix );
	}

	/**
	 * @param string $string
	 * @param mixed $callback
	 * @return array
	 */
	public function convertStringToArray( $string, $callback = null )
	{
		$array = array_map( 'trim', explode( ',', $string ));
		return $callback
			? array_filter( $array, $callback )
			: array_filter( $array );
	}

	/**
	 * @param string $string
	 * @return string
	 */
	public function dashCase( $string )
	{
		return str_replace( '_', '-', $this->snakeCase( $string ));
	}

	/**
	 * @param string $needle
	 * @param string $haystack
	 * @return bool
	 */
	public function endsWith( $needle, $haystack )
	{
		$length = strlen( $needle );
		return $length != 0
			? substr( $haystack, -$length ) === $needle
			: true;
	}

	/**
	 * @param string $key
	 * @return mixed
	 */
	public function filterInput( $key, array $request = [] )
	{
		if( isset( $request[$key] )) {
			return $request[$key];
		}
		$variable = filter_input( INPUT_POST, $key );
		if( is_null( $variable ) && isset( $_POST[$key] )) {
			$variable = $_POST[$key];
		}
		return $variable;
	}

	/**
	 * @param string $key
	 * @return array
	 */
	public function filterInputArray( $key )
	{
		$variable = filter_input( INPUT_POST, $key, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if( empty( $variable ) && !empty( $_POST[$key] ) && is_array( $_POST[$key] )) {
			$variable = $_POST[$key];
		}
		return (array)$variable;
	}

	/**
	 * @param bool $flattenValue
	 * @param string $prefix
	 * @return array
	 */
	public function flattenArray( array $array, $flattenValue = false, $prefix = '' )
	{
		$result = [];
		foreach( $array as $key => $value ) {
			$newKey = ltrim( $prefix.'.'.$key, '.' );
			if( $this->isIndexedFlatArray( $value )) {
				if( $flattenValue ) {
					$value = '['.implode( ', ', $value ).']';
				}
			}
			else if( is_array( $value )) {
				$result = array_merge( $result, $this->flattenArray( $value, $flattenValue, $newKey ));
				continue;
			}
			$result[$newKey] = $value;
		}
		return $result;
	}

	/**
	 * @return string
	 */
	public function getIpAddress()
	{
		$cloudflareIps = glsr( Cache::class )->getCloudflareIps();
		$ipv6 = defined( 'AF_INET6' )
			? $cloudflareIps['v6']
			: [];
		return (string)(new Whip( Whip::CLOUDFLARE_HEADERS | Whip::REMOTE_ADDR, [
			Whip::CLOUDFLARE_HEADERS => [
				Whip::IPV4 => $cloudflareIps['v4'],
				Whip::IPV6 => $ipv6,
			],
		]))->getValidIpAddress();
	}

	/**
	 * Get a value from an array of values using a dot-notation path as reference
	 * @param string $path
	 * @param mixed $fallback
	 * @return void|mixed
	 */
	public function getPathValue( $path = '', array $values, $fallback = '' )
	{
		$keys = explode( '.', $path );
		foreach( $keys as $key ) {
			if( !isset( $values[$key] )) {
				return $fallback;
			}
			$values = $values[$key];
		}
		return $values;
	}

	/**
	 * @param mixed $array
	 * @return bool
	 */
	public function isIndexedArray( $array )
	{
		if( !is_array( $array )) {
			return false;
		}
		$current = 0;
		foreach( array_keys( $array ) as $key ) {
			if( $key !== $current ) {
				return false;
			}
			$current++;
		}
		return true;
	}

	/**
	 * @param mixed $array
	 * @return bool
	 */
	public function isIndexedFlatArray( $array )
	{
		if( !is_array( $array ) || array_filter( $array, 'is_array' )) {
			return false;
		}
		return $this->isIndexedArray( $array );
	}

	/**
	 * @param string $string
	 * @param string $prefix
	 * @return string
	 */
	public function prefixString( $string, $prefix = '' )
	{
		return $prefix.str_replace( $prefix, '', trim( $string ));
	}

	/**
	 * @return array
	 */
	public function removeEmptyArrayValues( array $array )
	{
		$result = [];
		foreach( $array as $key => $value ) {
			if( !$value )continue;
			$result[$key] = is_array( $value )
				? $this->removeEmptyArrayValues( $value )
				: $value;
		}
		return $result;
	}

	/**
	 * @param string $prefix
	 * @param string $text
	 * @return string
	 */
	public function removePrefix( $prefix, $text ) {
		return 0 === strpos( $text, $prefix )
			? substr( $text, strlen( $prefix ))
			: $text;
	}

	/**
	 * Set a value to an array of values using a dot-notation path as reference
	 * @param string $path
	 * @param mixed $value
	 * @return array
	 */
	public function setPathValue( $path, $value, array $values )
	{
		$token = strtok( $path, '.' );
		$ref = &$values;
		while( $token !== false ) {
			$ref = is_array( $ref )
				? $ref
				: [];
			$ref = &$ref[$token];
			$token = strtok( '.' );
		}
		$ref = $value;
		return $values;
	}

	/**
	 * @param string $string
	 * @return string
	 */
	public function snakeCase( $string )
	{
		if( !ctype_lower( $string )) {
			$string = preg_replace( '/\s+/u', '', $string );
			$string = preg_replace( '/(.)(?=[A-Z])/u', '$1_', $string );
			$string = function_exists( 'mb_strtolower' )
				? mb_strtolower( $string, 'UTF-8' )
				: strtolower( $string );
		}
		return str_replace( '-', '_', $string );
	}

	/**
	 * @param string $needle
	 * @param string $haystack
	 * @return bool
	 */
	public function startsWith( $needle, $haystack )
	{
		return substr( $haystack, 0, strlen( $needle )) === $needle;
	}
}
