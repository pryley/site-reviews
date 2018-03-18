<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2017, Paul Ryley
 * @license   GPLv3
 * @since     2.12.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Commands\SubmitReview;

class Blacklist
{
	/**
	 * @return bool
	 */
	public function isBlacklisted( SubmitReview $review )
	{
		$target = implode( "\n", array_filter([
			$review->author,
			$review->content,
			$review->email,
			$review->ipAddress,
			$review->title,
		]));
		return (bool) apply_filters( 'site-reviews/blacklist/is-blacklisted',
			$this->check( $target ),
			$review
		);
	}

	/**
	 * @param string $target
	 * @return bool
	 */
	protected function check( $target )
	{
		$blacklist = trim( glsr_get_option( 'reviews-form.blacklist.entries' ));
		if( empty( $blacklist )) {
			return false;
		}
		$lines = explode( "\n", $blacklist );
		foreach( (array) $lines as $line ) {
			$line = trim( $line );
			if( empty( $line ) || 256 < strlen( $line ))continue;
			$pattern = sprintf( '#%s#i', preg_quote( $line, '#' ));
			if( preg_match( $pattern, $target )) {
				return true;
			}
		}
		return false;
	}
}
