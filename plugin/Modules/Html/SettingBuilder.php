<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

class SettingBuilder extends Builder
{
    /**
     * @return string|void
     */
    protected function buildFormInputChoice()
    {
        if (!empty($this->args->text)) {
            $this->args->set('label', $this->args->text);
        }
        return $this->buildFormLabel([
            'class' => 'glsr-'.$this->args->type.'-label',
            'text' => $this->buildOpeningTag().' '.$this->args->label.'<span></span>',
        ]);
    }

    /**
     * @return array
     */
    protected function normalize(array $args, $type)
    {
        if (class_exists($className = $this->getFieldClassName($type))) {
            $args = $className::merge($args, 'setting');
        }
        return $args;
    }
}
