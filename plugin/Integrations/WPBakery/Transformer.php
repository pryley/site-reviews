<?php

namespace GeminiLabs\SiteReviews\Integrations\WPBakery;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Integrations\WPBakery\Defaults\ControlDefaults;

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
            $control['heading'] = _x('Hide', 'admin-text', 'site-reviews');
        }
        if (!empty($control['options'])) {
            $control['value'] = array_flip($control['options']);
        } elseif (empty($control['value'])) {
            $control['value'] = [esc_html_x('Yes', 'admin-text', 'site-reviews') => 'true'];
        }
        unset($control['options']);
        return $control;
    }

    protected function transformNumber(array $control): array
    {
        $control['type'] = 'glsr_type_range';
        return $control;
    }

    protected function transformSelect(array $control): array
    {
        $control['type'] = 'dropdown';
        if (!isset($control['options']) && !isset($control['value'])) {
            $control['type'] = 'autocomplete';
            $control['settings'] = [
                'multiple' => true,
                'sortable' => true,
            ];
            return $control;
        }
        if ($options = Arr::consolidate($control['value'] ?? [])) {
            unset($control['options']);
            return $control;
        }
        if ($options = Arr::consolidate($control['options'] ?? [])) {
            if (!empty($control['placeholder'])) {
                $options = ['' => $control['placeholder']] + $options;
            }
            $control['value'] = array_flip($options);
            unset($control['options']);
            return $control;
        }
        return []; // invalid control
    }

    protected function transformText(array $control): array
    {
        $control['type'] = 'textfield';
        return $control;
    }
}
