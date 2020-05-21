<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Defaults\FieldDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use ReflectionClass;
use ReflectionMethod;

/**
 * @method string a(string|array ...$params)
 * @method string button(string|array ...$params)
 * @method string div(string|array ...$params)
 * @method string i(string|array ...$params)
 * @method string img(string|array ...$params)
 * @method string label(string|array ...$params)
 * @method string p(string|array ...$params)
 * @method string select(string|array ...$params)
 * @method string span(string|array ...$params)
 */
class Builder
{
    const INPUT_TYPES = [
        'checkbox', 'date', 'datetime-local', 'email', 'file', 'hidden', 'image', 'month',
        'number', 'password', 'radio', 'range', 'reset', 'search', 'submit', 'tel', 'text', 'time',
        'url', 'week',
    ];

    const TAGS_FORM = [
        'input', 'select', 'textarea',
    ];

    const TAGS_SINGLE = [
        'img',
    ];

    const TAGS_STRUCTURE = [
        'div', 'form', 'nav', 'ol', 'section', 'ul',
    ];

    const TAGS_TEXT = [
        'a', 'button', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'i', 'label', 'li', 'option', 'p', 'pre',
        'small', 'span',
    ];

    /**
     * @var array
     */
    public $args = [];

    /**
     * @var bool
     */
    public $render = false;

    /**
     * @var string
     */
    public $tag;

    /**
     * @var string
     */
    public $type;

    /**
     * @param string $method
     * @param array $methodArgs
     * @return string|void
     */
    public function __call($method, $methodArgs)
    {
        $instance = new static();
        $args = call_user_func_array([$instance, 'prepareArgs'], $methodArgs);
        $tag = Str::dashCase($method);
        $result = $instance->build($tag, $args);
        if (!$instance->render) {
            return $result;
        }
        echo $result;
    }

    /**
     * @param string $property
     * @param mixed $value
     * @return void
     */
    public function __set($property, $value)
    {
        $properties = [
            'args' => 'is_array',
            'render' => 'is_bool',
            'tag' => 'is_string',
        ];
        if (array_key_exists($property, $properties) && !empty($value)) {
            $this->$property = $value;
        }
    }

    /**
     * @return string
     */
    public function build($tag, array $args = [])
    {
        $this->setArgs($args, $tag);
        $this->setTag($tag);
        do_action_ref_array('site-reviews/builder', [$this]);
        $result = $this->isHtmlTag($this->tag)
            ? $this->buildElement()
            : $this->buildCustom($tag);
        return glsr()->filterString('builder/result', $result, $this);
    }

    /**
     * @return void|string
     */
    public function buildClosingTag()
    {
        return '</'.$this->tag.'>';
    }

    /**
     * @return string
     */
    public function buildDefaultElement($text = '')
    {
        if (empty($text)) {
            $text = $this->args['text'];
        }
        return $this->buildOpeningTag().$text.$this->buildClosingTag();
    }

    /**
     * @return void|string
     */
    public function buildElement()
    {
        if (in_array($this->tag, static::TAGS_SINGLE)) {
            return $this->buildOpeningTag();
        }
        if (in_array($this->tag, static::TAGS_FORM)) {
            return $this->{'buildForm'.ucfirst($this->tag)}().$this->buildFieldDescription();
        }
        return $this->buildDefaultElement();
    }

    /**
     * @param string $tag
     * @return void|string
     */
    public function buildCustom($tag)
    {
        if (class_exists($className = $this->getFieldClassName($tag))) {
            return (new $className($this))->build();
        }
        glsr_log()->error('Field class missing: '.$className);
    }

    /**
     * @return void|string
     */
    public function buildOpeningTag()
    {
        $attributes = glsr(Attributes::class)->{$this->tag}($this->args)->toString();
        return '<'.trim($this->tag.' '.$attributes).'>';
    }

    /**
     * @return string
     */
    public function raw(array $field)
    {
        unset($field['label']);
        return $this->{$field['type']}($field);
    }

    /**
     * @param string $className
     * @return void
     */
    public function setArgs(array $args = [], $type = '')
    {
        if (empty($args)) {
            return;
        }
        $args = $this->normalize($args);
        if (class_exists($className = $this->getFieldClassName($type))) {
            $args = $className::merge($args);
        }
        $args = glsr(FieldDefaults::class)->merge($args);
        $this->args = glsr()->filterArray('builder/'.$type.'/args', $args, $this);
    }

    /**
     * @param string $tag
     * @return void
     */
    public function setTag($tag)
    {
        $this->tag = in_array($tag, static::INPUT_TYPES)
            ? 'input'
            : $tag;
    }

