<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Database\OptionManager;

class SettingField extends Field
{
    public function __construct(array $field = [])
    {
        $this->field = wp_parse_args($field, [
            'errors' => false,
            'is_hidden' => false,
            'is_multi' => false,
            'is_raw' => false,
            'is_valid' => true,
            'path' => '',
        ]);
        $this->normalize();
    }

    /**
     * @return SettingBuilder
     */
    public function builder()
    {
        return glsr(SettingBuilder::class);
    }

    /**
     * @return string
     */
    public function getFieldClasses()
    {
        $classes = [];
        if ($this->field['is_hidden']) {
            $classes[] = 'hidden';
        }
        $classes = glsr()->filterArray('rendered/field/classes', $classes, $this->field);
        return implode(' ', $classes);
    }

    /**
     * @return string
     */
    public function getFieldDependsOn()
    {
        return !empty($this->field['data-depends'])
            ? $this->field['data-depends']
            : '';
    }

    /**
     * @return string
     */
    public function getFieldPrefix()
    {
        return OptionManager::databaseKey();
    }

    /**
     * @return string
     */
    protected function buildField()
    {
        return glsr(Template::class)->build('partials/form/table-row', [
            'context' => [
                'class' => $this->getFieldClasses(),
                'field' => $this->builder()->{$this->field['type']}($this->field),
                'label' => $this->builder()->label([
                    'for' => $this->field['id'],
                    'text' => $this->field['legend'],
                ]),
            ],
            'field' => $this->field,
        ]);
    }

    /**
     * @return string
     */
    protected function buildMultiField()
    {
        $dependsOn = $this->getFieldDependsOn();
        unset($this->field['data-depends']);
        return glsr(Template::class)->build('partials/form/table-row-multiple', [
            'context' => [
                'class' => $this->getFieldClasses(),
                'depends_on' => $dependsOn,
                'field' => $this->builder()->{$this->field['type']}($this->field),
                'label' => $this->field['legend'],
                'legend' => $this->field['legend'],
            ],
            'field' => $this->field,
        ]);
    }

    /**
     * @param string $className
     * @return array
     */
    protected function mergeFieldArgs($className)
    {
        return $className::merge($this->field, 'setting');
    }
}
