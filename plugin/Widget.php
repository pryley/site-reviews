<?php

/**
 * Widget Boilerplate
 *
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

use WP_Widget;

abstract class Widget extends WP_Widget
{
	public function __construct( $id_base, $name, $values )
	{
		$control_options = $widget_options = [];
		if( isset( $values['class'] )) {
			$widget_options['classname'] = $values['class'];
		}
		if( isset( $values['description'] )) {
			$widget_options['description'] = $values['description'];
		}
		if( isset( $values['width'] )) {
			$control_options['width'] = $values['width'];
		}
		parent::__construct( $id_base, $name, $widget_options, $control_options );
	}

	/**
	 * Display the widget form
	 * Subclasses should over-ride this function to generate the widget form
	 *
	 * @param array $instance
	 * @return void
	 */
	public function form( $instance )
	{
		parent::form( $instance );
	}

	/**
	 * Update the widget form
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance )
	{
		return parent::update( $new_instance, $old_instance );
	}

	/**
	 * Display the widget Html
	 * Subclasses should over-ride this function to generate the widget
	 *
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance )
	{
		parent::widget( $args, $instance );
	}

	/**
	 * Create a widget form field
	 *
	 * @return void
	 */
	protected function create_field( array $atts = [] )
	{
		// don't prefix the name field
		$atts['prefix'] = false;
		$atts['name'] = $this->get_field_name( (string) $atts['name'] );
		if( isset( $atts['depends'] )) {
			$atts['depends'] = $this->get_field_name( (string) $atts['depends'] );
		}
		if( !isset( $atts['type'] ) || $atts['type'] == 'text' ) {
			$atts['class'] = isset( $atts['class'] )
				? trim( $atts['class'].' widefat' )
				: 'widefat';
		}
		echo glsr_resolve( 'Html' )->renderField( $atts );
	}
}
