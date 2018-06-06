<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Modules\Html\Fields\Field;

class Hidden extends Field
{
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
