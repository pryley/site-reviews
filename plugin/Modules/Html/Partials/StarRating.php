<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Contracts\PartialContract;
use GeminiLabs\SiteReviews\Modules\Rating;

class StarRating implements PartialContract
{
	/**
	 * @return void|string
	 * @todo wp_star_rating()
	 */
	public function build( $name, array $args = [] )
	{
		$roundedRating = floor( round( $args['rating'], 1 ) * 2 ) / 2;
		$percentage = glsr( Builder::class )->span([
			'style' => 'width:'.( $roundedRating / Rating::MAX_RATING * 100 ).'%;',
		]);
		return glsr( Builder::class )->span( $percentage, [
			'class' => 'glsr-star-rating',
		]);
	}
}
