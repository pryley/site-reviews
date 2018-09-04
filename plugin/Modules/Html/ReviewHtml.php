<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

class ReviewHtml
{
	/**
	 * @var array
	 */
	public $values;

	public function __construct( array $values )
	{
		$this->values = $values;
	}

	/**
	 * @return string
	 */
	public function __get( $key )
	{
		if( array_key_exists( $key, $this->values )) {
			return $this->values[$key];
		}
		return '';
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return array_reduce( $this->values, function( $carry, $value ) {
			return $carry.$value;
		});
	}
}
