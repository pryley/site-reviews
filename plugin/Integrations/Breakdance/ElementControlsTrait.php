<?php

namespace GeminiLabs\SiteReviews\Integrations\Breakdance;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

trait ElementControlsTrait
{
    public static function bdShortcode(): ShortcodeContract
    {
        static $shortcode;
        if (empty($shortcode)) {
            $shortcode = glsr(static::bdShortcodeClass());
        }
        return $shortcode;
    }

    abstract public static function bdShortcodeClass(): string;

    /**
     * @return array[]
     */
    public static function contentControls()
    {
        $shortcode = static::bdShortcode();
        $transformer = new Transformer('content', $shortcode->settings(), $shortcode->tag);
        $controls = $transformer->controls();
        return glsr()->filterArray('breakdance/content_controls', $controls, $shortcode);
    }

    /**
     * This must return false if the element has no default properties
     * otherwise SSR will not trigger when control values are changed.
     *
     * @return array|false
     */
    public static function defaultProperties()
    {
        $properties = [];
        $shortcode = static::bdShortcode();
        $config = $shortcode->settings();
        $defaults = array_filter($config, fn ($args) => isset($args['default']));
        $transformer = new Transformer('content', $config, $shortcode->tag);
        foreach ($transformer as $item) {
            $slug = $item['control']['slug'] ?? '';
            if (empty($slug) || !array_key_exists($slug, $defaults)) {
                continue;
            }
            $path = "content.{$item['path']}";
            $properties[$path] = $defaults[$slug]['default'];
        }
        $properties = Arr::unflatten($properties);
        $properties = glsr()->filterArray('breakdance/default_properties', $properties, $shortcode);
        if (empty($properties)) {
            return false;
        }
        return $properties;
    }

    /**
     * @return array[]
     */
    public static function designControls()
    {
        $shortcode = static::bdShortcode();
        $transformer = new Transformer('design', [], $shortcode->tag);
        $controls = $transformer->controls();
        return glsr()->filterArray('breakdance/design_controls', $controls, $shortcode);
    }

    /**
     * @return array[]
     */
    public static function settingsControls()
    {
        return [];
    }

    /**
     * Return an array of saved keyed values.
     */
    public static function ssrArgs(array $data): array
    {
        $args = [];
        $settings = $data['content'] ?? [];
        $arrayIterator = new \RecursiveArrayIterator($settings);
        $iterator = new \RecursiveIteratorIterator($arrayIterator, \RecursiveIteratorIterator::SELF_FIRST);
        $keys = array_keys(static::bdShortcode()->settings());
        $mappedKeys = [
            'attr_class' => 'class',
            'attr_id' => 'id',
        ];
        foreach ($iterator as $key => $value) {
            if (array_key_exists((string) $key, $mappedKeys)) {
                $args[$mappedKeys[$key]] = $value;
                continue;
            }
            if (in_array($key, $mappedKeys)) {
                continue;
            }
            if (in_array($key, $keys)) {
                $args[$key] = $value;
            }
        }
        $replacements = [ // the post_chooser control requires integer keys
            -10 => 'post_id',
            -20 => 'parent_id',
            -30 => 'user_id',
            -40 => 'author_id',
            -50 => 'profile_id',
        ];
        foreach ($args as $key => $value) {
            if (!is_array($value)) {
                continue;
            }
            if (wp_is_numeric_array($value)) {
                $args[$key] = array_map(fn ($id) => $replacements[$id] ?? $id, $value);
            } else {
                $args[$key] = array_keys(array_filter($value));
            }
        }
        $args = glsr()->filterArray('breakdance/ssr_args', $args, $data, static::bdShortcode());
        return $args;
    }
}
