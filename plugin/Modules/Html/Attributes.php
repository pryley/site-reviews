<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Helpers\Arr;

/**
 * HTML Living Standard, 16 November 2026; W3C's HTML Media Capture.
 *
 * @see https://html.spec.whatwg.org/commit-snapshots/efcc0bcdec596651e2cb316ab076c0bdc7c4d120/
 * @see https://www.w3.org/TR/html-media-capture/
 */
class Attributes
{
    public const ATTRIBUTES_A = [
        'download', 'href', 'hreflang', 'ping', 'referrerpolicy', 'rel', 'target', 'type',
    ];

    public const ATTRIBUTES_BUTTON = [
        'command', 'commandfor', 'disabled', 'form', 'formaction', 'formenctype', 'formmethod',
        'formnovalidate', 'formtarget', 'name', 'popovertarget', 'popovertargetaction', 'type',
        'value',
    ];

    public const ATTRIBUTES_FORM = [
        'accept-charset', 'action', 'autocomplete', 'enctype', 'method', 'name', 'novalidate',
        'rel', 'target',
    ];

    public const ATTRIBUTES_IMG = [
        'alt', 'controls', 'crossorigin', 'decoding', 'fetchpriority', 'height', 'ismap', 'loading',
        'referrerpolicy', 'sizes', 'src', 'srcset', 'usemap', 'width',
    ];

    public const ATTRIBUTES_INPUT = [
        'accept', 'alpha', 'alt', 'autocomplete', 'capture', 'checked', 'colorspace', 'dirname',
        'disabled', 'form', 'formaction', 'formenctype', 'formmethod', 'formnovalidate',
        'formtarget', 'height', 'incremental', 'list', 'max', 'maxlength', 'min', 'minlength',
        'multiple', 'name', 'pattern', 'placeholder', 'popovertarget', 'popovertargetaction',
        'readonly', 'required', 'results', 'size', 'src', 'step', 'switch', 'type', 'value',
        'webkitdirectory', 'width',
    ];

    public const ATTRIBUTES_INPUT_EXCLUDED = [
        'autocapitalize' => ['url', 'email', 'password'],
        'autocomplete' => ['checkbox', 'radio', 'button'],
        'hidden' => ['hidden'],
        'list' => ['hidden', 'password', 'checkbox', 'radio', 'button'],
        'readonly' => ['hidden', 'range', 'color', 'checkbox', 'radio', 'button'],
        'required' => ['hidden', 'range', 'color', 'button'],
        'value' => ['image'],
    ];

    public const ATTRIBUTES_INPUT_INCLUDED = [
        'accept' => ['file'],
        'alpha' => ['color'],
        'alt' => ['image'],
        'capture' => ['file'],
        'checked' => ['checkbox', 'radio'],
        'colorspace' => ['color'],
        'dirname' => ['hidden', 'text', 'search', 'url', 'tel', 'email', 'password', 'submit'],
        'formaction' => ['image', 'submit'],
        'formenctype' => ['image', 'submit'],
        'formmethod' => ['image', 'submit'],
        'formnovalidate' => ['image', 'submit'],
        'formtarget' => ['image', 'submit'],
        'height' => ['image'],
        'max' => ['date', 'month', 'week', 'time', 'datetime-local', 'number', 'range'],
        'maxlength' => ['text', 'search', 'url', 'tel', 'email', 'password'],
        'min' => ['date', 'month', 'week', 'time', 'datetime-local', 'number', 'range'],
        'minlength' => ['text', 'search', 'url', 'tel', 'email', 'password'],
        'multiple' => ['email', 'file'],
        'pattern' => ['text', 'search', 'url', 'tel', 'email', 'password'],
        'placeholder' => ['text', 'search', 'url', 'tel', 'email', 'password', 'number'],
        'popovertarget' => ['button', 'image', 'reset', 'submit'],
        'popovertargetaction' => ['button', 'image', 'reset', 'submit'],
        'size' => ['text', 'search', 'url', 'tel', 'email', 'password'],
        'src' => ['image'],
        'step' => ['date', 'month', 'week', 'time', 'datetime-local', 'number', 'range'],
        'switch' => ['checkbox'],
        'width' => ['image'],
    ];

