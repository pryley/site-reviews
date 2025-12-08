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
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Modules\Style;

abstract class Shortcode implements ShortcodeContract
{
    public array $args;
    public string $debug;
    public string $description;
    public string $from;
    public string $name;
    public string $tag;

    public function __construct()
    {
        $this->args = [];
        $this->debug = '';
        $this->description = $this->description();
        $this->from = '';
        $this->name = $this->name();
        $this->tag = $this->tag();
    }

    /**
     * The attributes added to the unwrapped rendered root HTML element.
     */
    public function attributes(array $values, string $from = 'function'): array
    {
        $attributes = $this->defaults()->dataAttributes($values);
        $attributes = wp_parse_args($attributes, [
            'class' => $this->classAttr($values['class'] ?? '', isWrapper: false),
            'data-from' => ($values['from'] ?? '') ?: $from,
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
        $attributes = $this->attributes($this->args, $this->from);
        $html = glsr(Builder::class)->div($template, $attributes);
        $rendered = $this->debug.$html;
        if ($isWrapped) {
            return $this->wrap($rendered, $args); // pass the original $args here
        }
        return $rendered;
    }

    public function buildCallback(array $args = []): string
    {
        $this->enqueue();
        return $this->build($args);
    }

    public function defaults(): DefaultsAbstract
    {
        $classname = str_replace('Shortcodes\\', 'Defaults\\', get_class($this));
        $classname = str_replace('Shortcode', 'Defaults', $classname);
        return glsr($classname);
    }

    public function enqueue(): void
    {
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
        $this->args = [];
        $this->from = ($args['from'] ?? $from) ?: $this->from;
        $args = glsr()->filterArray("shortcode/args/{$this->tag}", $args, $this);
        $args = glsr()->filterArray('shortcode/args', $args, $this);
        $args = $this->defaults()->unguardedRestrict($args);
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
        return glsr(ShortcodeOptionManager::class)->get($option, $args);
    }

    public function register(): void
    {
        $shortcode = (new \ReflectionClass($this))->getShortName();
        $shortcode = Str::snakeCase($shortcode);
        $shortcode = str_replace('_shortcode', '', $shortcode);
        add_shortcode($shortcode, [$this, 'buildCallback']);
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
        $shortName = (new \ReflectionClass($this))->getShortName();
        return Str::snakeCase(str_replace('Shortcode', '', $shortName));
    }

    /**
     * @param array $args The unmodified $args as passed to the build method
     */
    public function wrap(string $html, array $args = []): string
    {
        $attributes = [
            'class' => $this->classAttr($args['class'] ?? '', isWrapper: true),
        ];
        $attributes = glsr()->filterArray('shortcode/wrap/attributes', $attributes, $args, $this);
        $attributes = array_map('esc_attr', $attributes);
        return glsr(Builder::class)->div($html, $attributes);
    }

    protected function classAttr(string $attr, bool $isWrapper = false): string
    {
        $prefixes = [
            'has-custom-',
            'has-text-align-',
            'is-custom-',
            'is-style-',
            'items-justified-',
        ];
        $classes = array_filter(explode(' ', trim($attr)));
        $rootClasses = [
            glsr(Style::class)->styleClasses(),
        ];
        $wrapClasses = [
            Str::dashCase("{$this->from}-{$this->tag}"),
        ];
        foreach ($classes as $class) {
            $isPrefixed = !empty(array_filter($prefixes, fn ($p) => str_starts_with($class, $p)));
            if ($isPrefixed) {
                $wrapClasses[] = $class;
                continue;
            }
            $rootClasses[] = $class;
        }
        return glsr(Sanitizer::class)->sanitizeAttrClass(
            implode(' ', $isWrapper ? $wrapClasses : $rootClasses)
        );
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
        return $this->classAttr($value, isWrapper: false);
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
}
