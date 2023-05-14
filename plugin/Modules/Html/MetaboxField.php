<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

class MetaboxField extends Field
{
    /**
     * @return SettingBuilder
     */
    public function builder()
    {
        return glsr(MetaboxBuilder::class);
    }

    protected function buildField(): string
    {
        return glsr(Template::class)->build('partials/editor/metabox-field', [
            'context' => [
                'class' => $this->getFieldClasses(),
                'field' => $this->builder()->raw($this->field),
                'label' => $this->builder()->label([
                    'for' => $this->field['id'],
                    'text' => $this->field['label'],
                ]),
            ],
            'field' => $this->field,
        ]);
    }

    protected function mergeFieldArgs(string $className): array
    {
        return $className::merge($this->field, 'metabox');
    }
}
