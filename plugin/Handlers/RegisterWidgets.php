<?php

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Commands\RegisterWidgets as Command;
use GeminiLabs\SiteReviews\Helper;

class RegisterWidgets
{
	/**
	 * @return void
	 */
	public function handle( Command $command )
	{
		global $wp_widget_factory;
		foreach( $command->widgets as $key => $values ) {
			$widgetClass = glsr( Helper::class )->buildClassName( $key.'-widget', 'Widgets' );
			if( !class_exists( $widgetClass )) {
				glsr_log()->error( sprintf( 'Class missing (%s)', $widgetClass ));
				continue;
			}
			// Here we bypass register_widget() in order to pass our custom values to the widget
			$widget = new $widgetClass( Application::ID.'_'.$key, $values['title'], $values );
			$wp_widget_factory->widgets[$widgetClass] = $widget;
		}
	}
}
