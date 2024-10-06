<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

use GeminiLabs\SiteReviews\Contracts\FieldContract;
use GeminiLabs\SiteReviews\Contracts\FieldElementContract;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

abstract class AbstractFieldElement implements FieldElementContract
{
    protected FieldContract $field;

    public function __construct(FieldContract $field)
    {
        $this->field = $field;
    }

    public function build(array $overrideArgs = []): string
    {
        $args = wp_parse_args($overrideArgs, $this->field->toArray());
        $method = Helper::buildMethodName('build', $this->field->location(), 'field');
        if (method_exists($this, $method)) {
            return call_user_func([$this, $method], glsr()->args($args));
        }
        return $this->field->builder()->build($this->tag(), $args);
    }

    public function defaults(): array
    {
        return [];
    }

    public function merge(): void
    {
        $args = $this->field->toArray();
        $merged = array_merge(wp_parse_args($args, $this->defaults()), $this->required());
        $merged['class'] = implode(' ', $this->mergeAttribute('class', ' '));
        $merged['style'] = implode(';', $this->mergeAttribute('style', ';'));
        $this->field->exchangeArgs($merged);
        $this->field->exchangeTag($this->tag());
        $this->normalize();
    }

    public function required(): array
    {
        return [];
    }

    public function tag(): string
    {
        return $this->field->type;
    }

    protected function mergeAttribute(string $key, string $separator): array
    {
        return Arr::unique(array_merge(
            explode($separator, Arr::get($this->field->toArray(), $key)),
            explode($separator, Arr::get($this->defaults(), $key)),
            explode($separator, Arr::get($this->required(), $key))
        ));
    }

    protected function normalize(): void
    {
        $this->normalizeId();
        $this->normalizeName();
        $this->normalizeOptions();
        $this->normalizeStyle();
        $this->normalizeValue();
    }

    protected function normalizeId(): void
    {
        if (!empty($this->field->id)) {
            return;
        }
        if ($this->field->is_raw) {
            return;
        }
        $this->field->id = Str::convertNameToId(
            $this->field->original_name,
            $this->field->namePrefix()
        );
    }

    protected function normalizeName(): void
    {
        $name = $this->field->original_name;
        $prefix = $this->field->namePrefix();
        if (!empty($prefix) && !str_starts_with($name, $prefix)) {
            $path = Str::convertNameToPath($name);
            $name = Str::convertPathToName($path, $prefix);
        }
        if ($this->field->isMultiField()) {
            $name = Str::suffix($name, '[]');
        }
        $this->field->name = $name;
    }

    protected function normalizeOptions(): void
    {
        // Here for child classes to override.
    }

    protected function normalizeStyle(): void
    {
        if (empty($this->field->style)) {
            return;
        }
        $style = glsr(Sanitizer::class)->sanitizeAttrStyle($this->field->style);
        $this->field->style = $style;
    }

    protected function normalizeValue(): void
    {
        if ($this->field->isMultiField()) {
            return;
        }
        $this->field->value = Cast::toString($this->field->value);
    }
}
