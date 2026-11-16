<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Modules\Sanitizer;

class TooltipPopoverDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'inline' => 'bool',
        'placement' => 'string',
        'syntax' => 'string',
    ];

    /**
     * The values that should be constrained after sanitization is run.
     * This is done after $casts and $sanitize.
     */
    public array $enums = [
        'placement' => ['bottom', 'top', 'top-start'],
        'syntax' => ['bash', 'css', 'html', 'js', 'php', 'shortcode'],
    ];

    protected function defaults(): array
    {
        return [
            'class' => '',
            'inline' => false, // if the popover is printed inside a paragraph
            'placement' => 'top',
            'syntax' => '', // the language of a popover that shows code
        ];
    }

    /**
     * Finalize provided values, this always runs last.
     */
    protected function finalize(array $values = []): array
    {
        $syntax = !empty($values['syntax']) ? 'is-code' : '';
        $values['inline'] = $values['inline'] && empty($values['syntax']); // code is a block
        $placement = match ($values['placement']) {
            'bottom' => 'is-below',
            'top-start' => 'is-start',
            default => '',
        };
        $values['class'] = glsr(Sanitizer::class)->sanitizeAttrClass(
            "glsr-tooltip {$values['class']} {$syntax} {$placement}"
        );
        return $values;
    }
}
