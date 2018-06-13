<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Modules\Html\Fields\Field;

class Checkbox extends Field
{
	/**
	 * @return array
	 */
	public static function defaults()
	{
		return [
			'value' => 1,
		];
	}

	/**
	 * @return array
	 */
	public static function required()
	{
		return [
			'is_multi' => true,
		];
	}
}
