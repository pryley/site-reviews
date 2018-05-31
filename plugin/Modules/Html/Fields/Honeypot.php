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
		$defaults = wp_parse_args( $this->defaults(), [
			'name' => $this->builder->args['text'],
		]);
		$this->builder->args = wp_parse_args( $this->builder->args, $defaults );
		$this->builder->tag = 'input';
		return $this->builder->getOpeningTag();
	}

	/**
	 * @return array
	 */
	public function defaults()
	{
		return [
			'autocomplete' => 'off',
			'style' => 'display:none!important',
			'tabindex' => '-1',
			'type' => 'text',
		];
	}
}
