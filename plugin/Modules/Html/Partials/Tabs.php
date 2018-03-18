<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Contracts\PartialContract;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class Tabs implements PartialContract
{
	/**
	 * @var array
	 */
	protected $args;

	/**
	 * @return void|string
	 */
	public function build( $name, array $args = [] )
	{
		$this->normalize( $args );
		if( count( $this->args['tabs'] ) < 2 )return;
		$links = array_reduce( array_keys( $this->args['tabs'] ), function( $result, $tab ) {
			return $result.$this->buildLink( $tab );
		});
		return glsr( Builder::class )->h2( $links, ['class' => 'nav-tab-wrapper'] );
	}

	/**
	 * @return void
	 */
	public function normalize( array $args )
	{
		$this->args = wp_parse_args( $args, [
			'page' => '',
			'tab' => '',
			'tabs' => [],
		]);
	}

	/**
	 * @param string $tab
	 * @return string
	 */
	protected function buildLink( $tab )
	{
		$class = strpos( $this->args['tab'], $tab ) === 0
			? ' nav-tab-active'
			: '';
		return glsr( Builder::class )->a( $this->args['tabs'][$tab]['title'], [
			'class' => 'nav-tab'.$class,
			'href' => '?post_type='.Application::POST_TYPE.'&page='.$this->args['page'].'&tab='.$tab,
		]);
	}
}
