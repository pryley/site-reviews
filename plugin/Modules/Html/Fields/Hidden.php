<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Modules\Html\Fields\Field;

class Hidden extends Field
{
	/**
	 * @return string|void
	 */
	public function build()
	{
		$this->mergeFieldArgs();
		return $this->builder->getOpeningTag();
	}

	/**
	 * @return array
	 */
	public static function required()
	{
		return [
			'is_raw' => true,
		];
	}
}
