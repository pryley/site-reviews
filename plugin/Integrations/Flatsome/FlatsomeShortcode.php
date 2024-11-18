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
        add_action('ux_builder_setup', function () {
            add_ux_builder_shortcode($this->shortcode()->shortcode, [
                'category' => glsr()->name,
                // 'inline' => true,
                'name' => $this->name(),
                'options' => $this->options(),
                'thumbnail' => $this->icon(),
                'wrap' => false,
            ]);
        });
    }

    protected function hideOptions(): array
    {
        return [
            'hide' => [
                'type' => 'select',
                'heading' => esc_html_x('Hide Fields', 'admin-text', 'site-reviews'),
                'description' => esc_html_x('Flatsome UX Builder does not support multiple checkboxes here so instead please use the dropdown to select fields that you want to hide.', 'admin-text', 'site-reviews'),
                'full_width' => true,
                'config' => [
                    'multiple' => true,
                    'options' => $this->shortcode()->getHideOptions(),
                    'placeholder' => esc_html_x('Select...', 'admin-text', 'site-reviews'),
                    'sortable' => false,
                ],
            ],
        ];
    }

    abstract protected function icon(): string;

    protected function name(): string
    {
        return $this->shortcode()->name;
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
}
