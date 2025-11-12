<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

class Text extends AbstractFieldElement
{
    public function defaults(): array
    {
        $settingInputSizeClass = 1 !== preg_match('/(tiny|small|regular|large)-text/', $this->field->class)
            ? 'regular-text'
            : '';
        $locations = [
            'setting' => $settingInputSizeClass,
            'widget' => 'widefat',
        ];
        return array_filter([
            'class' => $locations[$this->field->location()] ?? '',
        ]);
    }

    public function tag(): string
    {
        return 'input';
    }

    protected function normalizeValue(): void
    {
        $this->field->value = Cast::toString($this->field->value);
        if (!empty($this->field->value)) {
            return;
        }
        if ('review' !== $this->field->location()) {
            return;
        }
        if (!in_array($this->field->original_name, glsr_get_option('forms.autofill', [], 'array'))) {
            return;
        }
        $this->field->value = glsr(Sanitizer::class)->sanitizeUserName(wp_get_current_user());
    }
}
