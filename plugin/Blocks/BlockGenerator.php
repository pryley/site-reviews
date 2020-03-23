<?php

namespace GeminiLabs\SiteReviews\Blocks;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Helpers\Arr;

abstract class BlockGenerator
{
    /**
     * @return array
     */
    public function attributes()
    {
        return [];
    }

    /**
     * @return array
     */
    public function normalize(array $attributes)
    {
        $hide = array_flip(explode(',', $attributes['hide']));
        unset($hide['if_empty']);
        $attributes['hide'] = implode(',', array_keys($hide));
        if ('post_id' === Arr::get($attributes, 'assigned_to')) {
            $attributes['assigned_to'] = $attributes['post_id'];
        } elseif ('parent_id' === Arr::get($attributes, 'assigned_to')) {
            $attributes['assigned_to'] = wp_get_post_parent_id($attributes['post_id']);
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
        register_block_type(Application::ID.'/'.$block, [
            'attributes' => apply_filters('site-reviews/block/'.$block.'/attributes', $this->attributes()),
            'editor_script' => Application::ID.'/blocks',
            'editor_style' => Application::ID.'/blocks',
            'render_callback' => [$this, 'render'],
            'style' => Application::ID,
        ]);
    }

    /**
     * @return void
     */
    abstract public function render(array $attributes);

    /**
     * @return void
     */
    protected function filterBlockClass()
    {
        add_filter('site-reviews/style', function () {
            return 'default';
        });
    }

    /**
     * @param mixed $shortcode
     * @return bool
     */
    protected function hasVisibleFields($shortcode, array $attributes)
    {
        $args = $shortcode->normalizeAtts($attributes);
        $defaults = $shortcode->getHideOptions();
        $hide = array_flip($args['hide']);
        unset($defaults['if_empty'], $hide['if_empty']);
        return !empty(array_diff_key($defaults, $hide));
    }
}
