<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Modules\Schema;

class SiteReviews
{
	/**
	 * @var array
	 */
	protected $args;

	/**
	 * @var float
	 */
	protected $rating;

	/**
	 * @var object
	 */
	protected $reviews;

	/**
	 * @return void|string
	 */
	public function build( array $args = [] )
	{
		$this->args = $args;
		$this->reviews = glsr( Database::class )->getReviews( $args );
		$this->rating = glsr( Rating::class )->getAverage( $this->reviews->results );
	}
}
