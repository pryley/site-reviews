<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Modules\Html\Builder;

abstract class Field
{
	/**
	 * @var Builder
	 */
	protected $builder;

	public function __construct( Builder $builder )
	{
		$this->builder = $builder;
	}

	/**
	 * @return string|void
	 */
	abstract public function build();
}
