<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use ReflectionClass;

abstract class Field
{
    /**
     * @var Builder
     */
    protected $builder;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @return string|void
     */
    public function build()
    {
        $this->builder->args = $this->getArgs();
        $this->builder->tag = $this->getTag();
        return $this->builder->build($this->builder->tag);
    }

    /**
     * @return array
     */
    public function getArgs()
    {
        return $this->builder->args;
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->builder->tag;
    }

    /**
     * @return array
     */
    public static function defaults()
    {
        return [];
    }

    /**
     * @return array
     */
    public static function merge(array $args)
    {
        $merged = array_merge(
            wp_parse_args($args, static::defaults()),
            static::required()
        );
        $merged['class'] = implode(' ', static::mergedAttribute('class', ' ', $args));
        $merged['style'] = implode(';', static::mergedAttribute('style', ';', $args));
        return $merged;
    }

    /**
     * @param string $delimiter
     * @param string $key
     * @return array
     */
    public static function mergedAttribute($key, $delimiter, array $args)
    {
        return Arr::unique(array_merge(
            explode($delimiter, Arr::get($args, $key)),
            explode($delimiter, Arr::get(static::defaults(), $key)),
            explode($delimiter, Arr::get(static::required(), $key))
        ));
    }

    /**
     * @return array
     */
    public static function required()
    {
        return [];
    }
}
