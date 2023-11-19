<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Contracts\BuilderContract;
use GeminiLabs\SiteReviews\Defaults\FieldDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;

/**
 * This class generates raw HTML tags without additional DOM markup.
 *
 * @method string a(string|array ...$params)
 * @method string button(string|array ...$params)
 * @method string div(string|array ...$params)
 * @method string i(string|array ...$params)
 * @method string img(string|array ...$params)
 * @method string input(string|array ...$params)
 * @method string li(string|array ...$params)
 * @method string label(string|array ...$params)
 * @method string option(string|array ...$params)
 * @method string p(string|array ...$params)
 * @method string select(string|array ...$params)
 * @method string small(string|array ...$params)
 * @method string span(string|array ...$params)
 * @method string textarea(string|array ...$params)
 * @method string ul(string|array ...$params)
 */
class Builder implements BuilderContract
{
    public const INPUT_TYPES = [
        'checkbox', 'date', 'datetime-local', 'email', 'file', 'hidden', 'image', 'month',
        'number', 'password', 'radio', 'range', 'reset', 'search', 'submit', 'tel', 'text', 'time',
        'url', 'week',
    ];

    public const TAGS_FORM = [
        'input', 'select', 'textarea',
    ];

    public const TAGS_SINGLE = [
        'img',
    ];

    public const TAGS_STRUCTURE = [
        'div', 'form', 'nav', 'ol', 'section', 'ul',
    ];

    public const TAGS_TEXT = [
        'a', 'button', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'i', 'label', 'li', 'option', 'optgroup',
        'p', 'pre', 'small', 'span',
    ];

    /** @var Arguments */
    public $args;

    public bool $render = false;

    public string $tag = '';

    public string $type = '';

    /**
     * @return string|void
     */
    public function __call(string $method, array $args = [])
    {
        $instance = new static();
        $args = call_user_func_array([$instance, 'prepareArgs'], $args);
        $tag = Str::dashCase($method);
        $result = $instance->build($tag, $args);
        if (!$instance->render) {
            return $result;
        }
        echo $result;
    }

    /**
     * @param mixed $value
     */
    public function __set(string $property, $value): void
    {
        $method = Helper::buildMethodName('set', $property);
        if (method_exists($this, $method)) {
            call_user_func([$this, $method], $value);
        }
    }

    public function build(string $tag, array $args = []): string
    {
        $this->setArgs($args, $tag);
        $this->setTag($tag);
        glsr()->action('builder', $this);
        $result = $this->isHtmlTag($this->tag)
            ? $this->buildElement()
            : $this->buildCustom($tag);
        return glsr()->filterString('builder/result', $result, $this);
    }

    public function buildClosingTag(): string
    {
        return "</{$this->tag}>";
    }

    public function buildCustom(string $tag): string
    {
        if (class_exists($className = $this->getFieldClassName($tag))) {
            return (new $className($this))->build();
        }
        glsr_log()->error("Field [$className] missing.");
        return '';
    }

    public function buildDefaultElement(string $text = ''): string
    {
        $text = Helper::ifEmpty($text, $this->args->text, $strict = true);
        return $this->buildOpeningTag().$text.$this->buildClosingTag();
    }

    public function buildElement(): string
    {
        if (in_array($this->tag, static::TAGS_SINGLE)) {
            return $this->buildOpeningTag();
        }
        if (in_array($this->tag, static::TAGS_FORM)) {
            return $this->buildFormElement();
        }
        return $this->buildDefaultElement();
    }

    public function buildFormElement(): string
    {
        $method = Helper::buildMethodName('buildForm', $this->tag);
        return $this->$method();
    }

    public function buildOpeningTag(): string
    {
        $attributes = glsr(Attributes::class)->{$this->tag}($this->args->toArray())->toString();
        $tagContent = trim("{$this->tag} {$attributes}");
        return "<{$tagContent}>";
    }

    public function raw(array $field): string
    {
        unset($field['label']);
        return $this->{$field['type']}($field);
    }

    public function setArgs(array $args = [], string $type = ''): void
    {
        if (!empty($args)) {
            $args = $this->normalize($args, $type);
            $options = glsr()->args($args)->options;
            $args = glsr(FieldDefaults::class)->merge($args);
            if (is_array($options)) {
                // Merging reindexes the options array, this may not be desirable
                // if the array is indexed so here we restore the original options array.
                // It's a messy hack, but it will have to do for now.
                $args['options'] = $options;
            }
        }
        $args = glsr()->filterArray("builder/{$type}/args", $args, $this);
        $this->args = glsr()->args($args);
    }

    public function setRender(bool $bool): void
    {
        $this->render = $bool;
    }

    public function setTag(string $tag): void
    {
        $this->tag = Helper::ifTrue(in_array($tag, static::INPUT_TYPES), 'input', $tag);
    }

