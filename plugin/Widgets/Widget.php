<?php

namespace GeminiLabs\SiteReviews\Widgets;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\WidgetBuilder;
use GeminiLabs\SiteReviews\Modules\Html\WidgetField;

abstract class Widget extends \WP_Widget
{
    public ShortcodeContract $shortcode;

    public function __construct()
    {
        $this->shortcode = $this->widgetShortcode();
        $baseId = glsr()->prefix.Str::dashCase($this->shortcode->tag);
        $description = $this->shortcode->description ?: $this->shortcode->name;
        parent::__construct($baseId, $this->shortcode->name, [
            'description' => sprintf('%s: %s', glsr()->name, $description),
            'name' => $this->shortcode->name,
            'show_instance_in_rest' => true,
        ]);
    }

    /**
     * @param array $instance
     *
     * @return string
     */
    public function form($instance)
    {
        $instance = $this->normalizeInstance(wp_parse_args($instance));
        $notice = _x('This is a legacy widget with limited options, consider switching to the shortcode or block.', 'admin-text', 'site-reviews');
        echo glsr(WidgetBuilder::class)->div([
            'class' => 'notice notice-alt notice-warning inline',
            'style' => 'margin:1em 0;',
            'text' => glsr(WidgetBuilder::class)->p($notice),
        ]);
        $config = wp_parse_args($this->widgetConfig(), [ // prepend
            'title' => [
                'label' => _x('Widget Title', 'admin-text', 'site-reviews'),
                'type' => 'text',
            ],
        ]);
        $config = array_merge($config, [ // append
            'id' => [
                'description' => esc_html_x('This should be a unique value.', 'admin-text', 'site-reviews'),
                'label' => esc_html_x('Custom ID', 'admin-text', 'site-reviews'),
                'type' => 'text',
            ],
            'class' => [
                'description' => esc_html_x('Separate multiple classes with spaces.', 'admin-text', 'site-reviews'),
                'label' => esc_html_x('Additional CSS classes', 'admin-text', 'site-reviews'),
                'type' => 'text',
            ],
        ]);
        foreach ($config as $name => $args) {
            if ('type' === $name && empty($args['options'])) {
                continue;
            }
            $this->renderField($name, $args, $instance);
        }
        return '';
    }

    /**
     * @param array $oldInstance
     *
     * @return array
     */
    public function update($instance, $oldInstance)
    {
        return $this->normalizeInstance(wp_parse_args($instance));
    }

    /**
     * @param array $args
     * @param array $instance
     *
     * @return void
     */
    public function widget($args, $instance)
    {
        $args = $this->normalizeArgs(wp_parse_args($args));
        $html = $this->shortcode->build($instance, 'widget');
        $title = !empty($this->shortcode->args['title'])
            ? $args->before_title.$this->shortcode->args['title'].$args->after_title
            : '';
        echo $args->before_widget.$title.$html.$args->after_widget;
    }

    protected function normalizeArgs(array $args): Arguments
    {
        $args = wp_parse_args($args, [
            'before_widget' => '',
            'after_widget' => '',
            'before_title' => '<h2 class="glsr-title">',
            'after_title' => '</h2>',
        ]);
        $args = glsr()->filterArray('widget/args', $args, $this->shortcode->tag);
        return glsr()->args($args);
    }

    protected function normalizeInstance(array $instance): array
    {
        $atts = $this->shortcode->defaults()->unguardedMerge($instance);
        $pairs = array_fill_keys(array_keys($instance), '');
        return shortcode_atts($pairs, $atts);
    }

    protected function renderField(string $name, array $args, array $instance = []): void
    {
        if (isset($args['name']) && !isset($args['type'])) {
            $args['type'] = $name; // @todo remove in v8.0
        }
        $field = new WidgetField(wp_parse_args($args, compact('name')));
        if (!$field->isValid()) {
            return;
        }
        $value = Arr::get($instance, $field->original_name);
        if ('' !== $value) {
            $field->value = $value;
        }
        $field->id = $this->get_field_id($field->name);
        $field->name = $this->get_field_name($field->name);
        $field->render();
    }

    abstract protected function widgetConfig(): array;

    abstract protected function widgetShortcode(): ShortcodeContract;
}
