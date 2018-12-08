<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Modules\Html\Fields\Field;
use GeminiLabs\SiteReviews\Modules\Rating as RatingModule;

class Rating extends Field
{
	/**
	 * @return string|void
	 */
	public function build()
	{
		$this->builder->tag = 'select';
		$this->mergeFieldArgs();
		return $this->builder->getTag();
	}

	/**
	 * @return array
	 */
	public static function required()
	{
		$options = ['' => __( 'Select a Rating', 'site-reviews' )];
		foreach( range( RatingModule::MAX_RATING, 1 ) as $rating ) {
			$options[$rating] = sprintf( _n( '%s Star', '%s Stars', $rating, 'site-reviews' ), $rating );
		}
		return [
			'class' => 'glsr-star-rating',
			'options' => $options,
			'type' => 'select',
		];
	}
}
