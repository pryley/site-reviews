<?php

namespace GeminiLabs\SiteReviews;

use ArrayObject;
use GeminiLabs\SiteReviews\Defaults\SiteReviewsDefaults;
use GeminiLabs\SiteReviews\Modules\Html\Partials\SiteReviews as SiteReviewsPartial;
use GeminiLabs\SiteReviews\Modules\Html\ReviewsHtml;

class Reviews extends ArrayObject
{
	/**
	 * @var array
	 */
	public $args;

	/**
	 * @var int
	 */
	public $max_num_pages;

	/**
	 * @var array
	 */
	public $results;

	public function __construct( array $reviews, $maxPageCount, array $args )
	{
		$this->args = $args;
		$this->max_num_pages = $maxPageCount;
		$this->results = $reviews;
		parent::__construct( $reviews, ArrayObject::STD_PROP_LIST|ArrayObject::ARRAY_AS_PROPS );
	}

	/**
	 * @return mixed
	 */
	public function __get( $key )
	{
		return property_exists( $this, $key )
			? $this->$key
			: null;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return (string)$this->build();
	}

	/**
	 * @return ReviewsHtml
	 */
	public function build()
	{
		$args = glsr( SiteReviewsDefaults::class )->merge( $this->args );
		return glsr( SiteReviewsPartial::class )->build( $args, $this );
	}

	/**
	 * @return void
	 */
	public function render()
	{
		echo $this->build();
	}
}
