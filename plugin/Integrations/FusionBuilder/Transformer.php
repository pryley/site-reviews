<?php

namespace GeminiLabs\SiteReviews\Integrations\FusionBuilder;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Integrations\FusionBuilder\Defaults\ControlDefaults;

class Transformer
{
    public string $name;
    public string $shortcode;

    protected array $control;

    public function __construct(string $name, array $control, string $shortcode = '')
    {
        $this->name = $name;
        $this->shortcode = $shortcode;
        $control = wp_parse_args(compact('name'), $control);
        $control = glsr(ControlDefaults::class)->merge($control);
        $method = Helper::buildMethodName('transform', $control['type']);
        $this->control = method_exists($this, $method)
            ? call_user_func([$this, $method], $control)
            : $control;
    }

    public function control(): array
    {
        return $this->control;
    }

    protected function transformCheckbox(array $control): array
    {
        if ('hide' === $this->name) {
            $control['heading'] = esc_html_x('Hide', 'admin-text', 'site-reviews');
        }
        if (!empty($control['options'])) {
            $placeholder = $control['placeholder'] ?? esc_html_x('Select...', 'admin-text', 'site-reviews');
            $control['placeholder_text'] = $placeholder;
            $control['type'] = 'multiple_select';
            $control['value'] = $control['options'];
        } else {
            $control['default'] = 0;
            $control['type'] = 'radio_button_set';
            $control['value'] = [
                0 => esc_html_x('No', 'admin-text', 'site-reviews'),
                'yes' => esc_html_x('Yes', 'admin-text', 'site-reviews'), // because the dependency option doesn't work well with numerical values
            ];
        }
        return $control;
    }

    protected function transformNumber(array $control): array
    {
        $control['type'] = 'range';
        return $control;
    }

    protected function transformSelect(array $control): array
    {
        if ('assigned_posts' === $this->name) {
            $control['placeholder'] = esc_html_x('Search Pages...', 'admin-text', 'site-reviews');
        }
        if ('assigned_terms' === $this->name) {
            $control['placeholder'] = esc_html_x('Search Categories...', 'admin-text', 'site-reviews');
        }
        if ('assigned_users' === $this->name) {
            $control['placeholder'] = esc_html_x('Search Users...', 'admin-text', 'site-reviews');
        }
        if ('author' === $this->name) {
            $control['placeholder'] = esc_html_x('Search User...', 'admin-text', 'site-reviews');
        }
        if ('post_id' === $this->name) {
            $control['placeholder'] = esc_html_x('Search Review...', 'admin-text', 'site-reviews');
        }
        if (!empty($control['options'])) {
            $options = $control['options'];
            if (!array_key_exists('', $options)) {
                $placeholder = $control['placeholder'] ?? esc_html_x('Select...', 'admin-text', 'site-reviews');
                $options = ['' => $placeholder] + $options;
            }
            $control['value'] = $options;
            return $control;
        }
        if (!isset($control['options'])) {
            $control['ajax'] = glsr()->prefix.'fusion_search_query';
            $control['ajax_params'] = [
                'option' => $this->name,
                'shortcode' => $this->shortcode,
            ];
            $control['placeholder'] ??= esc_html_x('Search...', 'admin-text', 'site-reviews');
            $control['type'] = 'ajax_select';
            if (!($control['multiple'] ?? false)) {
                $control['max_input'] = 1;
            }
            return $control;
        }
        return []; // this is an invalid select control
    }

    protected function transformText(array $control): array
    {
        $control['type'] = 'textfield';
        if ('id' === $this->name) {
            $control['description'] = esc_html_x('Add an ID to the wrapping HTML element.', 'admin-text', 'site-reviews');
            $control['heading'] = esc_html_x('Custom CSS ID', 'admin-text', 'site-reviews');
        }
        if ('class' === $this->name) {
            $control['description'] = esc_html_x('Add a class to the wrapping HTML element.', 'admin-text', 'site-reviews');
            $control['heading'] = esc_html_x('Custom CSS Class', 'admin-text', 'site-reviews');
        }
        return $control;
    }
}
