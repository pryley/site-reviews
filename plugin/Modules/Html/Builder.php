<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Contracts\BuilderContract;
use GeminiLabs\SiteReviews\Contracts\FieldContract;
use GeminiLabs\SiteReviews\Defaults\FieldDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;

/**
 * This class generates HTML tags without additional DOM markup.
 *
 * @method string a(string|array ...$params)
 * @method string button(string|array ...$params)
 * @method string div(string|array ...$params)
 * @method string form(string|array ...$params)
 * @method string h1(string|array ...$params)
 * @method string h2(string|array ...$params)
 * @method string h3(string|array ...$params)
 * @method string h4(string|array ...$params)
 * @method string h5(string|array ...$params)
 * @method string h6(string|array ...$params)
 * @method string i(string|array ...$params)
 * @method string img(string|array ...$params)
 * @method string input(string|array ...$params)
 * @method string label(string|array ...$params)
 * @method string li(string|array ...$params)
 * @method string nav(string|array ...$params)
 * @method string ol(string|array ...$params)
 * @method string optgroup(string|array ...$params)
 * @method string option(string|array ...$params)
 * @method string p(string|array ...$params)
 * @method string pre(string|array ...$params)
 * @method string section(string|array ...$params)
 * @method string select(string|array ...$params)
 * @method string small(string|array ...$params)
 * @method string span(string|array ...$params)
 * @method string textarea(string|array ...$params)
 * @method string ul(string|array ...$params)
 */
class Builder implements BuilderContract
{
    public const TAGS_FIELD = [
        'input', 'select', 'textarea',
    ];

    public const TAGS_STRUCTURE = [
        'div', 'form', 'nav', 'ol', 'section', 'ul',
    ];

    public const TAGS_TEXT = [
        'a', 'button', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'i', 'label', 'li', 'option', 'optgroup',
        'p', 'pre', 'small', 'span',
    ];

    public const TAGS_VOID = [
        'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'source',
        'track', 'wbr',
    ];

    protected Arguments $args;

    protected string $tag;

    /**
     * This creates a new Builder instance to build an element.
     */
    public function __call(string $method, array $arguments): string
    {
        if (!is_array($args = array_shift($arguments))) {
            $text = Cast::toString($args);
            $args = Arr::consolidate(array_shift($arguments));
            $args['text'] = $text;
        }
        if (empty($method)) {
            return '';
        }
        $tag = Str::dashCase($method);
        return (new static())->build($tag, $args);
    }

    public function args(): Arguments
    {
        return $this->args ??= new Arguments();
    }

    /**
     * This uses the existing Builder instance to build an element
     * and overwrites existing tag and arguments
     * with the passed arguments.
     */
    public function build(string $tag, array $args = []): string
    {
        if (in_array($tag, static::TAGS_FIELD)) {
            $args = glsr(FieldDefaults::class)->merge($args);
        }
        $args = glsr()->filterArray("builder/{$tag}/args", $args, $this);
        $this->args = new Arguments($args);
        $this->tag = $tag;
        return $this->process();
    }

    /**
     * This uses the Field's builder instance/build method to build the element.
     */
    public function buildField(FieldContract $field): string
    {
        return $field->build();
    }

    public function field(array $args): FieldContract
    {
        return new Field($args);
    }

    public function set(string $key, $value): void
    {
        $this->args()->set($key, $value);
    }

    public function tag(): string
    {
        return $this->tag ??= '';
    }

    protected function buildElement(): string
    {
        $text = $this->args()->text;
        return $this->buildTagStart().$text.$this->buildTagEnd();
    }

    protected function buildFieldElement(): string
    {
        $method = Helper::buildMethodName('build', 'field', $this->tag(), 'element');
        return call_user_func([$this, $method]);
    }

    protected function buildFieldInputChoice(): string
    {
        $text = $this->args()->text ?: $this->args()->label;
        if (empty($text)) {
            return $this->buildVoidElement();
        }
        return $this->label([
            'for' => $this->args()->id,
            'text' => "{$this->buildVoidElement()} {$text}",
        ]);
    }

