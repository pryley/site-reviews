<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Helpers\Arr;

class Attributes
{
    public const ATTRIBUTES_A = [
        'download', 'href', 'hreflang', 'ping', 'referrerpolicy', 'rel', 'target', 'type',
    ];

    public const ATTRIBUTES_BUTTON = [
        'autofocus', 'disabled', 'form', 'formaction', 'formenctype', 'formmethod',
        'formnovalidate', 'formtarget', 'name', 'type', 'value',
    ];

    public const ATTRIBUTES_FORM = [
        'accept', 'accept-charset', 'action', 'autocapitalize', 'autocomplete', 'enctype', 'method',
        'name', 'novalidate', 'target',
    ];

    public const ATTRIBUTES_IMG = [
        'alt', 'crossorigin', 'decoding', 'height', 'ismap', 'loading', 'referrerpolicy', 'sizes',
        'src', 'srcset', 'width', 'usemap',
    ];

    public const ATTRIBUTES_INPUT = [
        'accept', 'autocomplete', 'autocorrect', 'autofocus', 'capture', 'checked', 'disabled',
        'form', 'formaction', 'formenctype', 'formmethod', 'formnovalidate', 'formtarget', 'height',
        'incremental', 'inputmode', 'list', 'max', 'maxlength', 'min', 'minlength', 'multiple',
        'name', 'pattern', 'placeholder', 'readonly', 'results', 'required', 'selectionDirection',
        'selectionEnd', 'selectionStart', 'size', 'spellcheck', 'src', 'step', 'tabindex', 'type',
        'value', 'webkitdirectory', 'width',
    ];

    public const ATTRIBUTES_LABEL = [
        'for',
    ];

    public const ATTRIBUTES_OPTGROUP = [
        'disabled', 'label',
    ];

    public const ATTRIBUTES_OPTION = [
        'disabled', 'label', 'selected', 'value',
    ];

    public const ATTRIBUTES_SELECT = [
        'autofocus', 'disabled', 'form', 'multiple', 'name', 'required', 'size',
    ];

    public const ATTRIBUTES_TEXTAREA = [
        'autocapitalize', 'autocomplete', 'autofocus', 'cols', 'disabled', 'form', 'maxlength',
        'minlength', 'name', 'placeholder', 'readonly', 'required', 'rows', 'spellcheck', 'wrap',
    ];

    public const BOOLEAN_ATTRIBUTES = [
        'autofocus', 'capture', 'checked', 'disabled', 'draggable', 'formnovalidate', 'hidden',
        'multiple', 'novalidate', 'readonly', 'required', 'selected', 'spellcheck',
        'webkitdirectory',
    ];

    public const GLOBAL_ATTRIBUTES = [ // ie-style is used by https://github.com/nuxodin/ie11CustomProperties
        'accesskey', 'class', 'contenteditable', 'contextmenu', 'dir', 'draggable', 'dropzone',
        'hidden', 'id', 'ie-style', 'lang', 'spellcheck', 'style', 'tabindex', 'title',
    ];

    public const GLOBAL_WILDCARD_ATTRIBUTES = [
        'aria-', 'data-', 'item', 'on',
    ];

    public const INPUT_TYPES = [
        'button', 'checkbox', 'color', 'date', 'datetime-local', 'email', 'file', 'hidden', 'image',
        'month', 'number', 'password', 'radio', 'range', 'reset', 'search', 'submit', 'tel', 'text',
        'time', 'url', 'week',
    ];

    protected array $attributes = [];

    /**
     * @return static
     */
    public function __call(string $method, array $args = [])
    {
        $constant = 'static::ATTRIBUTES_'.strtoupper($method);
        $allowedAttributeKeys = defined($constant)
            ? constant($constant)
            : [];
        $this->normalize(Arr::consolidate(Arr::get($args, 0)), $allowedAttributeKeys);
        $this->normalizeInputType($method);
        return $this;
    }

