<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database\SqlQueries;

class Cache
{
	/**
 	 * @return array
	 */
	public function getCloudflareIps()
	{
		$ipAddresses = wp_cache_get( Application::ID, '_cloudflare_ips' );
		if( $ipAddresses === false ) {
			$ipAddresses = array_fill_keys( ['v4', 'v6'], [] );
			foreach( array_keys( $ipAddresses ) as $version ) {
				$response = wp_remote_get( 'https://www.cloudflare.com/ips-'.$version );
				if( is_wp_error( $response )) {
					glsr_log()->error( $response->get_error_message() );
					continue;
				}
				$ipAddresses[$version] = array_filter( explode( PHP_EOL, wp_remote_retrieve_body( $response )));
			}
			wp_cache_set( Application::ID, $ipAddresses, '_cloudflare_ips' );
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
			$results = glsr( SqlQueries::class )->getReviewCounts( $metaKey );
			foreach( $results as $result ) {
				$counts[$result->name] = $result->num_posts;
			}
			wp_cache_set( Application::ID, $counts, $metaKey.'_count' );
		}
		return $counts;
	}
}
