<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Commands\EnqueuePublicAssets;
use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Database\ShortcodeOptionManager;
use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Helpers\Text;
use GeminiLabs\SiteReviews\Modules\Assets\Stylesheets;
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
            'data-glsr-modules' => implode(' ', $this->modules()),
            'data-glsr-styles' => glsr(Stylesheets::class)->declare($this->stylesheets()),
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
        glsr(Stylesheets::class)->suspend(); // don't print the stylesheets for the template partials
        try {
            $template = $this->buildTemplate();
        } finally {
            glsr(Stylesheets::class)->resume();
        }
        if (empty($template)) {
            return '';
        }
        $attributes = $this->attributes($this->args, $this->from);
        $this->enqueueModules($attributes['data-glsr-modules'] ?? '');
        $html = glsr(Builder::class)->div($template, $attributes);
        $stylesheets = array_filter(explode(' ', $attributes['data-glsr-styles'] ?? ''));
        if ($this->debug) {
            $stylesheets[] = 'debug';
        }
        $rendered = glsr(Stylesheets::class)->print($stylesheets).$this->debug.$html;
        if ($isWrapped) {
            $rendered = $this->wrap($rendered, $args); // pass the original $args here
        }
        return Text::escapeShortcodes($rendered);
    }

    public function buildCallback(array $args = []): string
    {
        $this->enqueue();
        glsr()->action('shortcode/enqueue', $this);
        return $this->build($args);
    }

    public function classAttr(string $classAttr, bool $isWrapper = false): string
    {
        $prefixes = [
            'has-', 'is-', 'items-',
        ];
        $rootClasses = [
            glsr(Style::class)->styleClasses(),
        ];
        $wrapClasses = [
            Str::dashCase("{$this->from}-{$this->tag}"),
        ];
        foreach (array_filter(explode(' ', $classAttr)) as $class) {
            $isPrefixed = !empty(array_filter($prefixes, fn ($p) => str_starts_with($class, $p)));
            if ($isPrefixed) {
                $wrapClasses[] = $class;
                continue;
            }
            $rootClasses[] = $class;
        }
        $classes = $isWrapper ? $wrapClasses : $rootClasses;
        return glsr(Sanitizer::class)->sanitizeAttrClass(implode(' ', $classes));
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

    /**
     * @param string $tokens the root's data-glsr-modules value
     */
    public function enqueueModules(string $tokens): void
    {
        if (!glsr()->filterBool('assets/js', true)) {
            return;
        }
        foreach (array_filter(explode(' ', $tokens)) as $token) {
            wp_enqueue_script_module(EnqueuePublicAssets::MODULE_PREFIX.$token);
        }
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
     * The unfiltered list of module names. Printed in data-glsr-modules.
     *
     * @return string[]
     */
    public function modules(): array
    {
        return [];
    }

    /**
     * The options for a shortcode setting. Results are filtered
     * by the "site-reviews/shortcode/options/{$option}" hook.
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
     * The filtered shortcode settings configuration.
     */
    public function settings(): array
    {
        $config = $this->config();
        $config = glsr()->filterArray("shortcode/config/{$this->tag}", $config, $this);
        $config = glsr()->filterArray('shortcode/config', $config, $this);
        return $config;
    }

    /**
     * The unfiltered list of stylesheet component names. Printed in data-glsr-styles.
     *
     * @return string[]
     */
    public function stylesheets(): array
    {
        return [];
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
        return glsr(Builder::class)->div($html, $this->wrapperAttributes($args));
    }

    /**
     * The unfiltered shortcode settings configuration.
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

    protected function wrapperAttributes(array $args): array
    {
        $attributes = [
            'class' => $this->classAttr($args['class'] ?? '', isWrapper: true),
            'style' => $args['style'] ?? '',
        ];
        $attributes = glsr()->filterArray('shortcode/wrap/attributes', $attributes, $args, $this);
        return array_filter([
            'class' => glsr(Sanitizer::class)->sanitizeAttrClass($attributes['class'] ?? ''),
            'style' => glsr(Sanitizer::class)->sanitizeAttrStyle($attributes['style'] ?? ''),
        ]);
    }
}
