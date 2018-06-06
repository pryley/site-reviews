<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Modules\Html\Fields\Field;

class Button extends Field
{
	/**
	 * @return array
	 */
	public static function defaults()
	{
		return [
			'class' => 'button glsr-button',
		];
	}
}
