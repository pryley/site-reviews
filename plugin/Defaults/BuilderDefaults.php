<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class BuilderDefaults extends Defaults
{
	/**
	 * @return array
	 */
	public function defaults()
	{
		return [
			'id' => '',
			'options' => [],
			'text' => '',
			'type' => '',
			'value' => '',
		];
	}
}
