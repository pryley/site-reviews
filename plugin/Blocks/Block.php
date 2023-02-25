<?php

namespace GeminiLabs\SiteReviews\Blocks;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;

abstract class Block
{
    /**
     * @return \GeminiLabs\SiteReviews\Application|\GeminiLabs\SiteReviews\Addons\Addon
     */
    public function app()
    {
        return glsr();
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [];
    }

    /**
     * Triggered on render in block editor.
     * @return array
     */
    public function normalize(array $attributes)
    {
        $hide = array_flip(Cast::toArray($attributes['hide']));
        unset($hide['if_empty']);
        $attributes['hide'] = implode(',', array_keys($hide));
        $attributes = $this->normalizeAssignment($attributes, 'assign_to');
        $attributes = $this->normalizeAssignment($attributes, 'assigned_to');
        return $attributes;
    }

    /**
     * @param string $assignType
     * @return array
     */
    public function normalizeAssignment(array $attributes, $assignType)
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

    /**
     * @return void
     */
    public function register($block)
    {
        if (!function_exists('register_block_type')) {
            return;
        }
        register_block_type(glsr()->id.'/'.$block, [
            'attributes' => $this->app()->filterArray('block/'.$block.'/attributes', $this->attributes()),
            'editor_script' => $this->app()->id.'/blocks',
            'editor_style' => $this->app()->id.'/blocks',
            'render_callback' => [$this, 'render'],
            'style' => $this->app()->id,
        ]);
    }

    /**
     * @return void
     */
    abstract public function render(array $attributes);

    /**
     * @param mixed $shortcode
     * @return bool
     */
    protected function hasVisibleFields($shortcode, array $attributes)
    {
        $shortcode->normalize($attributes);
        $defaults = $shortcode->getHideOptions();
        $hide = array_flip($shortcode->args['hide']);
        unset($defaults['if_empty'], $hide['if_empty']);
        return !empty(array_diff_key($defaults, $hide));
    }
}
