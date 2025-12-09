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
            return $this->shortcodeInstance()->build($attributes, 'block', isWrapped: false);
        }
        return glsr(Builder::class)->div(
            $this->shortcodeInstance()->build($attributes, 'block', isWrapped: false),
            $this->wrapperAttributes($attributes)
        );
    }

    /**
     * @return string[]
     */
    protected function blockClasses(array $args): array
    {
        return [];
    }

    protected function blockStyles(array $args): array
    {
        return [];
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

    protected function buildInlineStyles(array $styles): string
    {
        return array_reduce(
            array_keys($styles),
            fn (string $carry, string $key) => $carry."$key:{$styles[$key]};",
            ''
        );
    }

    protected function hasVisibleFields(array $attributes): bool
    {
        return $this->shortcodeInstance()->hasVisibleFields($attributes);
    }

    protected function wrapperAttributes(array $args): array
    {
        $blockSupports = \WP_Block_Supports::get_instance()->apply_block_supports();
        $blockClass = $blockSupports['class'] ?? '';
        $blockStyle = $blockSupports['style'] ?? '';
        $blockId = $blockSupports['id'] ?? '';
        $customClasses = glsr()->filterArray('block/classes', $this->blockClasses($args), $args, $this);
        $customStyles = glsr()->filterArray('block/styles', $this->blockStyles($args), $args, $this);
        $rootClass = $this->shortcodeInstance()->classAttr($args['className'] ?? '', isWrapper: false);
        $wrapperOnlyClasses = array_diff(explode(' ', $blockClass), explode(' ', $rootClass));
        $mergedClasses = array_unique(array_merge($customClasses, $wrapperOnlyClasses));
        $inlineStyles = $this->buildInlineStyles($customStyles).$blockStyle;
        $attributes = [
            'class' => trim(implode(' ', $mergedClasses)),
            'id' => $blockId,
            'style' => $inlineStyles,
        ];
        $attributes = glsr()->filterArray('block/wrap/attributes', $attributes, $args, $this);
        return array_filter([
            'class' => glsr(Sanitizer::class)->sanitizeAttrClass($attributes['class'] ?? ''),
            'id' => glsr(Sanitizer::class)->sanitizeId($attributes['id'] ?? ''),
            'style' => glsr(Sanitizer::class)->sanitizeAttrStyle($attributes['style'] ?? ''),
        ]);
    }
}
