<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Database\ShortcodeOptionManager;
use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Multilingual;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Modules\Style;

abstract class Shortcode implements ShortcodeContract
{
    public array $args;
    public string $debug;
    public string $description;
    public string $from;
    public string $name;
    public string $preset;
    public string $tag;

    public function __construct()
    {
        $this->args = [];
        $this->debug = '';
        $this->description = $this->description();
        $this->from = '';
        $this->name = $this->name();
        $this->preset = '';
        $this->tag = $this->tag();
    }

    public function attributes(array $values, string $from = 'function'): array
    {
        $attributes = $this->defaults()->dataAttributes($values);
        $attributes = wp_parse_args($attributes, [
            'class' => glsr(Style::class)->styleClasses($values['class'] ?? ''),
            'data-from' => $from,
            'data-shortcode' => $this->tag,
            'id' => $values['id'] ?? '',
        ]);
        unset($attributes['data-class']);
        unset($attributes['data-id']);
        unset($attributes['data-form_id']);
        $attributes = glsr()->filterArray("shortcode/attributes/{$this->tag}", $attributes, $this);
        $attributes = glsr()->filterArray('shortcode/attributes', $attributes, $this);
        $attributes = array_map('esc_attr', $attributes);
        return $attributes;
    }

    public function build(array $args = [], string $from = 'shortcode', bool $isWrapped = true): string
    {
        $this->normalize($args, $from);
        $template = $this->buildTemplate();
        if (empty($template)) {
            return '';
        }
        $attributes = $this->attributes($this->args, $from);
        $html = glsr(Builder::class)->div($template, $attributes);
        $rendered = sprintf('%s%s', $this->debug, $html);
        if ($isWrapped) {
            return $this->wrap($rendered);
        }
        return $rendered;
    }

    public function defaults(): DefaultsAbstract
    {
        $classname = str_replace('Shortcodes\\', 'Defaults\\', get_class($this));
        $classname = str_replace('Shortcode', 'Defaults', $classname);
        return glsr($classname);
    }

    public function hasVisibleFields(array $args = []): bool
    {
        if (!empty($args)) {
            $this->normalize($args);
        }
        $defaults = $this->options('hide');
        $hide = $this->args['hide'] ?? [];
        $hide = array_flip(Arr::consolidate($hide));
        unset($defaults['if_empty'], $hide['if_empty']);
        return !empty(array_diff_key($defaults, $hide));
    }

    public function normalize(array $args, string $from = ''): ShortcodeContract
    {
        if (!empty($from)) {
            $this->from = $from;
        }
        $this->args = [];
        $args = glsr()->filterArray("shortcode/args/{$this->tag}", $args, $this);
        $args = glsr()->filterArray('shortcode/args', $args, $this);
        $args = $this->defaults()->unguardedRestrict($args);
        $this->preset = $this->stylePreset($args['class']);
        foreach ($args as $key => $value) {
            $method = Helper::buildMethodName('normalize', $key);
            if (method_exists($this, $method)) {
                $value = call_user_func([$this, $method], $value);
            }
            $this->args[$key] = $value;
        }
        return $this;
    }

    /**
     * Returns the options for a shortcode setting. Results are filtered
     * by the "site-reviews/shortcode/options/{$options}" hook.
     */
    public function options(string $option, array $args = []): array
    {
        $args['option'] = $option;
        $args['shortcode'] = $this->tag;
        return call_user_func([glsr(ShortcodeOptionManager::class), $option], $args);
    }

    public function register(): void
    {
        if (!function_exists('add_shortcode')) {
            return;
        }
        $shortcode = (new \ReflectionClass($this))->getShortName();
        $shortcode = Str::snakeCase($shortcode);
        $shortcode = str_replace('_shortcode', '', $shortcode);
        add_shortcode($shortcode, fn ($atts) => $this->build($atts));
        glsr()->alias($shortcode, fn () => glsr(get_class($this)));
        glsr()->append('shortcodes', get_class($this), $shortcode);
    }

