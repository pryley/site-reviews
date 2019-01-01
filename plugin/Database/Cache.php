<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database\SqlQueries;

class Cache
{
	const EXPIRY_TIME = WEEK_IN_SECONDS;

	/**
 	 * @return array
	 */
	public function getCloudflareIps()
	{
		$ipAddresses = get_transient( Application::ID.'_cloudflare_ips' );
		if( $ipAddresses === false ) {
			$ipAddresses = array_fill_keys( ['v4', 'v6'], [] );
			foreach( array_keys( $ipAddresses ) as $version ) {
				$response = wp_remote_get( 'https://www.cloudflare.com/ips-'.$version );
				if( is_wp_error( $response )) {
					glsr_log()->error( $response->get_error_message() );
					continue;
				}
				$ipAddresses[$version] = array_filter(
					(array)preg_split( '/\R/', wp_remote_retrieve_body( $response ))
				);
			}
			set_transient( Application::ID.'_cloudflare_ips', $ipAddresses, static::EXPIRY_TIME );
		}
		return $ipAddresses;
	}

	/**
	 * @param string $metaKey
	 * @return array
	 */
	public function getReviewCountsFor( $metaKey )
	{
		$counts = wp_cache_get( Application::ID, $metaKey.'_count' );
		if( $counts === false ) {
			$counts = [];
			$results = glsr( SqlQueries::class )->getReviewCountsFor( $metaKey );
			foreach( $results as $result ) {
				$counts[$result->name] = $result->num_posts;
			}
			wp_cache_set( Application::ID, $counts, $metaKey.'_count' );
		}
		return $counts;
	}

	/**
	 * @return string
	 */
	public function getRemotePostTest()
	{
		$test = get_transient( Application::ID.'_remote_post_test' );
		if( $test === false ) {
			$response = wp_remote_post( 'https://api.wordpress.org/stats/php/1.0/' );
			$test = !is_wp_error( $response )
				&& in_array( $response['response']['code'], range( 200, 299 ))
				? 'Works'
				: 'Does not work';
			set_transient( Application::ID.'_remote_post_test', $test, static::EXPIRY_TIME );
		}
		return $test;
	}
}
