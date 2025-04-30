<?php

namespace GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks;

use GeminiLabs\SiteReviews\Contracts\PluginContract;
use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

abstract class Block
{
    public function app(): PluginContract
    {
        return glsr();
    }

    public function register(): void
    {
        $block = (new \ReflectionClass($this))->getShortName();
        $block = str_replace('_block', '', Str::snakeCase($block));
        register_block_type_from_metadata($this->app()->path("assets/blocks/{$block}"), [
            'render_callback' => [$this, 'render'],
        ]);
    }

    public function render(array $attributes): string
    {
        if ('edit' === filter_input(INPUT_GET, 'context')) {
            if (!$this->hasVisibleFields($attributes)) {
                return $this->buildEmptyBlock(
                    _x('You have hidden all of the fields for this block.', 'admin-text', 'site-reviews')
                );
            }
        }
        $rendered = $this->rendered($attributes);
        if ('edit' === filter_input(INPUT_GET, 'context')) {
            return $rendered;
        }
        return glsr(Builder::class)->div(
            $rendered,
            $this->blockWrapperAttributes($attributes)
        );
    }

    abstract public function shortcode(): ShortcodeContract;

    protected function blockStyle(array $attributes): string
    {
        return '';
    }

    protected function blockWrapperAttributes(array $attributes): array
    {
        $atts = wp_parse_args(
            \WP_Block_Supports::get_instance()->apply_block_supports(),
            array_fill_keys(['class', 'id', 'style'], '')
        );
        $style = "{$atts['style']} {$this->blockStyle($attributes)}";
        return array_filter([
            'class' => glsr(Sanitizer::class)->sanitizeAttrClass($atts['class']),
            'id' => glsr(Sanitizer::class)->sanitizeId($atts['id']),
            'style' => glsr(Sanitizer::class)->sanitizeAttrStyle($style),
        ]);
    }

    protected function buildEmptyBlock(string $text): string
    {
        return glsr(Builder::class)->div([
            'class' => 'block-editor-warning',
            'text' => glsr(Builder::class)->p([
                'class' => 'block-editor-warning__message',
                'text' => $text,
            ]),
        ]);
    }

    protected function hasVisibleFields(array $attributes): bool
    {
        return $this->shortcode()->hasVisibleFields($attributes);
    }

    protected function rendered(array $attributes): string
    {
        $html = $this->shortcode()->build($attributes, 'block');
        if (preg_match('/^<div[^>]*>(.*)<\/div>$/s', $html, $matches)) {
            return $matches[1];
        }
        return $rendered;
    }
}
