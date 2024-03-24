<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Field;

class Honeypot
{
    public function build(string $formId): string
    {
        $field = new Field([
            'class' => 'glsr-input glsr-input-text',
            'label' => esc_html__('Your review', 'site-reviews'),
            'name' => $this->hash($formId),
            'type' => 'text',
        ]);
        $field->id = "{$field->id}-{$formId}";
        return $field->builder()->div([
            'class' => glsr(Style::class)->classes('field'),
            'style' => 'display:none;',
            'text' => $field->buildFieldLabel().$field->buildFieldElement(),
        ]);
    }

    public function hash(string $formId): string
    {
        if (is_array($formId)) { // @phpstan-ignore-line
            glsr_log()
                ->warning('Honeypot expects the submitted form ID to be a string, an array was passed instead.')
                ->debug($formId);
            glsr_trace(10);
            $formId = array_shift($formId);
        }
        $formId = Cast::toString($formId);
        return Str::hash($formId, 8);
    }

    public function verify(string $hash, string $formId): bool
    {
        return hash_equals($this->hash($formId), $hash);
    }
}
