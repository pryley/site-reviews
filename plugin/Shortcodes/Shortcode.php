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

    public function attributes(array $values, string $from = 'function'): array
    {
        $attributes = $this->defaults()->dataAttributes($values);
        $attributes = wp_parse_args($attributes, [
            'class' => glsr(Style::class)->styleClasses(),
            'data-from' => $from,
            'data-shortcode' => $this->tag,
            'id' => Arr::get($values, 'id'),
        ]);
        unset($attributes['data-id']);
        unset($attributes['data-form_id']);
        $attributes = glsr()->filterArray("shortcode/{$this->tag}/attributes", $attributes, $this);
        $attributes = array_map('esc_attr', $attributes);
        return $attributes;
    }

    public function build($args = [], string $from = 'shortcode'): string
    {
        $this->normalize(wp_parse_args($args), $from);
        $template = $this->buildTemplate();
        $attributes = $this->attributes($this->args, $from);
        $html = glsr(Builder::class)->div($template, $attributes);
        return sprintf('%s%s', $this->debug, $html);
    }

    /**
     * @param string|array $args
     */
    public function buildBlock($args = []): string
    {
        return $this->build(wp_parse_args($args), 'block');
    }

    /**
     * @param string|array $args
     */
    public function buildShortcode($args = []): string
    {
        return $this->build(wp_parse_args($args), 'shortcode');
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
        $args = glsr()->filterArray('shortcode/args', $args, $this->tag);
        $args = $this->defaults()->unguardedRestrict($args);
        foreach ($args as $key => &$value) {
            $method = Helper::buildMethodName('normalize', $key);
            if (method_exists($this, $method)) {
                $value = call_user_func([$this, $method], $value, $args);
            }
        }
        $this->args = $args;
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
        glsr()->append('shortcodes', get_class($this), $shortcode);
    }

    /**
     * Returns the filtered shortcode settings configuration.
     */
    public function settings(): array
    {
        $config = $this->config();
        $config = glsr()->filterArray("shortcode/{$this->tag}/config", $config, $this);
        $config = glsr()->filterArray('shortcode/config', $config, $this->tag, $this);
        return $config;
    }

    public function tag(): string
    {
        return Str::snakeCase(
            str_replace('Shortcode', '', (new \ReflectionClass($this))->getShortName())
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

    protected function displayOptions(): array
    {
        return [];
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

    /**
     * @param string $value
     */
    protected function normalizeLabels($value): array
    {
        $defaults = [
            __('Excellent', 'site-reviews'),
            __('Very good', 'site-reviews'),
            __('Average', 'site-reviews'),
            __('Poor', 'site-reviews'),
            __('Terrible', 'site-reviews'),
        ];
        $maxRating = Rating::max();
        $defaults = array_pad(array_slice($defaults, 0, $maxRating), $maxRating, '');
        $labels = array_map('trim', explode(',', $value));
        foreach ($defaults as $i => $label) {
            if (!empty($labels[$i])) {
                $defaults[$i] = $labels[$i];
            }
        }
        return array_combine(range($maxRating, 1), $defaults);
    }
}