    protected function buildFormInput(): string
    {
        if (!in_array($this->args->type, ['checkbox', 'radio'])) {
            return $this->buildFormLabel().$this->buildOpeningTag();
        }
        return empty($this->args->options)
            ? $this->buildFormInputChoice()
            : $this->buildFormInputChoices();
    }

    protected function buildFormInputChoice(): string
    {
        if ($label = Helper::ifEmpty($this->args->text, $this->args->label)) {
            $openingTag = $this->buildOpeningTag();
            return $this->buildFormLabel([
                'text' => "{$openingTag} {$label}",
            ]);
        }
        return $this->buildOpeningTag();
    }

    protected function buildFormInputChoices(): string
    {
        $index = 0;
        return array_reduce(array_keys($this->args->options), function ($carry, $value) use (&$index) {
            return $carry.$this->input([
                'checked' => in_array($value, $this->args->cast('value', 'array')),
                'class' => $this->args->class,
                'disabled' => $this->args->disabled,
                'id' => $this->indexedId(++$index),
                'label' => $this->args->options[$value],
                'name' => $this->args->name,
                'required' => $this->args->required,
                'tabindex' => $this->args->tabindex,
                'type' => $this->args->type,
                'value' => $value,
            ]);
        }, '');
    }

    protected function buildFormLabel(array $customArgs = []): string
    {
        if (empty($this->args->label) || 'hidden' === $this->args->type) {
            return '';
        }
        return $this->label(wp_parse_args($customArgs, [
            'for' => $this->args->id,
            'text' => $this->args->label,
        ]));
    }

    protected function buildFormSelect(): string
    {
        return $this->buildFormLabel().$this->buildDefaultElement($this->buildFormSelectOptions());
    }

    protected function buildFormSelectOptions(): string
    {
        $options = $this->args->cast('options', 'array');
        if ($this->args->placeholder) {
            $options = Arr::prepend($options, $this->args->placeholder, '');
        }
        return array_reduce(array_keys($options), function ($carry, $key) use ($options) {
            $value = $options[$key];
            if (is_array($value)) {
                // if the option is an array and has a title and value key
                // then treat the option as a string with a title attribute
                if (array_diff(array_keys($value), ['title', 'value'])) {
                    return $carry.$this->buildFormSelectOptGroup($value, $key);
                }
                $title = $options[$key]['title'];
                $value = $options[$key]['value'];
            }
            return $carry.$this->option([
                'selected' => $this->args->cast('value', 'string') === Cast::toString($key),
                'text' => $value,
                'title' => $title ?? '',
                'value' => $key,
            ]);
        }, '');
    }

    protected function buildFormSelectOptGroup($options, $label): string
    {
        $children = array_reduce(array_keys($options), function ($carry, $key) use ($options) {
            $option = $options[$key];
            if (wp_is_numeric_array($option)) {
                $option = Arr::getAs('string', $options[$key], 0);
                $title = Arr::getAs('string', $options[$key], 1);
            }
            return $carry.glsr(Builder::class)->option([
                'selected' => $this->args->cast('value', 'string') === Cast::toString($key),
                'text' => $option,
                'title' => $title ?? '',
                'value' => $key,
            ]);
        });
        return glsr(Builder::class)->optgroup([
            'label' => $label,
            'text' => $children,
        ]);
    }

    protected function buildFormTextarea(): string
    {
        return $this->buildFormLabel().$this->buildDefaultElement(
            esc_html($this->args->cast('value', 'string'))
        );
    }

    protected function indexedId(int $index): string
    {
        return Helper::ifTrue(count($this->args->options) > 1,
            sprintf('%s-%d', $this->args->id, $index),
            $this->args->id
        );
    }

    protected function isHtmlTag(string $tag): bool
    {
        return in_array($tag, array_merge(
            static::TAGS_FORM,
            static::TAGS_SINGLE,
            static::TAGS_STRUCTURE,
            static::TAGS_TEXT
        ));
    }

    protected function getFieldClassName(string $tag): string
    {
        $className = Helper::buildClassName($tag, __NAMESPACE__.'\Fields');
        return glsr()->filterString("builder/field/{$tag}", $className);
    }

    protected function normalize(array $args, string $type): array
    {
        if (class_exists($className = $this->getFieldClassName($type))) {
            $args = $className::merge($args);
        }
        return $args;
    }

    /**
     * @param string|array $params,...
     */
    protected function prepareArgs(...$params): array
    {
        if (is_array($parameter1 = array_shift($params))) {
            return $parameter1;
        }
        $parameter2 = Arr::consolidate(array_shift($params));
        if (is_scalar($parameter1)) {
            $parameter2['text'] = $parameter1;
        }
        return $parameter2;
    }
}
