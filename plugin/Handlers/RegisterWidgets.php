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
    public function handle(Command $command)
    {
        global $wp_widget_factory;
        foreach ($command->widgets as $widget) {
            $widgetClass = Helper::buildClassName($widget.'-widget', 'Widgets');
            if (!class_exists($widgetClass)) {
                glsr_log()->error(sprintf('Class missing (%s)', $widgetClass));
                continue;
            }
            $wp_widget_factory->widgets[$widgetClass] = new $widgetClass();
        }
    }
}
