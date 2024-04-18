<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Contracts\FieldContract;
use GeminiLabs\SiteReviews\Contracts\FormContract;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Captcha;
use GeminiLabs\SiteReviews\Modules\Honeypot;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Modules\Style;

class Form extends \ArrayObject implements FormContract
{
    protected Arguments $args;
    protected Arguments $session;

    public function __construct(array $args = [], array $values = [])
    {
        $defaults = [
            'button_text' => __('Submit Form', 'site-reviews'),
            'button_text_loading' => __('Submitting, please wait...', 'site-reviews'),
            'id' => glsr(Sanitizer::class)->sanitizeIdHash(''),
        ];
        $this->args = glsr()->args(wp_parse_args($args, $defaults));
        $this->loadSession($values);
        parent::__construct($this->fieldsAll(), \ArrayObject::STD_PROP_LIST | \ArrayObject::ARRAY_AS_PROPS);
        array_map([$this, 'normalizeConditions'], $this->fields());
    }

    public function args(): Arguments
    {
        return $this->args;
    }

    public function build(): string
    {
        return glsr(Template::class)->build('templates/reviews-form', [
            'args' => $this->args,
            'context' => [
                'class' => $this->classAttrForm(),
                'fields' => $this->buildFields(),
                'response' => $this->buildResponse(),
                'submit_button' => $this->buildSubmitButton(),
            ],
            'form' => $this,
        ]);
    }

    public function config(): array
    {
        return [];
    }

    public function field(string $name, array $args): FieldContract
    {
        $field = new Field(wp_parse_args($args, compact('name')));
        $this->normalizeField($field);
        return $field;
    }

    /**
     * @return FieldContract[]
     */
    public function fields(): array
    {
        return $this->getArrayCopy();
    }

    /**
     * @return FieldContract[]
     */
    public function fieldsFor(string $group): array
    {
        return array_filter($this->fields(), fn ($field) => $group === $field->group);
    }

    /**
     * @return FieldContract[]
     */
    public function hidden(): array
    {
        $fields = [];
        foreach ($this->fields() as $field) {
            if ('hidden' === $field->original_type) {
                $fields[] = $field;
            }
        }
        return $fields;
    }

