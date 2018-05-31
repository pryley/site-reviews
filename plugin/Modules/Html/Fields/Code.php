<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Modules\Html\Fields\Field;

class Code extends Field
{
	/**
	 * @return string|void
	 */
	public function build()
	{
	}

	/**
	 * @return array
	 */
	public function defaults()
	{
		return [
			'class' => 'large-text code',
			'type' => 'textarea',
		];
	}
}
