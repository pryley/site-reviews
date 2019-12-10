<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

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
        glsr_log()->error('Build method is not implemented for '.get_class($this));
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
        return array_filter(array_merge(
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

    /**
     * @return void
     */
    protected function mergeFieldArgs()
    {
        $this->builder->args = static::merge($this->builder->args);
    }
}