    public const ATTRIBUTES_LABEL = [
        'for',
    ];

    public const ATTRIBUTES_LI = [
        'value',
    ];

    public const ATTRIBUTES_OL = [
        'reversed', 'start', 'type',
    ];

    public const ATTRIBUTES_OPTGROUP = [
        'disabled', 'label',
    ];

    public const ATTRIBUTES_OPTION = [
        'disabled', 'label', 'selected', 'value',
    ];

    public const ATTRIBUTES_SELECT = [
        'autocomplete', 'disabled', 'form', 'multiple', 'name', 'required', 'size',
    ];

    public const ATTRIBUTES_TEXTAREA = [
        'autocomplete', 'cols', 'dirname', 'disabled', 'form', 'maxlength', 'minlength', 'name',
        'placeholder', 'readonly', 'required', 'rows', 'wrap',
    ];

    public const BOOLEAN_ATTRIBUTES = [
        'alpha', 'autofocus', 'checked', 'controls', 'disabled', 'formnovalidate', 'headingreset',
        'inert', 'ismap', 'itemscope', 'multiple', 'novalidate', 'readonly', 'required',
        'reversed', 'selected', 'switch', 'webkitdirectory',
    ];

    public const ENUMERATED_ATTRIBUTES = [
        'autocorrect' => ['off', 'on'],
        'capture' => ['environment', 'user'],
        'contenteditable' => ['false', 'plaintext-only', 'true'],
        'draggable' => ['false', 'true'],
        'hidden' => ['hidden', 'until-found'],
        'popover' => ['auto', 'hint', 'manual'],
        'spellcheck' => ['false', 'true'],
        'translate' => ['no', 'yes'],
        'writingsuggestions' => ['false', 'true'],
    ];

    public const GLOBAL_ATTRIBUTES = [
        'accesskey', 'autocapitalize', 'autocorrect', 'autofocus', 'class', 'contenteditable',
        'dir', 'draggable', 'enterkeyhint', 'headingoffset', 'headingreset', 'hidden', 'id',
        'inert', 'inputmode', 'is', 'lang', 'nonce', 'popover', 'role', 'slot', 'spellcheck',
        'style', 'tabindex', 'title', 'translate', 'writingsuggestions',
    ];

    public const GLOBAL_WILDCARD_ATTRIBUTES = [
        'aria-', 'data-', 'item', 'on',
    ];

    public const INPUT_TYPES = [
        'button', 'checkbox', 'color', 'date', 'datetime-local', 'email', 'file', 'hidden', 'image',
        'month', 'number', 'password', 'radio', 'range', 'reset', 'search', 'submit', 'tel', 'text',
        'time', 'url', 'week',
    ];

    public const URL_ATTRIBUTES = [
        'href', 'src',
    ];

    protected array $attributes = [];

    /**
     * @return static
     */
    public function __call(string $method, array $args = [])
    {
        $constant = 'static::ATTRIBUTES_'.strtoupper($method);
        $allowedAttributes = defined($constant)
            ? constant($constant)
            : [];
        $this->normalize(Arr::consolidate(Arr::get($args, 0)), $allowedAttributes);
        if ('input' === $method) {
            $this->normalizeInput();
        }
        return $this;
    }

