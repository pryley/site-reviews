<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Sanitizers\SanitizeCompat;
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
        $value = array_shift($args);
        // @todo remove in v7.0.0
        $name = lcfirst(Str::removePrefix($method, 'sanitize'));
        if (in_array($name, ['array', 'bool', 'int'])) {
            return (new SanitizeCompat($value, $name, $this->values))->run();
        }
        $classname = Helper::buildClassName($method, 'Modules\Sanitizers');
        if (class_exists($classname)) {
            return (new $classname($value, $args, $this->values))->run();
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
                $classname = $sanitizer['sanitizer'];
                $value = (new $classname($value, $args, $this->values))->run();
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
        $classname = Helper::buildClassName('sanitize-'.$name, 'Modules\Sanitizers');
        if (!class_exists($classname)) {
            $classname = SanitizeText::class;
        }
        if (in_array($name, ['array', 'bool', 'int'])) { // @todo remove in v7.0.0
            $args = $name;
            $classname = SanitizeCompat::class;
        }
        return [
            'args' => $args,
            'sanitizer' => $classname,
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
