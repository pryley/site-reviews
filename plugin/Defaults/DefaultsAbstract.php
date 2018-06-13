<?php

namespace GeminiLabs\SiteReviews\Defaults;

abstract class DefaultsAbstract
{
	/**
	 * @return array
	 */
	abstract public function defaults();

	/**
	 * @return array
	 */
	public function merge( array $values = [] )
	{
		return wp_parse_args( $values, $this->defaults() );
	}

	/**
	 * @return array
	 */
	public function restrict( array $values = [] )
	{
		return shortcode_atts( $this->defaults(), $values );
	}
}