    /**
     * @return static
     */
    public function set(array $attributes)
    {
        $this->normalize($attributes);
        return $this;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    public function toString(): string
    {
        $attributes = [];
        foreach ($this->attributes as $attribute => $value) {
            $quote = $this->getQuoteChar($attribute);
            $value = esc_attr(implode(',', (array) $value));
            $attributes[] = in_array($attribute, static::BOOLEAN_ATTRIBUTES)
                ? $attribute
                : "{$attribute}={$quote}{$value}{$quote}";
        }
        return implode(' ', $attributes);
    }

    protected function filterAttributes(array $allowedAttributeKeys): array
    {
        return array_intersect_key($this->attributes, array_flip($allowedAttributeKeys));
    }

    protected function filterGlobalAttributes(): array
    {
        $globalAttributes = $this->filterAttributes(static::GLOBAL_ATTRIBUTES);
        $wildcards = [];
        foreach (static::GLOBAL_WILDCARD_ATTRIBUTES as $wildcard) {
            $newWildcards = array_filter($this->attributes,
                fn ($key) => str_starts_with($key, $wildcard),
                ARRAY_FILTER_USE_KEY
            );
            $wildcards = array_merge($wildcards, $newWildcards);
        }
        return array_merge($globalAttributes, $wildcards);
    }

    protected function getPermanentAttributes(): array
    {
        $permanentAttributes = [];
        if (array_key_exists('value', $this->attributes)) {
            $permanentAttributes['value'] = $this->attributes['value'];
        }
        return $permanentAttributes;
    }

    protected function getQuoteChar(string $attribute): string
    {
        return str_starts_with($attribute, 'data-') ? '\'' : '"';
    }

    /**
     * @param mixed $value
     */
    protected function isAttributeKeyNumeric(string $key, $value): bool
    {
        return is_string($value)
            && is_numeric($key)
            && !array_key_exists($value, $this->attributes);
    }

    protected function normalize(array $args, array $allowedAttributeKeys = []): void
    {
        $this->attributes = array_change_key_case($args, CASE_LOWER);
        $this->normalizeBooleanAttributes();
        $this->normalizeDataAttributes();
        $this->normalizeStringAttributes();
        $this->removeEmptyAttributes();
        $this->removeIndexedAttributes();
        $this->attributes = array_merge(
            $this->filterGlobalAttributes(),
            $this->filterAttributes($allowedAttributeKeys)
        );
    }

    protected function normalizeBooleanAttributes(): void
    {
        foreach ($this->attributes as $key => $value) {
            if ($this->isAttributeKeyNumeric($key, $value)) {
                $key = $value;
                $value = true;
            }
            if (!in_array($key, static::BOOLEAN_ATTRIBUTES)) {
                continue;
            }
            $this->attributes[$key] = wp_validate_boolean($value);
        }
    }

    protected function normalizeDataAttributes(): void
    {
        foreach ($this->attributes as $key => $value) {
            if ($this->isAttributeKeyNumeric($key, $value)) {
                $key = $value;
                $value = '';
            }
            if (!str_starts_with($key, 'data-')) {
                continue;
            }
            if (is_array($value)) {
                $value = json_encode($value, JSON_HEX_APOS | JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
            $this->attributes[$key] = $value;
        }
    }

    protected function normalizeStringAttributes(): void
    {
        foreach ($this->attributes as $key => $value) {
            if (is_string($value)) {
                $this->attributes[$key] = esc_attr(trim($value));
            }
        }
    }

    protected function normalizeInputType(string $method): void
    {
        if ('input' != $method) {
            return;
        }
        $attributes = wp_parse_args($this->attributes, ['type' => '']);
        if (!in_array($attributes['type'], static::INPUT_TYPES)) {
            $this->attributes['type'] = 'text';
        }
    }

    protected function removeEmptyAttributes(): void
    {
        $attributes = $this->attributes;
        $permanentAttributes = $this->getPermanentAttributes();
        foreach ($this->attributes as $key => $value) {
            if (in_array($key, static::BOOLEAN_ATTRIBUTES) && !$value) {
                unset($attributes[$key]);
            }
            if (str_starts_with($key, 'data-')) {
                $permanentAttributes[$key] = $value;
                unset($attributes[$key]);
            }
        }
        $this->attributes = array_merge(Arr::removeEmptyValues($attributes), $permanentAttributes);
    }

    protected function removeIndexedAttributes(): void
    {
        $this->attributes = array_diff_key(
            $this->attributes,
            array_filter($this->attributes, 'is_numeric', ARRAY_FILTER_USE_KEY)
        );
    }
}