    public function isInputType(string $type): bool
    {
        return in_array($type, Attributes::INPUT_TYPES);
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
            if ($this->isAttributeKeyAlone($attribute, $value)) {
                $attributes[] = $attribute;
                continue;
            }
            if (!is_scalar($value)) {
                continue;
            }
            $attributes[] = "{$attribute}=\"{$value}\"";
        }
        return implode(' ', $attributes);
    }

    protected function filterAttributes(array $allowedAttributes): array
    {
        return array_intersect_key($this->attributes, array_flip($allowedAttributes));
    }

    protected function filterGlobalAttributes(): array
    {
        $globalAttributes = $this->filterAttributes(static::GLOBAL_ATTRIBUTES);
        $wildcards = [];
        foreach (static::GLOBAL_WILDCARD_ATTRIBUTES as $wildcard) {
            $newWildcards = array_filter($this->attributes,
                fn ($key) => str_starts_with($key, $wildcard),
                \ARRAY_FILTER_USE_KEY
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

    /**
     * @param mixed $value
     */
    protected function isAttributeKeyAlone(string $key, $value): bool
    {
        if (!in_array($key, static::BOOLEAN_ATTRIBUTES) && !array_key_exists($key, static::ENUMERATED_ATTRIBUTES)) {
            return false;
        }
        return $key === $value || true === $value;
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

    protected function normalize(array $args, array $allowedAttributes = []): void
    {
        $this->attributes = array_change_key_case($args, \CASE_LOWER);
        $this->normalizeBooleanAttributes();
        $this->normalizeEnumeratedAttributes();
        $this->normalizeDataAttributes();
        $this->normalizeStringAttributes();
        $this->removeEmptyAttributes();
        $this->removeIndexedAttributes();
        $this->attributes = array_merge(
            $this->filterGlobalAttributes(),
            $this->filterAttributes($allowedAttributes)
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
                $value = wp_json_encode($value, \JSON_HEX_APOS | \JSON_NUMERIC_CHECK | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE);
            }
            $this->attributes[$key] = esc_attr($value);
        }
    }

    protected function normalizeEnumeratedAttributes(): void
    {
        foreach ($this->attributes as $key => $value) {
            if ($this->isAttributeKeyNumeric($key, $value)) {
                $key = $value;
                $value = true;
            }
            if (!array_key_exists($key, static::ENUMERATED_ATTRIBUTES) || in_array($value, [null, ''], true)) {
                continue;
            }
            $keywords = static::ENUMERATED_ATTRIBUTES[$key];
            if (in_array($value, $keywords, true)) {
                continue; // value is keyword
            }
            $isOn = wp_validate_boolean($value);
            $keyword = current(array_intersect($isOn ? ['on', 'true', 'yes'] : ['false', 'no', 'off'], $keywords));
            $this->attributes[$key] = false === $keyword ? $isOn : $keyword; // the name alone, or left out
        }
    }

    protected function normalizeInput(): void
    {
        $attributes = wp_parse_args($this->attributes, ['type' => '']);
        if (!in_array($attributes['type'], static::INPUT_TYPES)) {
            $attributes['type'] = 'text';
        }
        $included = array_intersect_key(static::ATTRIBUTES_INPUT_INCLUDED, $attributes);
        foreach ($included as $attribute => $types) {
            if (!in_array($attributes['type'], $types)) {
                unset($attributes[$attribute]);
            }
        }
        $excluded = array_intersect_key(static::ATTRIBUTES_INPUT_EXCLUDED, $attributes);
        foreach ($excluded as $attribute => $types) {
            if (in_array($attributes['type'], $types)) {
                unset($attributes[$attribute]);
            }
        }
        $this->attributes = $attributes;
    }

    protected function normalizeStringAttributes(): void
    {
        foreach ($this->attributes as $key => $value) {
            if (!is_string($value)) {
                continue;
            }
            if (in_array($key, static::URL_ATTRIBUTES)) {
                $this->attributes[$key] = esc_url(trim($value));
            } else {
                $this->attributes[$key] = esc_attr(trim($value));
            }
        }
    }

    protected function removeEmptyAttributes(): void
    {
        $attributes = $this->attributes;
        $permanentAttributes = $this->getPermanentAttributes();
        foreach ($this->attributes as $key => $value) {
            if (!$value && $this->isAttributeKeyAlone($key, true)) { // true checks only the key
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
            array_filter($this->attributes, 'is_numeric', \ARRAY_FILTER_USE_KEY)
        );
    }
}
