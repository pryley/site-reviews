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
			'autocomplete' => 'off',
			'name' => $this->builder->args['text'],
			'style' => 'display:none!important',
			'tabindex' => '-1',
			'type' => 'text',
		]);
		$this->builder->tag = 'input';
		return $this->builder->getOpeningTag();
	}
}
