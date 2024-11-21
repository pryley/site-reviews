<?php

namespace GeminiLabs\SiteReviews\Widgets;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\WidgetBuilder;
use GeminiLabs\SiteReviews\Modules\Html\WidgetField;

abstract class Widget extends \WP_Widget
{
    public function __construct()
    {
        $className = (new \ReflectionClass($this))->getShortName();
        $className = str_replace('Widget', '', $className);
        $baseId = glsr()->prefix.Str::dashCase($className);
        parent::__construct($baseId, $this->shortcode()->name, [
            'description' => sprintf('%s: %s', glsr()->name, $this->shortcode()->description),
            'name' => $this->shortcode()->name,
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
        $instance = $this->normalizeInstance($instance);
        $notice = _x('This is a legacy widget with limited options, consider switching to the shortcode or block.', 'admin-text', 'site-reviews');
        echo glsr(WidgetBuilder::class)->div([
            'class' => 'notice notice-alt notice-warning inline',
            'style' => 'margin:1em 0;',
            'text' => glsr(WidgetBuilder::class)->p($notice),
        ]);
        $config = wp_parse_args($this->widgetConfig(), [
            'title' => [
                'label' => _x('Widget Title', 'admin-text', 'site-reviews'),
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
        return $this->normalizeInstance($instance);
    }

    /**
     * @param array $args
     * @param array $instance
     *
     * @return void
     */
    public function widget($args, $instance)
    {
        $shortcode = $this->shortcode();
        $args = $this->normalizeArgs($args);
        $html = $shortcode->build($instance, 'widget');
        $title = !empty($shortcode->args['title'])
            ? $args->before_title.$shortcode->args['title'].$args->after_title
            : '';
        echo $args->before_widget.$title.$html.$args->after_widget;
    }

    protected function fieldAssignedTermsOptions(): array
    {
        return Arr::prepend(
            glsr(Database::class)->terms(),
            esc_html_x('— Select —', 'admin-text', 'site-reviews'),
            ''
        );
    }

    protected function fieldTypeOptions(): array
    {
        $types = glsr()->retrieveAs('array', 'review_types');
        if (count($types) > 1) {
            return $types;
        }
        return [];
    }

    /**
     * @param array|string $args
     */
    protected function normalizeArgs($args): Arguments
    {
        $args = wp_parse_args($args, [
            'before_widget' => '',
            'after_widget' => '',
            'before_title' => '<h2 class="glsr-title">',
            'after_title' => '</h2>',
        ]);
        $args = glsr()->filterArray('widget/args', $args, $this->shortcode()->shortcode);
        return glsr()->args($args);
    }

    protected function normalizeInstance(array $instance): array
    {
        $pairs = array_fill_keys(array_keys($instance), '');
        $title = $instance['title'] ?? '';
        $instance = $this->shortcode()->defaults()->merge($instance);
        $instance['title'] = sanitize_text_field($title);
        return shortcode_atts($pairs, $instance);
    }

    protected function renderField($name, array $args, array $instance = []): void
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

    abstract protected function shortcode(): ShortcodeContract;

    protected function widgetConfig(): array
    {
        return [];
    }
}
