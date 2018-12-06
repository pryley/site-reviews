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
	public function build()
	{
		glsr_log()->error( 'Build method is not implemented for '.get_class( $this ));
	}

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
