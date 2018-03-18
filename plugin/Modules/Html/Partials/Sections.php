<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Contracts\PartialContract;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class Sections implements PartialContract
{
	/**
	 * @var array
	 */
	protected $args;

	/**
	 * @var array
	 */
	protected $sections;

	/**
	 * @return void|string
	 */
	public function build( $name, array $args = [] )
	{
		$this->normalize( $args );
		if( count( $this->sections ) < 2 )return;
		$links = array_reduce( array_keys( $this->sections ), function( $result, $section ) {
			return $result.glsr( Builder::class )->li( $this->buildLink( $section ));
		});
		return glsr( Builder::class )->ul( $links, ['class' => 'subsubsub glsr-subsubsub'] );
	}

	/**
	 * @return void
	 */
	public function normalize( array $args )
	{
		$this->args = wp_parse_args( $args, [
			'page' => '',
			'section' => '',
			'tab' => '',
			'tabs' => [],
		]);
		$this->sections = $this->args['tabs'][$this->args['tab']]['sections'];
	}

	/**
	 * @param string $section
	 * @return string
	 */
	protected function buildLink( $section )
	{
		$class = $separator = '';
		if( strpos( $this->args['section'], $section ) === 0 ) {
			$class = 'current';
		}
		if( end( $this->sections ) !== $this->sections[$section] ) {
			$separator = ' | ';
		}
		return glsr( Builder::class )->a( $this->sections[$section], [
			'class' => $class,
			'href' => '?post_type='.Application::POST_TYPE.'&page='.$this->args['page'].'&tab='.$this->args['tab'].'&section='.$section,
		]).$separator;
	}
}
