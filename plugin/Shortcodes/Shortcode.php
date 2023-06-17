<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
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
    /**
     * @var array
     */
    public $args;

    /**
     * @var string
     */
    public $debug;

    /**
     * @var string
     */
    public $shortcode;

    /**
     * @var string
     */
    public $type;

    public function __construct()
    {
        $this->args = [];
        $this->shortcode = $this->shortcodeTag();
    }

    /**
     * @todo remove in v7.0
     */
    public function __call($method, $args)
    {
        if ('normalizeAtts' === $method) { // @compat for < 6.6.0
            call_user_func_array([$this, 'normalize'], $args);
            return glsr()->args($this->args);
        }
        throw new \BadMethodCallException("Method [$method] does not exist.");
    }

    /**
     * @todo remove in v7.0
     */
    public function __get($parameter)
    {
        // @compat provides backwards compatibility for unsupported addons
    }

    public function attributes(array $values, string $source = 'function'): array
    {
        $attributes = $this->defaults()->dataAttributes($values);
        $attributes = wp_parse_args($attributes, [
            'class' => glsr(Style::class)->styleClasses(),
            'data-from' => $source,
            'data-shortcode' => $this->shortcode,
            'id' => Arr::get($values, 'id'),
        ]);
        unset($attributes['data-id']);
        unset($attributes['data-form_id']);
        $attributes = glsr()->filterArray('shortcode/'.$this->shortcode.'/attributes', $attributes, $this);
        $attributes = array_map('esc_attr', $attributes);
        return $attributes;
    }

    public function build(array $args = [], string $type = 'shortcode'): string
    {
        $this->normalize($args, $type);
        $template = $this->buildTemplate($this->args); // @compat remove parameter usage in v7.0
        $attributes = $this->attributes($this->args, $type);
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

    public function getDisplayOptions(): array
    {
        $options = $this->displayOptions();
        return glsr()->filterArray('shortcode/display-options', $options, $this->shortcode, $this);
    }

    public function getHideOptions(): array
    {
        $options = $this->hideOptions();
        return glsr()->filterArray('shortcode/hide-options', $options, $this->shortcode, $this);
    }

    public function normalize(array $args, string $type = ''): self
    {
        if (!empty($type)) {
            $this->type = $type;
        }
        $args = wp_parse_args($args);
        $args = glsr()->filterArray('shortcode/args', $args, $this->shortcode);
        $args = $this->defaults()->unguardedRestrict($args);
        foreach ($args as $key => &$value) {
            $method = Helper::buildMethodName($key, 'normalize');
            if (method_exists($this, $method)) {
                $value = call_user_func([$this, $method], $value, $args);
            }
        }
        $this->args = $args;
        return $this;
    }

    protected function debug(array $data = []): void
    {
        if (empty($this->args['debug']) || 'shortcode' !== $this->type) {
            return;
        }
        $data = wp_parse_args($data, [
            'args' => $this->args,
            'shortcode' => $this->shortcode,
        ]);
        ksort($data);
        ob_start();
        glsr_debug($data);
        $this->debug = ob_get_clean();
    }

    protected function defaults(): DefaultsAbstract
    {
        $classname = str_replace('Shortcodes\\', 'Defaults\\', get_class($this));
        $classname = str_replace('Shortcode', 'Defaults', $classname);
        return glsr($classname);
    }

    /**
     * @return array
     */
    protected function displayOptions()
    {
        return [];
    }

    /**
     * @return array
     */
    protected function hideOptions()
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
        $values = glsr(Multilingual::class)->getPostIds($values);
        $values = array_merge($values, $postTypes);
        return implode(',', $values);
    }

    /**
     * @param string $value
     */
    protected function normalizeAssignedTerms($value): string
    {
        return implode(',', glsr(Sanitizer::class)->sanitizeTermIds($value));
    }

    /**
     * @param string $value
     */
    protected function normalizeAssignedUsers($value): string
    {
        return implode(',', glsr(Sanitizer::class)->sanitizeUserIds($value));
    }

    /**
     * @param string|array $value
     */
    protected function normalizeHide($value): array
    {
        $hideKeys = array_keys($this->getHideOptions());
        return array_filter(Cast::toArray($value), function ($value) use ($hideKeys) {
            return in_array($value, $hideKeys);
        });
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
        $maxRating = (int) glsr()->constant('MAX_RATING', Rating::class);
        $defaults = array_pad(array_slice($defaults, 0, $maxRating), $maxRating, '');
        $labels = array_map('trim', explode(',', $value));
        foreach ($defaults as $i => $label) {
            if (!empty($labels[$i])) {
                $defaults[$i] = $labels[$i];
            }
        }
        return array_combine(range($maxRating, 1), $defaults);
    }

    protected function shortcodeTag(): string
    {
        return Str::snakeCase(
            str_replace('Shortcode', '', (new \ReflectionClass($this))->getShortName())
        );
    }
}
