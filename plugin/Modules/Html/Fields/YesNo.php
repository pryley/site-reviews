<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Modules\Html\Fields\Field;

class YesNo extends Field
{
	/**
	 * @return string|void
	 */
	public function build()
	{
		$this->builder->args = wp_parse_args( $this->builder->args, $this->defaults() );
		$this->builder->tag = 'input';
		return $this->builder->buildFormInput();
	}

	/**
	 * @return array
	 */
	public function defaults()
	{
		return [
			'class' => 'inline',
			'options' => [
				'no' => __( 'No', 'site-reviews' ),
				'yes' => __( 'Yes', 'site-reviews' ),
			],
			'type' => 'radio',
		];
	}
}
