<?php

namespace GeminiLabs\SiteReviews\Widgets;

use GeminiLabs\SiteReviews\Modules\Html\WidgetBuilder;
use WP_Widget;

abstract class Widget extends WP_Widget
{
    /**
     * @var array
     */
    protected $widgetArgs;

    /**
     * @param array $args
     * @param array $instance
     * @return void
     */
    public function widget($args, $instance)
    {
        echo $this->shortcode()->build($instance, $args, 'widget');
    }

    /**
     * @param string $tag
     * @return array
     */
    protected function normalizeFieldAttributes($tag, array $args)
    {
        if (empty($args['value'])) {
            $args['value'] = $this->widgetArgs[$args['name']];
        }
        if (empty($this->widgetArgs['options']) && in_array($tag, ['checkbox', 'radio'])) {
            $args['checked'] = in_array($args['value'], (array) $this->widgetArgs[$args['name']]);
        }
        $args['id'] = $this->get_field_id($args['name']);
        $args['name'] = $this->get_field_name($args['name']);
        return $args;
    }

    /**
     * @param string $tag
     * @return void
     */
    protected function renderField($tag, array $args = [])
    {
        $args = $this->normalizeFieldAttributes($tag, $args);
        echo glsr(WidgetBuilder::class)->div([
            'class' => 'glsr-field',
            'text' => glsr(WidgetBuilder::class)->$tag($args['name'], $args),
        ]);
    }

    /**
     * @return \GeminiLabs\SiteReviews\Shortcodes\Shortcode
     */
    abstract protected function shortcode();
}
