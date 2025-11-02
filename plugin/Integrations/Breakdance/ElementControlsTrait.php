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
        $controls = [];
        $settings = [];
        $shortcode = static::bdShortcode();
        $transformer = new Transformer($shortcode->settings(), $shortcode->tag);
        foreach ($transformer as $item) {
            $controls[$item['path']] = $item['control'];
        }
        $controls = Arr::unflatten(array_filter($controls));
        foreach ($controls as $slug => $children) {
            $section = $transformer->section($slug);
            foreach ($children as $key => $child) {
                if (!str_starts_with($key, 'popout_')) {
                    $section['children'][] = $child;
                    continue;
                }
                $slug = Str::removePrefix($key, 'popout_');
                $section['children'][] = $transformer->popout($slug, array_values($child));
            }
            $settings[] = $section;
        }
        return $settings;
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
        $transformer = new Transformer($config, $shortcode->tag);
        foreach ($transformer as $item) {
            $slug = $item['control']['slug'] ?? '';
            if (empty($slug) || !array_key_exists($slug, $defaults)) {
                continue;
            }
            $path = "content.{$item['path']}";
            $properties[$path] = $defaults[$slug]['default'];
        }
        if (empty($properties)) {
            return false;
        }
        return Arr::unflatten($defaults);
    }

    /**
     * Return an array of saved keyed values.
     */
    public static function ssrArgs(array $data): array
    {
        $args = [];
        $arrayIterator = new \RecursiveArrayIterator($data);
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
        $args = glsr()->filterArray('breakdance/ssr', $args, $data, static::bdShortcode()->tag);
        return $args;
    }
}
