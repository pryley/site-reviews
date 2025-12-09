<?php

namespace GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks;

use GeminiLabs\SiteReviews\Contracts\BlockContract;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Integrations\IntegrationShortcode;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

abstract class Block implements BlockContract
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
        return glsr(Builder::class)->div(
            $rendered,
            $this->blockWrapperAttributes($attributes)
        );
    }

    /**
     * @return string[]
     */
    protected function blockClasses(array $attributes): array
    {
        return [];
    }

    protected function blockStyles(array $attributes): array
    {
        return [];
    }

    protected function blockWrapperAttributes(array $attributes): array
    {
        $supports = \WP_Block_Supports::get_instance()->apply_block_supports();
        $classes = glsr()->filterArray('block/classes', $this->blockClasses($attributes), $attributes, $this);
        $styles = glsr()->filterArray('block/styles', $this->blockStyles($attributes), $attributes, $this);
        $extraClasses = array_diff(
            explode(' ', $supports['class'] ?? ''),
            preg_grep('/^(?!has-|is-|items-)/', explode(' ', $attributes['className'] ?? ''))
        );
        $finalClasses = implode(' ', array_merge($classes, $extraClasses));
        $finalStyles = array_reduce(
            array_keys($styles),
            fn (string $carry, string $key) => $carry."$key:{$styles[$key]};",
            ''
        ).($supports['style'] ?? '');
        return array_filter([
            'class' => glsr(Sanitizer::class)->sanitizeAttrClass($finalClasses),
            'id' => glsr(Sanitizer::class)->sanitizeId($supports['id'] ?? ''),
            'style' => glsr(Sanitizer::class)->sanitizeAttrStyle($finalStyles),
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
