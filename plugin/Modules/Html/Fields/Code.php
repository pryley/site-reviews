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
		$this->mergeFieldArgs();
		$this->builder->tag = 'textarea';
		return $this->builder->buildTag();
	}

	/**
	 * @return array
	 */
	public static function defaults()
	{
		return [
			'class' => 'large-text code',
		];
	}

	/**
	 * @return array
	 */
	public static function required()
	{
		return [
			'type' => 'textarea',
		];
	}
}
