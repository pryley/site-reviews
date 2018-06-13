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
			'class' => '',
			'id' => '',
			'label' => '',
			'options' => [],
			'text' => '',
			'type' => '',
			'value' => '',
		];
	}
}
