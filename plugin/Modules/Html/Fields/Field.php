<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Contracts\BuilderContract;
use GeminiLabs\SiteReviews\Helpers\Arr;

abstract class Field
{
    protected BuilderContract $builder;

    public function __construct(BuilderContract $builder)
    {
        $this->builder = $builder;
    }

    public function args(): Arguments
    {
        return $this->builder->args;
    }

    /**
     * This method is used when building a custom Field type.
     */
    public function build(): string
    {
        return $this->builder->build($this->tag(), $this->args()->toArray());
    }

    public static function defaults(string $fieldLocation = ''): array
    {
        return [];
    }

    public static function merge(array $args, string $fieldLocation = ''): array
    {
        $merged = array_merge(
            wp_parse_args($args, static::defaults($fieldLocation)),
            static::required($fieldLocation)
        );
        $merged['class'] = implode(' ', static::mergedAttribute('class', ' ', $args, $fieldLocation));
        $merged['style'] = implode(';', static::mergedAttribute('style', ';', $args, $fieldLocation));
        return $merged;
    }

    public static function mergedAttribute(string $key, string $separator, array $args, string $fieldLocation): array
    {
        return Arr::unique(array_merge(
            explode($separator, Arr::get($args, $key)),
            explode($separator, Arr::get(static::defaults($fieldLocation), $key)),
            explode($separator, Arr::get(static::required($fieldLocation), $key))
        ));
    }

    public static function required(string $fieldLocation = ''): array
    {
        return [];
    }

    public function tag(): string
    {
        return $this->builder->tag;
    }
}
