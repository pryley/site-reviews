<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Html\Partials;

use GeminiLabs\SiteReviews\Html\Partials\Base;

class StarRating extends Base
{
	/**
	 * @return string
	 */
	public function render()
	{
		$defaults = [
			'hidden' => false,
			'rating' => 5,
			'schema' => false,
		];
		$this->args = shortcode_atts( $defaults, $this->args );
		$attributes = '';
		if( $this->isAdmin() || !wp_validate_boolean( $this->args['hidden'] )) {
			$class = $this->setForAdmin( ' star-rating' );
			$attributes .= sprintf( ' class="glsr-review-rating%s"', $class );
		}
		return sprintf( '<span%s>%s</span>', $attributes, $this->buildStars() );
	}

	/**
	 * @param int $numberOfStars
	 * @return string
	 */
	protected function buildStars( $numberOfStars = 5 )
	{
		$rating = '';
		if( !$this->isAdmin() && wp_validate_boolean( $this->args['hidden'] )) {
			return $rating;
		}
		$star = $this->setForAdmin( ' star star%s', '%s' );
		$star = sprintf( '<span class="glsr-star%s"></span>', $star );
		$roundedRating = floor( round( $this->args['rating'], 1 ) * 2 ) / 2;
		for( $i = 0; $i < $numberOfStars; $i++ ) {
			if( $roundedRating == ( $i + 0.5 )) {
				$rating .= sprintf( $star, '-half' );
			}
			else if( $roundedRating > $i ) {
				$rating .= sprintf( $star, '-full' );
			}
			else {
				$rating .= sprintf( $star, '-empty' );
			}
		}
		return $rating;
	}

	/**
	 * @return bool
	 */
	protected function isAdmin()
	{
		return is_admin() && !wp_doing_ajax();
	}

	/**
	 * @param string $adminValue
	 * @param string $defaultValue
	 * @return string
	 */
	protected function setForAdmin( $adminValue, $defaultValue = '' )
	{
		return $this->isAdmin() ? $adminValue : $defaultValue;
	}
}
