<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Modules\Html\SettingField;

class Textarea extends AbstractFieldElement
{
    public function defaults(): array
    {
        $locations = [
            'setting' => 'large-text',
            'widget' => 'widefat',
        ];
        return array_filter([
            'class' => $locations[$this->field->location()] ?? '',
        ]);
    }

    public function required(): array
    {
        $field = $this->field;
        $autosize = $field instanceof SettingField && $field->autosize;
        $locations = [
            'setting' => $autosize ? 'autosized' : '',
        ];
        $styles = [
            'setting' => $autosize ? $this->autosizeStyle() : '',
        ];
        return [
            'class' => $locations[$this->field->location()] ?? '',
            'style' => $styles[$this->field->location()] ?? '',
        ];
    }

    protected function autosizeStyle(): string
    {
        // CSS field-sizing ignores the rows attribute. Set the start height from it.
        $rows = max(2, (int) $this->field->rows);
        return "min-height:{$rows}lh";
    }

    protected function buildSettingField(Arguments $args): string
    {
        $textarea = $this->field->builder()->build($this->tag(), $args->toArray());
        if (!$this->hasEditorControls($args)) {
            return $textarea;
        }
        return $this->field->builder()->div([
            'class' => 'glsr-textarea-editor',
            'text' => $textarea.$this->toolbar($args),
        ]);
    }

    protected function hasEditorControls(Arguments $args): bool
    {
        return !empty($args->default) || !empty($args->tags);
    }

    protected function resetButton(Arguments $args): string
    {
        if (empty($args->default)) {
            return '';
        }
        $button = $this->field->builder()->button([
            'class' => 'button is-reset-button hide-if-no-js',
            'data-default' => esc_attr($args->default),
            'data-tippy-content' => esc_attr_x('Reset value to default', 'admin-text', 'site-reviews'),
            'text' => $this->field->builder()->span([
                'aria-hidden' => 'true',
                'class' => 'dashicons dashicons-undo',
            ]),
            'type' => 'button',
        ]);
        return $button;
    }

    protected function tagInserter(Arguments $args): string
    {
        if (empty($args->tags)) {
            return '';
        }
        $tags = array_keys($args->tags);
        $options = array_combine($tags, $tags);
        return $this->field->builder()->select([
            'aria-label' => esc_attr_x('Insert a tag', 'admin-text', 'site-reviews'),
            'class' => 'hide-if-no-js',
            'options' => ['' => _x('Insert a tag', 'admin-text', 'site-reviews')] + $options,
        ]);
    }

    protected function toolbar(Arguments $args): string
    {
        if (empty($args->tags)) {
            return $this->resetButton($args);
        }
        return $this->field->builder()->div([
            'class' => 'glsr-textarea-editor__toolbar',
            'text' => $this->tagInserter($args).$this->resetButton($args),
        ]);
    }
}
