<?php

namespace GeminiLabs\SiteReviews\Integrations\Flatsome;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Helpers\Cast;

abstract class FlatsomeShortcode
{
    abstract public function options(): array;

    /**
     * Override the "add_ux_builder_shortcode" data with
     * the "ux_builder_shortcode_data_{$tag}" filter hook.
     */
    public function register(): void
    {
        add_shortcode($this->uxShortcode(), [$this, 'renderShortcode']);
        add_action('ux_builder_setup', function () {
            add_ux_builder_shortcode($this->uxShortcode(), [
                'category' => glsr()->name,
                'name' => $this->name(),
                'options' => $this->options(),
                'thumbnail' => $this->icon(),
                'wrap' => false,
            ]);
        });
    }

    /**
     * @param array|string $atts
     */
    public function renderShortcode($atts = []): string
    {
        $args = wp_parse_args($atts);
        $hide = [];
        foreach ($atts as $key => $value) {
            if (str_starts_with((string) $key, 'hide_') && Cast::toBool($value)) {
                $hide[] = substr($key, 5);
            }
        }
        $args['hide'] = array_filter($hide);
        if (!empty($args['visibility'])) {
            $args['class'] = ($args['class'] ?? '').' '.$args['visibility'];
        }
        return $this->shortcode()->build($args, 'flatsome');
    }

    abstract protected function icon(): string;

    abstract protected function name(): string;

    protected function hideOptions(): array
    {
        $options = [];
        foreach ($this->shortcode()->getHideOptions() as $key => $label) {
            $options["hide_{$key}"] = [
                'heading' => $label,
                'type' => 'checkbox',
            ];
        }
        return $options;
    }

    abstract protected function shortcode(): ShortcodeContract;

    protected function typeOptions(): array
    {
        $types = glsr()->retrieveAs('array', 'review_types', []);
        if (2 > count($types)) {
            return [];
        }
        return [
            'type' => 'select',
            'heading' => esc_html_x('Limit Reviews by Type', 'admin-text', 'site-reviews'),
            'full_width' => true,
            'default' => 'local',
            'options' => $types,
        ];
    }

    protected function uxShortcode(): string
    {
        return "ux_{$this->shortcode()->shortcode}";
    }
}
