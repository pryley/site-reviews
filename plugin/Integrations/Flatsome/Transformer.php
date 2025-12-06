<?php

namespace GeminiLabs\SiteReviews\Integrations\Flatsome;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Integrations\Flatsome\Defaults\ControlDefaults;

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
            $control['description'] = esc_html_x('Flatsome does not support multiple checkboxes here so use the dropdown to select fields that you want to hide.', 'admin-text', 'site-reviews');
        }
        if (!empty($control['options'])) {
            $control['config'] = [
                'multiple' => true,
                'options' => $control['options'],
                'placeholder' => $control['placeholder'] ?? esc_html_x('Select...', 'admin-text', 'site-reviews'),
                'sortable' => false,
            ];
            $control['type'] = 'select';
            unset($control['options']);
            return $control;
        }
        if (!isset($control['options'])) {
            $control['options'] = [
                '' => ['title' => _x('No', 'admin-text', 'site-reviews')],
                'true' => ['title' => _x('Yes', 'admin-text', 'site-reviews')],
            ];
            $control['type'] = 'radio-buttons';
        }
        return $control;
    }

    protected function transformNumber(array $control): array
    {
        $control['type'] = 'slider';
        return $control;
    }

    protected function transformSelect(array $control): array
    {
        $control['config'] = [
            'sortable' => false,
        ];
        if (!empty($control['options'])) {
            if (!isset($control['options'][''])) {
                $placeholder = $control['placeholder'] ?? esc_html_x('Select...', 'admin-text', 'site-reviews');
                $control['options'] = ['' => $placeholder] + $control['options'];
            }
            return $control;
        }
        if (!isset($control['options'])) {
            if ('assigned_terms' === $this->name) {
                $control['config']['termSelect'] = [
                    'taxonomies' => glsr()->taxonomy,
                ];
            } else {
                $control['config']['postSelect'] = glsr()->prefix.$this->name;
            }
            if (str_starts_with($this->name, 'assigned_')) {
                $control['multiple'] = true;
            }
            $control['config']['multiple'] = $control['multiple'] ?? false;
            $control['config']['placeholder'] = $control['placeholder'] ?? esc_html_x('Select...', 'admin-text', 'site-reviews');
            unset($control['multiple']);
            unset($control['placeholder']);
            return $control;
        }
        return []; // invalid select control
    }

    protected function transformText(array $control): array
    {
        $control['type'] = 'textfield';
        return $control;
    }
}
