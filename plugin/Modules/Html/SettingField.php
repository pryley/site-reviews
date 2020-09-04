<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Database\OptionManager;

class SettingField extends Field
{
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
                'class' => $this->getFieldClass(),
                'field' => $this->builder()->{$this->field['type']}($this->field),
                'label' => $this->builder()->label($this->field['legend'], ['for' => $this->field['id']]),
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
                'class' => $this->getFieldClass(),
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
