<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Contracts\FieldContract;
use GeminiLabs\SiteReviews\Review;

class MetaboxForm extends Form
{
    protected Review $review;

    public function __construct()
    {
        $this->review = glsr_get_review(get_the_ID()); // review is cached
        parent::__construct();
    }

    /**
     * This builds all fields but it does not build the form element.
     */
    public function build(): string
    {
        return $this->buildFields();
    }

    public function field(string $name, array $args): FieldContract
    {
        $args = wp_parse_args($args, [
            'name' => $name,
        ]);
        $field = new MetaboxField($args);
        $this->normalizeField($field);
        return $field;
    }

    public function loadSession(): void
    {
        $this->session = glsr()->args([
            'errors' => [],
            'values' => $this->review->toArray(),
        ]);
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
        $config = glsr()->config('forms/metabox-fields');
        $config = glsr()->filterArray('metabox-form/fields', $config, $this, $this->review); // @todo update hook in addons
        if (2 > count(glsr()->retrieveAs('array', 'review_types'))) {
            unset($config['type']);
        }
        $fields = [];
        foreach ($config as $key => $args) {
            $field = $this->field($key, wp_parse_args($args, [
                'class' => 'glsr-input-value',
                'data-value' => $this->review->get($key),
            ]));
            if ($field->isValid()) {
                $fields[$key] = $field;
            }
        }
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
