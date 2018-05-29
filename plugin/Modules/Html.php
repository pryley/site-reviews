<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Form;
use GeminiLabs\SiteReviews\Modules\Html\Partial;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Notice;

class Html
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
	 * @return Builder
	 */
	public function build( array $globals = [] )
	{
		$this->builder->globals = $globals;
		$this->builder->render = false;
		return $this->builder;
	}

	/**
	 * @param string $path
	 * @return void|string
	 */
	public function buildForm( $path, array $fields = [] )
	{
		return glsr( Form::class )->build( $path, $fields );
	}

	/**
	 * @param string $partialPath
	 * @return string
	 */
	public function buildPartial( $partialPath, array $args = [] )
	{
		return glsr( Partial::class )->build( $partialPath, $args );
	}

	/**
	 * @param string $templatePath
	 * @return void|string
	 */
	public function buildTemplate( $templatePath, array $args = [] )
	{
		return glsr( Template::class )->build( $templatePath, $args );
	}

	/**
	 * @return Builder
	 */
	public function render( array $globals = [] )
	{
		$this->builder->globals = $globals;
		$this->builder->render = true;
		return $this->builder;
	}

	/**
	 * @return void
	 */
	public function renderForm( $path, array $fields = [] )
	{
		echo $this->buildForm( $path, $fields );
	}

	/**
	 * @return void
	 */
	public function renderNotices()
	{
		$this->render()->div( glsr( Notice::class )->get(), [
			'id' => 'glsr-notices',
		]);
	}

	/**
	 * @param string $partialPath
	 * @return void
	 */
	public function renderPartial( $partialPath, array $args = [] )
	{
		echo $this->buildPartial( $partialPath, $args );
	}

	/**
	 * @param string $templatePath
	 * @return void
	 */
	public function renderTemplate( $templatePath, array $args = [] )
	{
		echo $this->buildTemplate( $templatePath, $args );
	}
}
