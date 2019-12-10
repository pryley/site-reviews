<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Defaults\BuilderDefaults;
use GeminiLabs\SiteReviews\Helper;

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
     * @param string $method
     * @param array $args
     * @return string|void
     */
    public function __call($method, $args)
    {
        $instance = new static();
        $instance->setTagFromMethod($method);
        call_user_func_array([$instance, 'normalize'], $args += ['', '']);
        $tags = array_merge(static::TAGS_FORM, static::TAGS_SINGLE, static::TAGS_STRUCTURE, static::TAGS_TEXT);
        do_action_ref_array('site-reviews/builder', [$instance]);
        $generatedTag = in_array($instance->tag, $tags)
            ? $instance->buildTag()
            : $instance->buildCustomField();
        $generatedTag = apply_filters('site-reviews/builder/result', $generatedTag, $instance);
        if (!$this->render) {
            return $generatedTag;
        }
        echo $generatedTag;
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
        if (!isset($properties[$property])
            || empty(array_filter([$value], $properties[$property]))
        ) {
            return;
        }
        $this->$property = $value;
    }

    /**
     * @return void|string
     */
    public function getClosingTag()
    {
        if (empty($this->tag)) {
            return;
        }
        return '</'.$this->tag.'>';
    }

    /**
     * @return void|string
     */
    public function getOpeningTag()
    {
        if (empty($this->tag)) {
            return;
        }
        $attributes = glsr(Attributes::class)->{$this->tag}($this->args)->toString();
        return '<'.trim($this->tag.' '.$attributes).'>';
    }

    /**
     * @return void|string
     */
    public function getTag()
    {
        if (in_array($this->tag, static::TAGS_SINGLE)) {
            return $this->getOpeningTag();
        }
        if (!in_array($this->tag, static::TAGS_FORM)) {
            return $this->buildDefaultTag();
        }
        return call_user_func([$this, 'buildForm'.ucfirst($this->tag)]).$this->buildFieldDescription();
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
     * @return string|void
     */
    protected function buildCustomField()
    {
        $className = $this->getCustomFieldClassName();
        if (class_exists($className)) {
            return (new $className($this))->build();
        }
        glsr_log()->error('Field missing: '.$className);
    }

    /**
     * @return string|void
     */
    protected function buildDefaultTag($text = '')
    {
        if (empty($text)) {
            $text = $this->args['text'];
        }
        return $this->getOpeningTag().$text.$this->getClosingTag();
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
                $this->args['name'].= '[]';
            }
            return $this->buildFormLabel().$this->getOpeningTag();
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
                'text' => $this->getOpeningTag().' '.$this->args['label'].'<span></span>',
            ]);
        }
        return $this->getOpeningTag().$this->buildFormLabel([
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
            $this->args['name'].= '[]';
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
        return $this->buildFormLabel().$this->buildDefaultTag($this->buildFormSelectOptions());
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
        return $this->buildFormLabel().$this->buildDefaultTag($this->args['value']);
    }

    /**
     * @return string|void
     */
    protected function buildTag()
    {
        $this->mergeArgsWithRequiredDefaults();
        return $this->getTag();
    }

    /**
     * @return string
     */
    protected function getCustomFieldClassName()
    {
        $classname = Helper::buildClassName($this->tag, __NAMESPACE__.'\Fields');
        return apply_filters('site-reviews/builder/field/'.$this->tag, $classname);
    }

    /**
     * @return void
     */
    protected function mergeArgsWithRequiredDefaults()
    {
        $className = $this->getCustomFieldClassName();
        if (class_exists($className)) {
            $this->args = $className::merge($this->args);
        }
        $this->args = glsr(BuilderDefaults::class)->merge($this->args);
    }

    /**
     * @param string|array ...$params
     * @return void
     */
    protected function normalize(...$params)
    {
        if (is_string($params[0]) || is_numeric($params[0])) {
            $this->setNameOrTextAttributeForTag($params[0]);
        }
        if (is_array($params[0])) {
            $this->args += $params[0];
        } elseif (is_array($params[1])) {
            $this->args += $params[1];
        }
        if (!isset($this->args['is_public'])) {
            $this->args['is_public'] = false;
        }
    }

    /**
     * @param string $value
     * @return void
     */
    protected function setNameOrTextAttributeForTag($value)
    {
        $attribute = in_array($this->tag, static::TAGS_FORM)
            ? 'name'
            : 'text';
        $this->args[$attribute] = $value;
    }

    /**
     * @param string $method
     * @return void
     */
    protected function setTagFromMethod($method)
    {
        $this->tag = strtolower($method);
        if (in_array($this->tag, static::INPUT_TYPES)) {
            $this->args['type'] = $this->tag;
            $this->tag = 'input';
        }
    }
}
