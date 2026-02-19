<?php

namespace GeminiLabs\SiteReviews\Integrations\Avada\Elements;

use GeminiLabs\SiteReviews\Integrations\Avada\Transformer;
use GeminiLabs\SiteReviews\Integrations\IntegrationShortcode;

abstract class FusionElement extends \Fusion_Element
{
    use IntegrationShortcode;

    abstract public function elementIcon(): string;

    public function elementParameters(array $additional = []): array
    {
        $controls = array_merge(
            $this->styleConfig(),
            $this->contentConfig(),
            $additional
        );
        $controls = glsr()->filterArray('fusion-builder/controls', $controls, $this->shortcodeInstance());
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
