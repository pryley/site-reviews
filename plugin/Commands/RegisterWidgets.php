<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Helper;

class RegisterWidgets implements Contract
{
    public $widgets;

    public function __construct(array $input)
    {
        array_walk($input, function (&$args) {
            $args = wp_parse_args($args, [
                'description' => '',
                'name' => '',
            ]);
        });
        $this->widgets = $input;
    }

    /**
     * @return void
     */
    public function handle()
    {
        foreach ($this->widgets as $baseId => $args) {
            $widgetClass = Helper::buildClassName($baseId.'-widget', 'Widgets');
            if (!class_exists($widgetClass)) {
                glsr_log()->error(sprintf('Widget class missing (%s)', $widgetClass));
                continue;
            }
            register_widget(new $widgetClass(glsr()->prefix.$baseId, $args['name'], $args));
        }
    }
}
