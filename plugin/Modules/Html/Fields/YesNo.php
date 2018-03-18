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
		$this->builder->args = wp_parse_args( $this->builder->args, [
			'type' => 'radio',
			'options' => [
				'no' => __( 'No', 'site-reviews' ),
				'yes' => __( 'Yes', 'site-reviews' ),
			],
		]);
		$this->builder->tag = 'input';
		return $this->builder->buildFormInput();
	}
}
