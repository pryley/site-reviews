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
        if ('hide' === $this->name) {
            $this->args['heading'] = esc_html_x('Hide', 'admin-text', 'site-reviews');
        }
        if (!empty($this->args['options'])) {
            $placeholder = $this->args['placeholder'] ?? esc_html_x('Select...', 'admin-text', 'site-reviews');
            $this->args['placeholder_text'] = $placeholder;
            $this->args['type'] = 'multiple_select';
            $this->args['value'] = $this->args['options'];
        } else {
            $this->args['default'] = 0;
            $this->args['type'] = 'radio_button_set';
            $this->args['value'] = [
                0 => esc_html_x('No', 'admin-text', 'site-reviews'),
                'yes' => esc_html_x('Yes', 'admin-text', 'site-reviews'), // because the dependency option doesn't work well with numerical values
            ];
        }
    }

    public function transformNumber(): void
    {
        $this->args['type'] = 'range';
    }

    public function transformSelect(): void
    {
        if ('assigned_posts' === $this->name) {
            $this->args['placeholder'] = esc_html_x('Search Pages...', 'admin-text', 'site-reviews');
        }
        if ('assigned_terms' === $this->name) {
            $this->args['placeholder'] = esc_html_x('Search Categories...', 'admin-text', 'site-reviews');
        }
        if ('assigned_users' === $this->name) {
            $this->args['placeholder'] = esc_html_x('Search Users...', 'admin-text', 'site-reviews');
        }
        if ('author' === $this->name) {
            $this->args['placeholder'] = esc_html_x('Search User...', 'admin-text', 'site-reviews');
        }
        if ('post_id' === $this->name) {
            $this->args['placeholder'] = esc_html_x('Search Review...', 'admin-text', 'site-reviews');
        }
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
            $this->args['placeholder'] ??= esc_html_x('Search...', 'admin-text', 'site-reviews');
            $this->args['type'] = 'ajax_select';
            if (!($this->args['multiple'] ?? false)) {
                $this->args['max_input'] = 1;
            }
        }
    }

    public function transformText(): void
    {
        $this->args['type'] = 'textfield';
        if ('id' === $this->name) {
            $this->args['description'] = esc_html_x('Add an ID to the wrapping HTML element.', 'admin-text', 'site-reviews');
            $this->args['heading'] = esc_html_x('Custom CSS ID', 'admin-text', 'site-reviews');
        }
        if ('class' === $this->name) {
            $this->args['description'] = esc_html_x('Add a class to the wrapping HTML element.', 'admin-text', 'site-reviews');
            $this->args['heading'] = esc_html_x('Custom CSS Class', 'admin-text', 'site-reviews');
        }
    }
}
