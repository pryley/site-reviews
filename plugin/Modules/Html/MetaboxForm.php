<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Contracts\FieldContract;
use GeminiLabs\SiteReviews\Review;

class MetaboxForm extends Form
{
    public Review $review;

    public function __construct(Review $review, array $args = [])
    {
        $this->review = $review;
        parent::__construct($args, array_merge(
            $review->toArray(),
            $review->custom()->toArray(),
        ));
    }

    /**
     * This builds all fields but it does not build the form element.
     */
    public function build(): string
    {
        return $this->buildFields();
    }

    public function config(): array
    {
        $config = glsr()->config('forms/metabox-fields');
        $config = glsr()->filterArray('metabox-form/fields', $config, $this);
        if (2 > count(glsr()->retrieveAs('array', 'review_types'))) {
            unset($config['type']);
        }
        foreach ($config as $key => $values) {
            $value = $this->session->values[$key] ?? '';
            if (is_array($value)) {
                $value = wp_json_encode($value);
            }
            $config[$key] = wp_parse_args($values, [
                'class' => 'glsr-input-value',
                'data-value' => esc_js($value),
            ]);
        }
        return $config;
    }

    public function field(string $name, array $args): FieldContract
    {
        $field = new MetaboxField(wp_parse_args($args, compact('name')));
        $this->normalizeField($field);
        return $field;
    }

    protected function buildFields(): string
    {
        $fields = [];
        foreach ($this->hidden() as $field) {
            $fields[] = $field->build();
        }
        foreach ($this->visible() as $field) {
            $fields[] = $field->build();
        }
        $rendered = implode("\n", $fields);
        return $rendered;
    }

    /**
     * @return FieldContract[]
     */
    protected function fieldsAll(): array
    {
        $fields = parent::fieldsAll();
        $fields = glsr()->filterArray('metabox-form/fields/all', $fields, $this);
        return $fields;
    }

    /**
     * @return FieldContract[]
     */
    protected function fieldsHidden(): array
    {
        $fields = [];
        $fields = glsr()->filterArray('metabox-form/fields/hidden', $fields, $this);
        return $fields;
    }

    /**
     * @return FieldContract[]
     */
    protected function fieldsVisible(): array
    {
        $fields = parent::fieldsVisible();
        $fields = glsr()->filterArray('metabox-form/fields/visible', $fields, $this);
        return $fields;
    }

    /**
     * Normalize the field with the form's session data.
     * Any normalization that is not specific to the form or session data
     * should be done in the field itself.
     */
    protected function normalizeField(FieldContract $field): void
    {
        $this->normalizeFieldChecked($field);
        $this->normalizeFieldDisabled($field);
        $this->normalizeFieldValue($field);
    }

    /**
     * Set the disabled attribute of the field from the \WP_Screen action.
     */
    protected function normalizeFieldDisabled(FieldContract $field): void
    {
        $field->disabled = 'add' !== glsr_current_screen()->action
            && !wp_doing_ajax();
    }
}
