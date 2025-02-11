<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Contracts\DefaultsContract;
use GeminiLabs\SiteReviews\Contracts\PluginContract;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

/**
 * @method array dataAttributes(array $values = [])
 * @method array defaults():
 * @method array filter(array $values = [])
 * @method array merge(array $values = [])
 * @method array restrict(array $values = [])
 * @method array unguardedDataAttributes(array $values = [])
 * @method array unguardedDefaults():
 * @method array unguardedFilter(array $values = [])
 * @method array unguardedMerge(array $values = [])
 * @method array unguardedRestrict(array $values = [])
 */
abstract class DefaultsAbstract implements DefaultsContract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [];

    /**
     * The values that should be concatenated.
     *
     * @var string[]
     */
    public array $concatenated = [];

    /**
     * The values that should be constrained after sanitization is run.
     * This is done after $casts and $sanitize.
     */
    public array $enums = [];

    /**
     * The values that should be guarded.
     *
     * @var string[]
     */
    public array $guarded = [];

    /**
     * The keys that should be mapped to other keys.
     * Keys are mapped before the values are normalized and sanitized.
     * Note: Mapped keys should not be included in the defaults!
     */
    public array $mapped = [];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [];

    /**
     * The methods that are callable.
     */
    protected array $callable = [
        'dataAttributes', 'defaults', 'filter', 'merge', 'restrict',
    ];

    /**
     * The method being called.
     */
    protected string $called = '';

    /**
     * The default data.
     */
    protected array $defaults = [];

    /**
     * The string used for concatenation.
     */
    protected string $glue = ' ';

    /**
     * The current filter hook name.
     */
    protected string $hook = '';

    /**
     * The unprefixed method being called.
     */
    protected string $method = '';

    public function __construct()
    {
        $this->hook = $this->currentHook();
        $this->defaults = $this->app()->filterArray("defaults/{$this->hook}/defaults", $this->defaults(), $this->hook);
    }

    public function __call(string $name, array $args = []): array
    {
        $this->called = $name;
        $this->method = Helper::buildMethodName(Str::removePrefix($name, 'unguarded'));
        $values = Arr::consolidate(array_shift($args));
        $values = $this->mapKeys($values);
        $values = $this->normalize($values);
        array_unshift($args, $values);
        if (in_array($this->method, $this->callable)) { // this also means that the method exists
            return $this->callMethod($args);
        }
        glsr_log()->error("Invalid method [$this->method].");
        return $args;
    }

    public function property($key): array
    {
        try {
            $reflection = new \ReflectionClass($this);
            $property = $reflection->getProperty($key);
            $value = $property->getValue($this);
            if ($property->isPublic()) { // all public properties are expected to be an array
                return $this->app()->filterArray("defaults/{$this->hook}/{$key}", $value, $this->method, $this->hook);
            }
        } catch (\ReflectionException $e) {
            glsr_log()->error("Invalid or protected property [$key].");
        }
        return [];
    }

    protected function app(): PluginContract
    {
        return glsr();
    }

    protected function callMethod(array $args): array
    {
        $this->app()->action('defaults', $this, $this->hook, $this->method, $args[0]);
        $values = 'defaults' === $this->method
            ? $this->defaults // use the filtered defaults (these have not been normalized!)
            : call_user_func_array([$this, $this->method], $args);
        if ('dataAttributes' !== $this->method) {
            $values = $this->sanitize($values);
            $values = $this->guard($values);
            $values = $this->finalize($values);
        }
        $args = array_shift($args);
        return $this->app()->filterArray("defaults/{$this->hook}", $values, $this->method, $args, $this->hook);
    }

    protected function currentHook(): string
    {
        $hookName = (new \ReflectionClass($this))->getShortName();
        $hookName = Str::replaceLast('Defaults', '', $hookName);
        return Str::dashCase($hookName);
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function concatenate(string $key, $value)
    {
        if (!in_array($key, $this->property('concatenated'))) {
            return $value;
        }
        if (!is_string($value)) {
            return $value;
        }
        $default = glsr()->args($this->defaults)->$key;
        return trim($default.$this->glue.$value);
    }

    /**
     * Restrict provided values to defaults, remove empty and unchanged values,
     * and return data attribute keys with JSON encoded values.
     */
    protected function dataAttributes(array $values = []): array
    {
        $values = shortcode_atts($this->defaults, $values);
        $values = $this->sanitize($values);
        $values = $this->guard($values); // this after sanitize for a more unique id
        $values = $this->finalize($values);
        $filtered = array_filter(array_diff_assoc(
            $this->flattenArrayValues($values),
            $this->flattenArrayValues($this->defaults)
        ));
        $filteredJson = [];
        foreach ($filtered as $key => $value) {
            $filteredJson["data-{$key}"] = !is_scalar($value)
                ? wp_json_encode((array) $value, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                : $value;
        }
        return $filteredJson;
    }

    /**
     * The default values.
     */
    protected function defaults(): array
    {
        return [];
    }

    /**
     * Remove empty values from the provided values and merge with the defaults.
     */
    protected function filter(array $values = []): array
    {
        return $this->merge(array_filter($values, Helper::class.'::isNotEmpty'));
    }

    /**
     * Finalize provided values, this always runs last.
     */
    protected function finalize(array $values = []): array
    {
        return $values;
    }

    protected function flattenArrayValues(array $values): array
    {
        array_walk($values, function (&$value) {
            if (is_array($value)) {
                $value = implode(',', array_filter($value, 'is_scalar'));
            }
        });
        return $values;
    }

    /**
     * Remove guarded keys from the provided values.
     */
    protected function guard(array $values): array
    {
        if (!str_starts_with($this->called, 'unguarded')) {
            return array_diff_key($values, array_flip($this->property('guarded')));
        }
        return $values;
    }

    /**
     * Map old or deprecated keys to new keys.
     */
    protected function mapKeys(array $values): array
    {
        foreach ($this->property('mapped') as $old => $new) {
            if (empty($values[$new]) && !empty($values[$old])) { // new always takes precedence
                $values[$new] = $values[$old];
            }
            unset($values[$old]);
        }
        return $values;
    }

    /**
     * Merge provided values with the defaults.
     */
    protected function merge(array $values = []): array
    {
        return $this->parse($values, $this->defaults);
    }

    /**
     * Normalize provided values, this always runs first.
     */
    protected function normalize(array $values = []): array
    {
        return $values;
    }

    /**
     * @param mixed $values
     * @param mixed $defaults
     */
    protected function parse($values, $defaults): array
    {
        $values = Cast::toArray($values);
        if (!is_array($defaults)) {
            return $values;
        }
        $parsed = $defaults;
        foreach ($values as $key => $value) {
            if (!is_scalar($value) && isset($parsed[$key])) {
                $parsed[$key] = Arr::unique($this->parse($value, $parsed[$key])); // does not reindex
                continue;
            }
            $parsed[$key] = $this->concatenate((string) $key, $value);
        }
        return $parsed;
    }

    /**
     * @param mixed $values
     */
    protected function parseRestricted($values): array
    {
        $values = Cast::toArray($values);
        $parsed = [];
        foreach ($this->defaults as $key => $default) {
            if (!array_key_exists($key, $values)) {
                $parsed[$key] = $default;
                continue;
            }
            if (is_array($default)) { // if the default value is supposed to be an array
                $parsed[$key] = $this->parse($values[$key], $default);
                continue;
            }
            $parsed[$key] = $this->concatenate((string) $key, $values[$key]);
        }
        return $parsed;
    }

    /**
     * Merge the provided values with the defaults and remove any non-default keys.
     */
    protected function restrict(array $values = []): array
    {
        return $this->parseRestricted($values);
    }

    protected function sanitize(array $values = []): array
    {
        foreach ($this->property('casts') as $key => $cast) {
            if (array_key_exists($key, $values)) {
                $values[$key] = Cast::to($cast, $values[$key]);
            }
        }
        $values = (new Sanitizer($values, $this->property('sanitize')))->run();
        foreach ($this->property('enums') as $key => $enums) {
            if (array_key_exists($key, $values) && !in_array($values[$key], $enums, true)) {
                $values[$key] = $this->defaults[$key] ?? '';
            }
        }
        return $values;
    }

    protected function unmapKeys(array $args): array
    {
        foreach ($this->property('mapped') as $old => $new) {
            if (array_key_exists($new, $args)) {
                $args[$old] = $args[$new];
                unset($args[$new]);
            }
        }
        return $args;
    }
}
