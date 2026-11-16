<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Defaults\TooltipButtonDefaults;
use GeminiLabs\SiteReviews\Defaults\TooltipPopoverDefaults;

/**
 * The tooltips and popovers of the plugin screens.
 */
class Tooltip
{
    /**
     * The button that opens a tooltip or popover on click, hover, and focus.
     */
    public function button(string $id, string $label, array $args = []): string
    {
        $args = glsr(TooltipButtonDefaults::class)->restrict($args);
        return glsr(Builder::class)->button([
            'aria-label' => $label,
            'class' => $args['class'],
            'popovertarget' => $id,
            'type' => 'button',
        ]);
    }

    /**
     * The button and popover used with labels in the plugin settings.
     */
    public function help(string $id, string $label, string $content, array $args = []): string
    {
        /* translators: %s: the label of a setting */
        $buttonLabel = sprintf(_x('More information about %s', 'admin-text', 'site-reviews'), wp_strip_all_tags($label));
        return $this->button($id, $buttonLabel, ['hover' => true]).$this->popover($id, $content, $args);
    }

    /**
     * Popovers are larger tooltips that can hold links, lists, or code; they stay open
     * until clicked outside or Escape is pressed.
     */
    public function popover(string $id, string $content, array $args = []): string
    {
        $args = glsr(TooltipPopoverDefaults::class)->restrict($args);
        $text = !empty($args['syntax'])
            ? $this->code($content, $args['syntax'])
            : $this->clean($content, $args['inline'], true);
        return glsr(Builder::class)->build($args['inline'] ? 'span' : 'div', [
            'class' => $args['class'],
            'id' => $id,
            'popover' => 'auto',
            'text' => $text,
        ]);
    }

    /**
     * Tooltips are displayed inline in a span. They are meant for short text
     * and are shown on hover or focus of the element naming it in data-tooltip.
     */
    public function tooltip(string $id, string $content, array $args = []): string
    {
        unset($args['syntax']); // a tooltip doesn't show code
        $args = glsr(TooltipPopoverDefaults::class)->restrict($args);
        return glsr(Builder::class)->span([
            'class' => $args['class'],
            'id' => $id,
            'popover' => 'manual',
            'role' => 'tooltip',
            'text' => $this->clean($content, true, false),
        ]);
    }

    protected function clean(string $content, bool $isInline, bool $hasLinks): string
    {
        $allowed = array_fill_keys(['br', 'code', 'em', 'strong', 'u'], []);
        if (!$isInline) {
            $allowed += array_fill_keys(['li', 'ol', 'p', 'ul'], []);
        }
        if ($hasLinks) {
            $allowed['a'] = ['href' => true, 'target' => true];
        }
        return wp_kses($content, $allowed, ['http', 'https']);
    }

    protected function code(string $content, string $syntax): string
    {
        $code = glsr(Builder::class)->code([
            'class' => "language-{$syntax}",
            'text' => esc_html($content),
        ]);
        return glsr(Builder::class)->pre($code);
    }
}