    public function loadSession(array $values): void
    {
        $this->session = glsr()->args([
            'errors' => [],
            'message' => '',
            'values' => $values,
        ]);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($key)
    {
        $iterator = $this->getIterator();
        if (is_numeric($key)) {
            return $iterator[$key] ?? null;
        }
        foreach ($iterator as $field) {
            if ($key === $field->original_name) {
                return $field;
            }
        }
        return null;
    }

    public function session(): Arguments
    {
        return $this->session;
    }

    /**
     * @return FieldContract[]
     */
    public function visible(): array
    {
        $fields = [];
        foreach ($this->fields() as $field) {
            if ('hidden' !== $field->original_type) {
                $fields[] = $field;
            }
        }
        return $fields;
    }

    protected function buildFields(): string
    {
        $fields = [];
        foreach ($this->hidden() as $field) {
            $fields[] = $field->build();
        }
        $fields[] = glsr(Honeypot::class)->build($this->args->id);
        foreach ($this->visible() as $field) {
            $fields[] = $field->build();
        }
        $rendered = implode("\n", $fields);
        $rendered = glsr()->filterString('form/build/fields', $rendered, $this);
        return $rendered;
    }

    protected function buildResponse(): string
    {
        $captcha = glsr(Captcha::class)->container();
        $response = glsr(Template::class)->build('templates/form/response', [
            'context' => [
                'class' => $this->classAttrResponse(),
                'message' => wpautop($this->session->message),
            ],
            'has_errors' => !empty($this->session->errors),
        ]);
        $rendered = $captcha.$response;
        $rendered = glsr()->filterString('form/build/response', $rendered, $this);
        return $rendered;
    }

    protected function buildSubmitButton(): string
    {
        $rendered = glsr(Template::class)->build('templates/form/submit-button', [
            'context' => [
                'class' => $this->classAttrSubmitButton(),
                'loading_text' => $this->args->button_text_loading,
                'text' => $this->args->button_text,
            ],
        ]);
        $rendered = glsr()->filterString('form/build/submit_button', $rendered, $this);
        return $rendered;
    }

    protected function classAttrForm(): string
    {
        $classes = [
            $this->args->class,
            glsr(Style::class)->classes('form'),
        ];
        if (!empty($this->session->errors)) {
            $classes[] = glsr(Style::class)->validation('form_error');
        }
        $classes = implode(' ', $classes);
        $classes = glsr(Sanitizer::class)->sanitizeAttrClass($classes);
        return $classes;
    }

    protected function classAttrResponse(): string
    {
        $classes = [
            glsr(Style::class)->validation('form_message'),
        ];
        if (!empty($this->session->errors)) {
            $classes[] = glsr(Style::class)->validation('form_message_failed');
        }
        $classes = implode(' ', $classes);
        $classes = glsr(Sanitizer::class)->sanitizeAttrClass($classes);
        return $classes;
    }

    protected function classAttrSubmitButton(): string
    {
        $classes = glsr(Style::class)->classes('button');
        $classes = glsr(Sanitizer::class)->sanitizeAttrClass($classes);
        return $classes;
    }

    /**
     * @return FieldContract[]
     */
    protected function fieldsAll(): array
    {
        $fields = array_merge($this->fieldsHidden(), $this->fieldsVisible());
        $fields = array_values($fields);
        return $fields;
    }

    /**
     * @return FieldContract[]
     */
    protected function fieldsHidden(): array
    {
        return [];
    }

    /**
     * @return FieldContract[]
     */
    protected function fieldsVisible(): array
    {
        $fields = [];
        foreach ($this->config() as $name => $args) {
            $field = $this->field($name, $args);
            if ($field->isValid()) {
                $fields[$name] = $field;
            }
        }
        return $fields;
    }

    /**
     * @todo Should this be done manually? This shouldn't be run when getting field rules for validation
     */
    protected function normalizeConditions(FieldContract $field): void
    {
        if (!$conditions = $field->conditions()) {
            return;
        }
        $results = [];
        foreach ($conditions['conditions'] as $args) {
            if (!$triggerField = $this->offsetGet($args['name'])) {
                $results[] = true; // ignore if the condition's field does not exist
                continue;
            }
            $results[] = (new FieldCondition($args, $triggerField))->isValid();
        }
        $results = array_filter($results);
        if (count($results) === count($conditions['conditions'])) {
            return; // all conditions valid
        }
        if ('any' === $conditions['criteria'] && !empty($results)) {
            return; // some conditions valid
        }
        $field->is_hidden = true;
        // reset value
    }

    /**
     * Normalize the field with the form's session data.
     * Any normalization that is not specific to the form or session data
     * should be done in the field itself.
     */
    protected function normalizeField(FieldContract $field): void
    {
        $this->normalizeFieldChecked($field);
        $this->normalizeFieldErrors($field);
        $this->normalizeFieldId($field);
        $this->normalizeFieldValue($field);
    }

    /**
     * Set the checked attribute of the field from the session.
     */
    protected function normalizeFieldChecked(FieldContract $field): void
    {
        if (!$field->isChoiceField()) {
            return;
        }
        if (!is_scalar($field->value)) {
            return;
        }
        $value = Cast::toString($this->session->values[$field->original_name] ?? '');
        if (empty($value)) {
            return;
        }
        $field->checked = (string) $field->value === $value;
    }

    /**
     * Set the field errors from the session.
     */
    protected function normalizeFieldErrors(FieldContract $field): void
    {
        $errors = $this->session->errors[$field->original_name] ?? [];
        $field->errors = Arr::consolidate($errors);
    }

    /**
     * Prefix the field id with the form id.
     */
    protected function normalizeFieldId(FieldContract $field): void
    {
        if (empty($this->args->id)) {
            return;
        }
        if (empty($field->id)) {
            return;
        }
        if ($field->is_raw) {
            return;
        }
        $fieldId = Str::removePrefix($field->id, $field->namePrefix());
        $fieldId = Str::prefix($fieldId, $this->args->id);
        $field->id = $fieldId;
    }

    /**
     * Set the value attribute of the field from the session.
     */
    protected function normalizeFieldValue(FieldContract $field): void
    {
        if ($field->isChoiceField()) {
            $value = Cast::toArray($this->session->values[$field->original_name] ?? []);
        } else {
            $value = Cast::toString($this->session->values[$field->original_name] ?? '');
        }
        if (empty($value)) {
            return;
        }
        $field->value = $value;
    }
}
