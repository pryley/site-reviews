<?php

namespace GeminiLabs\SiteReviews\Blocks;

use GeminiLabs\SiteReviews\Contracts\PluginContract;
use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

abstract class Block
{
    public function app(): PluginContract
    {
        return glsr();
    }

    public function attributes(): array
    {
        return [];
    }

    /**
     * Triggered on render in block editor.
     */
    public function normalize(array $attributes): array
    {
        $hide = array_flip(Cast::toArray($attributes['hide']));
        unset($hide['if_empty']);
        $attributes['hide'] = implode(',', array_keys($hide));
        $attributes = $this->normalizeAssignment($attributes, 'assign_to');
        $attributes = $this->normalizeAssignment($attributes, 'assigned_to');
        return $attributes;
    }

    public function normalizeAssignment(array $attributes, string $assignType): array
    {
        if ('post_id' === Arr::get($attributes, $assignType)) {
            $attributes[$assignType] = $attributes['post_id'];
        } elseif ('parent_id' === Arr::get($attributes, $assignType)) {
            $attributes[$assignType] = wp_get_post_parent_id($attributes['post_id']);
        } elseif ('custom' === Arr::get($attributes, $assignType)) {
            $attributes[$assignType] = Arr::get($attributes, $assignType.'_custom');
        }
        return $attributes;
    }

    public function register(): void
    {
        if (!function_exists('register_block_type')) {
            return;
        }
        $block = (new \ReflectionClass($this))->getShortName();
        $block = Str::snakeCase($block);
        $block = str_replace(['_block', 'site_reviews_', 'site_'], '', $block);
        register_block_type(glsr()->id."/{$block}", [
            'attributes' => $this->app()->filterArray("block/{$block}/attributes", $this->attributes(), $block),
            'editor_script' => "{$this->app()->id}/blocks",
            'editor_style' => "{$this->app()->id}/blocks",
            'render_callback' => [$this, 'render'],
            'style' => $this->app()->id,
        ]);
    }

    abstract public function render(array $attributes): string;

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

    protected function hasVisibleFields(ShortcodeContract $shortcode, array $attributes): bool
    {
        $shortcode->normalize($attributes);
        $defaults = $shortcode->getHideOptions();
        $hide = array_flip($shortcode->args['hide']);
        unset($defaults['if_empty'], $hide['if_empty']);
        return !empty(array_diff_key($defaults, $hide));
    }
}
