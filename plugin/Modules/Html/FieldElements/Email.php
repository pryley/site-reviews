<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

class Email extends Text
{
    public function required(): array
    {
        return [
            'validation' => 'email',
        ];
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
        $this->field->value = glsr(Sanitizer::class)->sanitizeUserEmail(wp_get_current_user());
    }
}
