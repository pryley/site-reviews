<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Modules\Sanitizer;

class TooltipButtonDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'hover' => 'bool',
    ];

    protected function defaults(): array
    {
        return [
            'class' => '',
            'hover' => false, // if hover and focus open the popover
            'icon' => 'dashicons-editor-help',
        ];
    }

    /**
     * Finalize provided values, this always runs last.
     */
    protected function finalize(array $values = []): array
    {
        $help = $values['hover'] ? 'glsr-help' : '';
        $values['class'] = glsr(Sanitizer::class)->sanitizeAttrClass(
            "glsr-tooltip-button {$help} dashicons-before {$values['icon']} {$values['class']}"
        );
        return $values;
    }
}
