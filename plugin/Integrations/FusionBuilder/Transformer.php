<?php

namespace GeminiLabs\SiteReviews\Integrations\FusionBuilder;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Integrations\FusionBuilder\Defaults\ControlDefaults;

class Transformer
{
    public array $args;
    public string $name;
    public string $shortcode;

    public function __construct(string $name, array $args, string $shortcode = '')
    {
        $args = glsr(ControlDefaults::class)->merge(
            wp_parse_args(compact('name'), $args)
        );
        $this->args = $args;
        $this->name = $name;
        $this->shortcode = $shortcode;
        $method = Helper::buildMethodName('transform', $this->args['type']);
        if (method_exists($this, $method)) {
            call_user_func([$this, $method]);
        }
    }

    public function control(): array
    {
        $groups = [ // order is intentional
            'design' => esc_html_x('Design', 'admin-text', 'site-reviews'),
            'general' => esc_html_x('General', 'admin-text', 'site-reviews'),
            'advanced' => esc_html_x('Advanced', 'admin-text', 'site-reviews'),
        ];
        $control = $this->args;
        $control['group'] = $groups[$this->args['group']] ?? ucfirst($this->args['group']);
        return $control;
    }

    public function transformCheckbox(): void
    {
        if (!empty($this->args['options'])) {
            if ('hide' === $this->name) {
                $this->args['heading'] = esc_html_x('Hide', 'admin-text', 'site-reviews');
            }
            $placeholder = $this->args['placeholder'] ?? esc_html_x('Select...', 'admin-text', 'site-reviews');
            $this->args['placeholder'] = $placeholder;
            $this->args['type'] = 'multiple_select';
            $this->args['value'] = $this->args['options'];
        } else {
            $this->args['type'] = 'radio_button_set';
            $this->args['value'] = [
                0 => esc_html_x('No', 'admin-text', 'site-reviews'),
                1 => esc_html_x('Yes', 'admin-text', 'site-reviews'),
            ];
        }
    }

    public function transformNumber(): void
    {
        $this->args['type'] = 'range';
    }

    public function transformSelect(): void
    {
        if (!empty($this->args['options'])) {
            $options = $this->args['options'];
            if (!array_key_exists('', $options)) {
                $placeholder = $this->args['placeholder'] ?? esc_html_x('Select...', 'admin-text', 'site-reviews');
                $options = ['' => $placeholder] + $options;
            }
            $this->args['value'] = $options;
        } elseif (!isset($this->args['options'])) {
            $this->args['ajax'] = glsr()->prefix.'fusion_search_query';
            $this->args['ajax_params'] = [
                'option' => $this->name,
                'shortcode' => $this->shortcode,
            ];
            $this->args['type'] = 'ajax_select';
        }
    }
}