    /**
     * @return string|void
     */
    protected function buildFieldDescription()
    {
        if (empty($this->args['description'])) {
            return;
        }
        if ($this->args['is_widget']) {
            return $this->small($this->args['description']);
        }
        return $this->p($this->args['description'], ['class' => 'description']);
    }

    /**
     * @return string|void
     */
    protected function buildFormInput()
    {
        if (!in_array($this->args['type'], ['checkbox', 'radio'])) {
            if (isset($this->args['multiple'])) {
                $this->args['name'] .= '[]';
            }
            return $this->buildFormLabel().$this->buildOpeningTag();
        }
        return empty($this->args['options'])
            ? $this->buildFormInputChoice()
            : $this->buildFormInputMultiChoice();
    }

    /**
     * @return string|void
     */
    protected function buildFormInputChoice()
    {
        if (!empty($this->args['text'])) {
            $this->args['label'] = $this->args['text'];
        }
        if (!$this->args['is_public']) {
            return $this->buildFormLabel([
                'class' => 'glsr-'.$this->args['type'].'-label',
                'text' => $this->buildOpeningTag().' '.$this->args['label'].'<span></span>',
            ]);
        }
        return $this->buildOpeningTag().$this->buildFormLabel([
            'class' => 'glsr-'.$this->args['type'].'-label',
            'text' => $this->args['label'].'<span></span>',
        ]);
    }

    /**
     * @return string|void
     */
    protected function buildFormInputMultiChoice()
    {
        if ('checkbox' == $this->args['type']) {
            $this->args['name'] .= '[]';
        }
        $index = 0;
        $options = array_reduce(array_keys($this->args['options']), function ($carry, $key) use (&$index) {
            return $carry.$this->li($this->{$this->args['type']}([
                'checked' => in_array($key, (array) $this->args['value']),
                'id' => $this->args['id'].'-'.$index++,
                'name' => $this->args['name'],
                'text' => $this->args['options'][$key],
                'value' => $key,
            ]));
        });
        return $this->ul($options, [
            'class' => $this->args['class'],
            'id' => $this->args['id'],
        ]);
    }

    /**
     * @return void|string
     */
    protected function buildFormLabel(array $customArgs = [])
    {
        if (empty($this->args['label']) || 'hidden' == $this->args['type']) {
            return;
        }
        return $this->label(wp_parse_args($customArgs, [
            'for' => $this->args['id'],
            'is_public' => $this->args['is_public'],
            'text' => $this->args['label'],
            'type' => $this->args['type'],
        ]));
    }

    /**
     * @return string|void
     */
    protected function buildFormSelect()
    {
        return $this->buildFormLabel().$this->buildDefaultElement($this->buildFormSelectOptions());
    }

    /**
     * @return string|void
     */
    protected function buildFormSelectOptions()
    {
        return array_reduce(array_keys($this->args['options']), function ($carry, $key) {
            return $carry.$this->option([
                'selected' => $this->args['value'] === (string) $key,
                'text' => $this->args['options'][$key],
                'value' => $key,
            ]);
        });
    }

    /**
     * @return string|void
     */
    protected function buildFormTextarea()
    {
        return $this->buildFormLabel().$this->buildDefaultElement($this->args['value']);
    }

    /**
     * @param string $tag
     * @return bool
     */
    protected function isHtmlTag($tag)
    {
        return in_array($tag, array_merge(
            static::TAGS_FORM,
            static::TAGS_SINGLE,
            static::TAGS_STRUCTURE,
            static::TAGS_TEXT
        ));
    }

    /**
     * @param string $type
     * @return string
     */
    protected function getFieldClassName($tag)
    {
        $className = Helper::buildClassName($tag, __NAMESPACE__.'\Fields');
        return glsr()->filterString('builder/field/'.$tag, $className);
    }

    /**
     * @return array
     */
    protected function normalize(array $args)
    {
        if (!isset($args['is_public'])) {
            $args['is_public'] = false;
        }
        return $args;
    }

    /**
     * @param string|array ...$params
     * @return array
     */
    protected function prepareArgs(...$params)
    {
        $args = [];
        $parameter1 = Arr::get($params, 0);
        $parameter2 = Arr::get($params, 1);
        if (is_string($parameter1) || is_numeric($parameter1)) {
            $args['text'] = $parameter1;
        }
        if (is_array($parameter1)) {
            $args += $parameter1;
        } elseif (is_array($parameter2)) {
            $args += $parameter2; // does not overwrite $args['text']
        }
        return $args;
    }
}
