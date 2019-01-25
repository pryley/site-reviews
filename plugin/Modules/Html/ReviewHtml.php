<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

class ReviewHtml implements \ArrayAccess
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

	/**
	 * @param mixed $key
	 * @return bool
	 */
	public function offsetExists( $key )
	{
		return isset( $this->values[$key] );
	}

	/**
	 * @param mixed $key
	 * @return mixed
	 */
	public function offsetGet( $key )
	{
		return isset( $this->values[$key] )
			? $this->values[$key]
			: null;
	}

	/**
	 * @param mixed $key
	 * @param mixed $value
	 * @return void
	 */
	public function offsetSet( $key, $value )
	{
		if( is_null( $key )) {
			$this->values[] = $value;
		}
		else {
			$this->values[$key] = $value;
		}
	}

	/**
	 * @param mixed $key
	 * @return void
	 */
	public function offsetUnset( $key )
	{
		if( !$this->offsetExists( $key ))return;
		$this->offsetSet( $key, null );
	}
}