    /**
     * Returns the filtered shortcode settings configuration.
     */
    public function settings(): array
    {
        $config = $this->config();
        $config = glsr()->filterArray("shortcode/config/{$this->tag}", $config, $this);
        $config = glsr()->filterArray('shortcode/config', $config, $this);
        return $config;
    }

    public function tag(): string
    {
        return Str::snakeCase(
            str_replace('Shortcode', '', (new \ReflectionClass($this))->getShortName())
        );
    }

    public function wrap(string $renderedHtml, array $attributes = []): string
    {
        $classes = [
            Str::dashCase("{$this->from}-{$this->tag}"),
            Arr::getAs('string', $attributes, 'class'),
            $this->preset,
        ];
        $classAttr = implode(' ', $classes);
        $attributes['class'] = glsr(Sanitizer::class)->sanitizeAttrClass($classAttr);
        return glsr(Builder::class)->div($renderedHtml, $attributes);
    }

    /**
     * Returns the unfiltered shortcode settings configuration.
     */
    abstract protected function config(): array;

    protected function debug(array $data = []): void
    {
        if (empty($this->args['debug']) || 'shortcode' !== $this->from) {
            return;
        }
        $data = wp_parse_args($data, [
            'args' => $this->args,
            'shortcode' => $this->tag,
        ]);
        ksort($data);
        ob_start();
        glsr_debug($data);
        $this->debug = ob_get_clean();
    }

    protected function hideOptions(): array
    {
        return [];
    }

    /**
     * @param string $value
     */
    protected function normalizeAssignedPosts($value): string
    {
        $values = Cast::toArray($value);
        $postTypes = [];
        foreach ($values as $postType) {
            if (!is_numeric($postType) && post_type_exists((string) $postType)) {
                $postTypes[] = $postType;
            }
        }
        $values = glsr(Sanitizer::class)->sanitizePostIds($values);
        $values = glsr(Multilingual::class)->getPostIdsForAllLanguages($values);
        $values = array_merge($values, $postTypes);
        return implode(',', $values);
    }

    /**
     * @param string $value
     */
    protected function normalizeAssignedTerms($value): string
    {
        $values = glsr(Sanitizer::class)->sanitizeTermIds($value);
        $values = glsr(Multilingual::class)->getTermIdsForAllLanguages($values);
        return implode(',', $values);
    }

    /**
     * @param string $value
     */
    protected function normalizeAssignedUsers($value): string
    {
        $values = glsr(Sanitizer::class)->sanitizeUserIds($value);
        return implode(',', $values);
    }

    protected function normalizeClass(string $value): string
    {
        $values = $this->parseClassAttr($value)['custom'];
        return implode(' ', $values);
    }

    /**
     * @param string|array $value
     */
    protected function normalizeHide($value): array
    {
        $hideKeys = array_keys($this->options('hide'));
        return array_filter(Cast::toArray($value),
            fn ($value) => in_array($value, $hideKeys)
        );
    }

    protected function parseClassAttr(string $classAttr): array
    {
        $prefixes = [
            'has-custom-',
            'has-text-align-',
            'is-custom-',
            'is-style-',
            'items-justified-',
        ];
        $values = array_filter(explode(' ', trim($classAttr)),
            fn ($val) => !empty($val)
        );
        $custom = [];
        $styles = [];
        foreach ($values as $value) {
            foreach ($prefixes as $prefix) {
                if (str_starts_with($value, $prefix)) {
                    $styles[] = $value;
                    continue 2; // Skip to next value
                }
            }
            $custom[] = $value;
        }
        return compact('custom', 'styles');
    }

    protected function stylePreset(string $classAttr): string
    {
        $values = $this->parseClassAttr($classAttr)['styles'];
        $styles = array_filter($values, fn ($value) => str_starts_with($value, 'is-style-'));
        $others = array_filter($values, fn ($value) => !str_starts_with($value, 'is-style-'));
        $merged = array_merge(
            [array_shift($styles) ?: 'is-style-default'],
            $others
        );
        return implode(' ', $merged);
    }
}
