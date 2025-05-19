<?php

namespace GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks;

use GeminiLabs\SiteReviews\Contracts\PluginContract;
use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Integrations\IntegrationShortcode;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

abstract class Block
{
    use IntegrationShortcode;

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
        $rendered = $this->shortcodeInstance()->build($attributes, 'block', false); // do not wrap html
        if ('edit' === filter_input(INPUT_GET, 'context')) {
            return $rendered;
        }
        return $this->shortcodeInstance()->wrap(
            $rendered,
            $this->blockWrapperAttributes($attributes)
        );
    }

    protected function blockClassAttr(array $attributes): string
    {
        return '';
    }

    protected function blockStyleAttr(array $attributes): string
    {
        return '';
    }

    protected function blockWrapperAttributes(array $attributes): array
    {
        $atts = wp_parse_args(
            \WP_Block_Supports::get_instance()->apply_block_supports(),
            array_fill_keys(['class', 'id', 'style'], '')
        );

        $exclude = explode(' ', $attributes['className']);
        $include = explode(' ', $atts['class']);
        $classes = implode(' ', array_diff($include, $exclude));
        $class = "{$classes} {$this->blockClassAttr($attributes)}";
        $style = "{$atts['style']} {$this->blockStyleAttr($attributes)}";
        return array_filter([
            'class' => glsr(Sanitizer::class)->sanitizeAttrClass($class),
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
        return $this->shortcodeInstance()->hasVisibleFields($attributes);
    }
}
