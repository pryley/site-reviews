<?php

namespace GeminiLabs\SiteReviews\Integrations\Avada\Elements;

use GeminiLabs\SiteReviews\Integrations\Avada\Transformer;
use GeminiLabs\SiteReviews\Integrations\IntegrationShortcode;

abstract class FusionElement extends \Fusion_Element
{
    use IntegrationShortcode;

    public function add_css_files()
    {
        $parts = explode('_', $this->shortcodeInstance()->tag);
        $suffix = end($parts);
        $handle = sprintf('%s-%s-style', glsr()->ID, $suffix);
        $path = (string) wp_styles()->get_data($handle, 'path');
        if (file_exists($path)) {
            FusionBuilder()->add_element_css($path);
        }
        glsr()->action('avada/add_css_files', $this->shortcodeInstance());
    }

    abstract public function elementIcon(): string;

    public function elementParameters(array $additional = []): array
    {
        $controls = array_merge(
            $this->styleConfig(),
            $this->contentConfig(),
            $additional
        );
        $controls = glsr()->filterArray('avada/controls', $controls, $this->shortcodeInstance());
        $params = [];
        foreach ($controls as $name => $control) {
            $transformer = new Transformer($name, $control, $this->shortcodeInstance()->tag);
            if ($transformedControl = $transformer->control()) {
                $params[$name] = $transformedControl;
            }
        }
        return $params;
    }

    public static function registerElement(): void
    {
        $instance = new static();
        add_action('fusion_builder_before_init', function () use ($instance) {
            if (!function_exists('fusion_builder_map')) {
                return;
            }
            if (!function_exists('fusion_builder_frontend_data')) {
                return;
            }
            fusion_builder_map(fusion_builder_frontend_data(static::class, [
                'icon' => $instance->elementIcon(),
                'name' => $instance->shortcodeInstance()->name,
                'params' => $instance->elementParameters([
                    'from' => [
                        'type' => 'hidden',
                        'value' => 'avada',
                    ],
                ]),
                'shortcode' => $instance->shortcodeInstance()->tag,
            ]));
        });
    }

    protected function contentConfig(): array
    {
        return $this->shortcodeInstance()->settings();
    }

    protected function styleConfig(): array
    {
        return [];
    }
}
