<?php

namespace GeminiLabs\SiteReviews\Widgets;

use GeminiLabs\SiteReviews\Modules\Html\Builder;
use WP_Widget;

abstract class Widget extends WP_Widget
{
	/**
	 * @var array
	 */
	protected $widgetArgs;

	public function __construct( $idBase, $name, $values )
	{
		$controlOptions = $widgetOptions = [];
		if( isset( $values['class'] )) {
			$widgetOptions['classname'] = $values['class'];
		}
		if( isset( $values['description'] )) {
			$widgetOptions['description'] = $values['description'];
		}
		if( isset( $values['width'] )) {
			$controlOptions['width'] = $values['width'];
		}
		parent::__construct( $idBase, $name, $widgetOptions, $controlOptions );
	}

	/**
	 * @param string $tag
	 * @return void
	 */
	protected function renderField( $tag, array $args = [] )
	{
		$args = $this->normalizeFieldAttributes( $tag, $args );
		$field = glsr( Builder::class )->{$tag}( $args['name'], $args );
		echo glsr( Builder::class )->div( $field, [
			'class' => 'glsr-field',
		]);
	}

	/**
	 * @param string $tag
	 * @return array
	 */
	protected function normalizeFieldAttributes( $tag, array $args )
	{
		if( empty( $args['value'] )) {
			$args['value'] = $this->widgetArgs[$args['name']];
		}
		if( empty( $this->widgetArgs['options'] ) && in_array( $tag, ['checkbox', 'radio'] )) {
			$args['checked'] = in_array( $args['value'], (array)$this->widgetArgs[$args['name']] );
		}
		$args['id'] = $this->get_field_id( $args['name'] );
		$args['name'] = $this->get_field_name( $args['name'] );
		$args['is_widget'] = true;
		return $args;
	}
}
