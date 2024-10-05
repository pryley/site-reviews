<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Contracts\BuilderContract;
use GeminiLabs\SiteReviews\Contracts\FieldContract;
use GeminiLabs\SiteReviews\Contracts\FieldElementContract;
use GeminiLabs\SiteReviews\Defaults\FieldConditionDefaults;
use GeminiLabs\SiteReviews\Defaults\FieldDefaults;
use GeminiLabs\SiteReviews\Defaults\FieldRuleDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\FieldElements\UnknownElement;

/**
 * This class normalizes the DOM markup surrounding the field element.
 * Styled class attributes for the field are merged here.
 *
 * @property string       $after
 * @property bool         $checked
 * @property string       $class
 * @property string       $conditions
 * @property string       $description
 * @property array        $errors
 * @property string       $group
 * @property string       $id
 * @property bool         $is_custom
 * @property bool         $is_hidden
 * @property bool         $is_raw
 * @property bool         $is_valid
 * @property string       $label
 * @property array        $labels
 * @property bool         $multiple
 * @property string       $name
 * @property array        $options
 * @property string       $original_name
 * @property string       $original_type
 * @property bool         $required
 * @property string       $tag
 * @property string       $text
 * @property string       $type
 * @property string       $validation
 * @property string|array $value
 */
class Field extends \ArrayObject implements FieldContract
{
    protected string $tag = '';

    public function __construct(array $args = [])
    {
        $field = glsr(FieldDefaults::class)->merge($args);
        $field = wp_parse_args($field, [
            'errors' => [],
            'is_custom' => false, // is this a custom field?
            'is_hidden' => false, // is this visibly hidden based on field conditions?
            'is_raw' => false, // Only build the field element, and use the builder instance..
            'is_valid' => false, // Does the field include required parameters (i.e. name, type)?
            'original_name' => $field['name'],
            'original_type' => $field['type'],
        ]);
        parent::__construct($field, \ArrayObject::STD_PROP_LIST | \ArrayObject::ARRAY_AS_PROPS);
        $this->normalize(); // this sets the initial field tag property.
    }

    public function __toString(): string
    {
        return $this->build();
    }

    public function build(): string
    {
        if (!$this->isValid()) {
            return '';
        }
        return $this->buildField();
    }

    public function builder(): BuilderContract
    {
        return glsr(Builder::class);
    }

    public function buildField(): string
    {
        if ($this->is_raw) { // only build the field element
            return $this->builder()->build($this->tag(), $this->toArray());
        }
        return $this->builder()->div(
            $this->buildFieldLabel().$this->buildFieldElement()
        );
    }

    public function buildFieldElement(): string
    {
        return $this->fieldElement()->build([
            'label' => '', // prevent the field label from being built
        ]);
    }

    public function buildFieldLabel(): string
    {
        if (empty($this->label)) {
            return '';
        }
        return $this->builder()->label([
            'for' => !$this->isChoiceField() ? $this->id : '',
            'text' => $this->label,
        ]);
    }

    public function conditions(): array
    {
        $conditions = explode('|', $this->conditions);
        $criteria = array_shift($conditions) ?: 'always';
        if ('always' === $criteria) {
            $conditions = [];
        }
        $conditions = array_map(fn ($val) => explode(':', $val), $conditions);
        $conditions = array_map(fn ($val) => array_slice(array_pad($val, 3, ''), 0, 3), $conditions);
        $conditions = array_map(fn ($val) => array_combine(['name', 'operator', 'value'], $val), $conditions);
        $conditions = array_map(fn ($val) => glsr(FieldConditionDefaults::class)->restrict($val), $conditions);
        $conditions = array_filter($conditions, fn ($val) => !empty($val['name']));
        return compact('criteria', 'conditions');
    }

    public function exchangeArgs(array $args): void
    {
        $this->exchangeArray($args);
    }

    public function exchangeTag(string $tag): void
    {
        $this->tag = $tag;
    }

    public function fieldElement(): FieldElementContract
    {
        $className = Helper::buildClassName($this->original_type, __NAMESPACE__.'\FieldElements');
        $className = glsr()->filterString("field/element/{$this->original_type}", $className, $this);
        if (!class_exists($className)) {
            return new UnknownElement($this);
        }
        if (!(new \ReflectionClass($className))->isInstantiable()) {
            return new UnknownElement($this);
        }
        if (!(new \ReflectionClass($className))->implementsInterface(FieldElementContract::class)) {
            glsr_log()->error("Field Elements must implement FieldElementContract [{$className}]");
            return new UnknownElement($this);
        }
        return new $className($this);
    }

    public function isChoiceField(): bool
    {
        return in_array($this->type, [
            'checkbox',
            'radio',
        ]);
    }

    public function isMultiField(): bool
    {
        if ($this->multiple && in_array($this->type, ['email', 'file', 'select'])) {
            return true; // email and file inputs do not need [] after the name to allow multiple selection
        }
        if ('checkbox' === $this->type && count($this->options) > 1) {
            return true;
        }
        return false;
    }

    public function isValid(): bool
    {
        return $this->is_valid;
    }

    public function location(): string
    {
        return '';
    }

    public function namePrefix(): string
    {
        return glsr()->id;
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($key)
    {
        if (!$this->offsetExists($key)) {
            return null;
        }
        return parent::offsetGet($key);
    }

    public function render(): void
    {
        echo $this->build();
    }

    public function rules(): array
    {
        $rules = explode('|', $this->validation);
        $rules = array_filter($rules);
        $rules = array_map(fn ($val) => explode(':', $val), $rules);
        $rules = array_map(fn ($val) => array_slice(array_pad($val, 2, ''), 0, 2), $rules);
        $rules = array_map(fn ($val) => array_combine(['rule', 'parameters'], $val), $rules);
        $rules = array_map(fn ($val) => glsr(FieldRuleDefaults::class)->restrict($val), $rules);
        $rules = array_filter($rules, fn ($val) => !empty($val['rule']));
        return $rules;
    }

    public function tag(): string
    {
        return $this->tag;
    }

    public function toArray(): array
    {
        return $this->getArrayCopy();
    }

    protected function normalize(): void
    {
        if (!$this->validate()) {
            return;
        }
        // The field tag is set here.
        // The field type may also be modified here.
        $this->fieldElement()->merge();
        glsr()->action("field/{$this->original_type}", $this);
    }

    protected function validate(): bool
    {
        $requiredKeys = array_filter([
            'name' => empty($this->name),
            'type' => empty($this->type),
        ]);
        $this->is_valid = empty($requiredKeys);
        if (!$this->is_valid) {
            glsr_log()
                ->warning('Field properties are missing: '.implode(', ', array_keys($requiredKeys)))
                ->debug($this);
        }
        return $this->is_valid;
    }
}
