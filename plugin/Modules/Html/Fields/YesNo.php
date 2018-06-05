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
		$this->mergeFieldArgs();
		$this->builder->tag = 'input';
		return $this->builder->buildFormInput();
	}

	/**
	 * @return array
	 */
	public static function defaults()
	{
		return [
			'class' => 'inline',
		];
	}

	/**
	 * @return array
	 */
	public static function required()
	{
		return [
			'options' => [
				'no' => __( 'No', 'site-reviews' ),
				'yes' => __( 'Yes', 'site-reviews' ),
			],
			'type' => 'radio',
		];
	}
}
