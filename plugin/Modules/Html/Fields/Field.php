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

	/**
	 * @return array
	 */
	public static function defaults()
	{
		return [];
	}

	/**
	 * @return array
	 */
	public static function required()
	{
		return [];
	}

	/**
	 * @return void
	 */
	protected function mergeFieldArgs()
	{
		$this->builder->args = array_merge(
			wp_parse_args( $this->builder->args, static::defaults() ),
			static::required()
		);
	}
}
