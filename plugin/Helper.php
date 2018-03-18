<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2017, Paul Ryley
 * @license   GPLv3
 * @since     2.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\App;
use GeminiLabs\SiteReviews\Database;
use Vectorface\Whip\Whip;

class Helper
{
	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @var Database
	 */
	protected $db;

	public function __construct( App $app, Database $db )
	{
		$this->app = $app;
		$this->db  = $db;
	}

	/**
	 * @param string $name
	 * @param string $path
	 * @return string
	 */
	public function buildClassName( $name, $path = '' )
	{
		$className = array_map( 'ucfirst', array_map( 'strtolower', (array) preg_split( '/[-_]/', $name )));
		$className = implode( '', $className );
		return !empty( $path )
			? str_replace( '\\\\', '\\', sprintf( '%s\%s', $path, $className ))
			: $className;
	}

	/**
	 * @param string $name
	 * @param string $prefix
	 * @return string
	 */
	public function buildMethodName( $name, $prefix = 'get' )
	{
		return $prefix . $this->buildClassName( $name );
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
	 * @param string $prefix
	 * @return array
	 */
	public function flattenArray( array $array, $prefix = '' )
	{
		$result = [];
		foreach( $array as $key => $value ) {
			$newKey = $prefix.( empty( $prefix ) ? '' : '.' ).$key;
			if( $this->isSimpleArray( $value )) {
				$value = '['.implode( ', ', $value ).']';
			}
			if( is_array( $value )) {
				$result = array_merge( $result, $this->flattenArray( $value, $newKey ));
			}
			else {
				$result[$newKey] = $value;
			}
		}
		return $result;
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function get( $name )
	{
		$method = $this->buildMethodName( $name );
		if( !method_exists( $this, $method ))return;
		return call_user_func_array([ $this, $method ], array_slice( func_get_args(), 1 ));
	}

	/**
	 * @return null|string
	 */
	public function getIpAddress()
	{
		$cloudflareHeaders = [];
		$cloudflareIPv4 = wp_remote_get( 'https://www.cloudflare.com/ips-v4' );
		$cloudflareIPv6 = wp_remote_get( 'https://www.cloudflare.com/ips-v6' );
		if( !is_wp_error( $cloudflareIPv4 )) {
			$cloudflareHeaders[Whip::IPV4] = array_filter( explode( PHP_EOL, wp_remote_retrieve_body( $cloudflareIPv4 )));
		}
		if( !is_wp_error( $cloudflareIPv6 )) {
			$cloudflareHeaders[Whip::IPV6] = array_filter( explode( PHP_EOL, wp_remote_retrieve_body( $cloudflareIPv6 )));
		}
		$ipAddress = (new Whip( Whip::CLOUDFLARE_HEADERS | Whip::REMOTE_ADDR, [
			Whip::CLOUDFLARE_HEADERS => $cloudflareHeaders,
		]))->getValidIpAddress();
		return $ipAddress ? $ipAddress : null;
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

	/**
	 * @param string $optionPath
	 * @param mixed  $fallback
	 * @return mixed
	 */
	protected function getOption( $optionPath, $fallback )
	{
		return !empty( $optionPath )
			? $this->db->getOption( $optionPath, $fallback, 'settings' )
			: '';
	}

	/**
	 * @return array
	 */
	protected function getOptions()
	{
		return $this->db->getOptions( 'settings' );
	}

	/**
	 * @param int $postId
	 * @return null|object
	 */
	protected function getReview( $postId )
	{
		return $this->db->getReview( get_post( $postId ));
	}

	/**
	 * @return array
	 */
	protected function getReviews( array $args = [] )
	{
		return $this->db->getReviews( $args )->reviews;
	}

	/**
	 * @param mixed $array
	 * @return bool
	 */
	protected function isSimpleArray( $array )
	{
		if( !is_array( $array ) || array_filter( $array, 'is_array' )) {
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
}
