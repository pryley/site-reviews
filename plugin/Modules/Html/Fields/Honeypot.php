<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Modules\Html\Fields\Field;

class Honeypot extends Field
{
	/**
	 * @return string|void
	 */
	public function build()
	{
		$this->builder->args = wp_parse_args( $this->builder->args, [
			'name' => $this->builder->args['text'],
		]);
		$this->builder->tag = 'input';
		$this->mergeFieldArgs();
		return $this->builder->getOpeningTag();
	}

	/**
	 * @return array
	 */
	public static function required()
	{
		return [
			'autocomplete' => 'off',
			'is_raw' => true,
			'style' => 'display:none!important',
			'tabindex' => '-1',
			'type' => 'text',
		];
	}
}
