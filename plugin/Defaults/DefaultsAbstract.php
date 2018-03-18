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
	public function merge( array $values = [], $restrict = false )
	{
		return $restrict
			? shortcode_atts( $this->defaults(), $values )
			: wp_parse_args( $values, $this->defaults() );
	}
}
