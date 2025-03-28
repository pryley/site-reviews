<?php

namespace GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks;

use GeminiLabs\SiteReviews\Contracts\PluginContract;
use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

abstract class Block
{
    public function app(): PluginContract
    {
        return glsr();
    }

    public function register(): void
    {
        if (!function_exists('register_block_type_from_metadata')) {
            return;
        }
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
        preg_match_all('/(\w+)="([^"]*)"/', get_block_wrapper_attributes(), $matches, PREG_SET_ORDER);
        $atts = array_column($matches, 2, 1);
        $attributes['class'] = $atts['class'] ?? '';
        return $this->shortcode()->build($attributes, 'block');
    }

    abstract public function shortcode(): ShortcodeContract;

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
}
