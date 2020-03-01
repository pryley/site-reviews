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
        foreach ($command->widgets as $baseId => $args) {
            $widgetClass = Helper::buildClassName($baseId.'-widget', 'Widgets');
            if (!class_exists($widgetClass)) {
                glsr_log()->error(sprintf('Widget class missing (%s)', $widgetClass));
                continue;
            }
            register_widget(new $widgetClass(Application::PREFIX.$baseId, $args['name'], $args));
        }
    }
}
