<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Sanitizers\SanitizeText;

class Sanitizer
{
    /**
     * @var array
     */
    public $sanitizers;

    /**
     * @var array
     */
    public $values;

    public function __construct(array $values = [], array $sanitizers = [])
    {
        $this->sanitizers = $this->buildSanitizers($sanitizers);
        $this->values = $values;
    }

    public function __call(string $method, array $args)
    {
        $name = Str::dashcase(Str::removePrefix($method, 'sanitize'));
        $value = array_shift($args);
        $className = Helper::buildClassName($method, 'Modules\Sanitizers');
        $className = glsr()->filterString("sanitizer/{$name}", $className);
        if (class_exists($className)) {
            return (new $className($value, $args, $this->values))->run();
        }
        glsr_log()->error("Sanitizer method [$method] not found.");
        return array_shift($args);
    }

    public function run(): array
    {
        $results = $this->values;
        foreach ($this->values as $key => $value) {
            if (!array_key_exists($key, $this->sanitizers)) {
                continue;
            }
            foreach ($this->sanitizers[$key] as $sanitizer) {
                $args = $sanitizer['args'];
                $className = $sanitizer['sanitizer'];
                $value = (new $className($value, $args, $this->values))->run();
            }
            $results[$key] = $value;
        }
        return $results;
    }

    protected function buildSanitizer(string $sanitizer): array
    {
        $parts = preg_split('/:/', $sanitizer, 2, PREG_SPLIT_NO_EMPTY);
        $name = trim(Arr::get($parts, 0));
        $args = trim(Arr::get($parts, 1));
        $args = 'regex' === $name ? [$args] : explode(',', $args);
        $className = Helper::buildClassName(['sanitize', $name], 'Modules\Sanitizers');
        $className = glsr()->filterString("sanitizer/{$name}", $className);
        if (!class_exists($className)) {
            $className = SanitizeText::class;
        }
        return [
            'args' => $args,
            'sanitizer' => $className,
        ];
    }

    protected function buildSanitizers(array $sanitizers): array
    {
        foreach ($sanitizers as $key => $value) {
            $methods = Arr::consolidate(preg_split('/\|/', $value, -1, PREG_SPLIT_NO_EMPTY));
            $sanitizers[$key] = [];
            if (empty($methods)) {
                $sanitizers[$key][] = $this->buildSanitizer('text');
                continue;
            }
            foreach ($methods as $sanitizer) {
                $sanitizers[$key][] = $this->buildSanitizer($sanitizer);
            }
        }
        return $sanitizers;
    }
}