    protected function buildFieldInputChoices(): string
    {
        $index = 0;
        $values = array_keys($this->args()->options);
        return array_reduce($values, function ($carry, $value) use (&$index) {
            return $carry.$this->input([
                'checked' => in_array($value, $this->args()->cast('value', 'array')),
                'class' => $this->args()->class,
                'disabled' => $this->args()->disabled,
                'id' => $this->indexedId(++$index),
                'label' => $this->args()->options[$value],
                'name' => $this->args()->name,
                'required' => $this->args()->required,
                'tabindex' => $this->args()->tabindex,
                'type' => $this->args()->type,
                'value' => $value,
            ]);
        }, '');
    }

    protected function buildFieldInputElement(): string
    {
        if (!in_array($this->args()->type, ['checkbox', 'radio'])) {
            return $this->buildFieldLabel().$this->buildVoidElement();
        }
        if (empty($this->args()->options)) {
            return $this->buildFieldInputChoice();
        }
        return $this->buildFieldInputChoices();
    }

    protected function buildFieldLabel(): string
    {
        if ('hidden' === $this->args()->type) {
            return '';
        }
        if (empty($this->args()->label)) {
            return '';
        }
        return $this->label([
            'for' => $this->args()->id,
            'text' => $this->args()->label,
        ]);
    }

    protected function buildFieldSelectElement(): string
    {
        $options = $this->buildFieldSelectOptions();
        $select = $this->buildTagStart().$options.$this->buildTagEnd();
        return $this->buildFieldLabel().$select;
    }

    protected function buildFieldSelectOptgroup(array $options, string $label): string
    {
        $values = array_keys($options);
        $children = array_reduce($values, fn ($carry, $value) => $carry.$this->buildFieldSelectOption([
            'text' => $options[$value],
            'value' => $value,
        ]), '');
        return $this->optgroup([
            'label' => $label,
            'text' => $children,
        ]);
    }

    protected function buildFieldSelectOption(array $args): string
    {
        $selected = in_array($args['value'] ?? null, $this->args()->cast('value', 'array'));
        $args = wp_parse_args($args, [
            'selected' => $selected,
            'text' => '',
            'value' => '',
        ]);
        if (!is_array($args['text'])) {
            return $this->option($args);
        }
        // If $args['text'] is an array and has a title and text key then create an option tag
        // with a title attribute to provide accessibility when the text is made up of symbols.
        if (Arr::compare(array_keys($args['text']), ['text', 'title'])) {
            return $this->option(wp_parse_args($args['text'], $args));
        }
        return '';
    }

    protected function buildFieldSelectOptions(): string
    {
        $options = $this->args()->options;
        if ($this->args()->placeholder) {
            $options = Arr::prepend($options, $this->args()->placeholder, '');
        }
        $values = array_keys($options);
        return array_reduce($values, function ($carry, $value) use ($options) {
            $option = $this->buildFieldSelectOption([
                'text' => $options[$value],
                'value' => $value,
            ]);
            if (empty($option)) {
                return $carry.$this->buildFieldSelectOptgroup($options[$value], $value);
            }
            return $carry.$option;
        }, '');
    }

    protected function buildFieldTextareaElement(): string
    {
        $text = esc_html($this->args()->cast('value', 'string'));
        $textarea = $this->buildTagStart().$text.$this->buildTagEnd();
        return $this->buildFieldLabel().$textarea;
    }

    protected function buildTagEnd(): string
    {
        if (in_array($this->tag(), static::TAGS_VOID)) {
            return '';
        }
        return "</{$this->tag()}>";
    }

    protected function buildTagStart(): string
    {
        $attributes = glsr(Attributes::class)->{$this->tag()}($this->args()->toArray())->toString();
        $tag = trim("{$this->tag()} {$attributes}");
        if (in_array($this->tag(), static::TAGS_VOID)) {
            return "<{$tag} />";
        }
        return "<{$tag}>";
    }

    protected function buildVoidElement(): string
    {
        return $this->buildTagStart();
    }

    protected function indexedId(int $index): string
    {
        if (!empty($this->args()->id)) {
            return "{$this->args()->id}-{$index}";
        }
        return $this->args()->id;
    }

    protected function process(): string
    {
        glsr()->action('builder', $this); // This hook is used in PublicController to add styled classes
        if (in_array($this->tag(), static::TAGS_FIELD)) { // check this first
            $result = $this->buildFieldElement();
        } elseif (in_array($this->tag(), static::TAGS_VOID)) {
            $result = $this->buildVoidElement();
        } else {
            $result = $this->buildElement();
        }
        $result = glsr()->filterString('builder/result', $result, $this);
        return $result;
    }
}
