<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Contracts\FieldContract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Translation;

class SettingForm extends Form
{
    protected array $groups;

    /**
     * Field dependencies are set after all fields have been added and normalized.
     */
    public function __construct(array $groups = [])
    {
        $this->groups = $groups;
        $values = glsr(OptionManager::class)->all();
        $values = array_intersect_key($values, ['settings' => []]);
        $values = Arr::flatten($values);
        parent::__construct([], $values);
        $this->normalizeDependencies();
    }

    /**
     * This builds all fields into tabbed groups but it does not build the form element.
     */
    public function build(): string
    {
        return $this->buildFields();
    }

    public function config(): array
    {
        return glsr()->settings();
    }

    public function field(string $name, array $args): FieldContract
    {
        if (str_starts_with($name, 'settings.')) {
            $parts = explode('.', $name);
            $args['group'] = count($parts) > 2 ? $parts[1] : '';
        }
        return parent::field($name, $args);
    }

    public function fieldClass(): string
    {
        return SettingField::class;
    }

    protected function buildFields(): string
    {
        $fields = [];
        foreach ($this->hidden() as $field) {
            $fields[] = $field->build();
        }
        foreach ($this->groups as $group => $label) {
            $method = Helper::buildMethodName('templateDataFor', $group);
            $data = method_exists($this, $method)
                ? call_user_func([$this, $method], $group)
                : $this->templateData($group);
            $text = glsr(Template::class)->build("pages/settings/{$group}", $data);
            $fields[] = glsr(Builder::class)->div([
                'class' => 'glsr-nav-view ui-tabs-hide',
                'id' => $group,
                'text' => $text,
            ]);
        }
        $rendered = implode("\n", $fields);
        return $rendered;
    }

    /**
     * Set the data-depends attribute of each field. This is done in the form
     * because field dependencies reference other field names.
     */
    protected function normalizeDependencies(): void
    {
        foreach ($this->fields() as $field) {
            $dependencies = [];
            foreach (Arr::consolidate($field->depends_on) as $path => $value) {
                if ($triggerField = $this->offsetGet($path)) {
                    $name = $triggerField->name;
                    $dependencies[] = compact('name', 'value');
                }
            }
            if (empty($dependencies)) {
                continue;
            }
            $field['data-depends'] = wp_json_encode($dependencies, JSON_HEX_APOS | JSON_HEX_QUOT);
        }
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
        $this->normalizeFieldIsHidden($field);
        $this->normalizeFieldValue($field);
    }

    /**
     * Prefix the field id with the form id then hash it to prevent
     * a really long value since it is based on the setting path.
     */
    protected function normalizeFieldId(FieldContract $field): void
    {
        parent::normalizeFieldId($field);
        if (empty($field->id)) {
            return;
        }
        if ($field->is_raw) {
            return;
        }
        $fieldId = Str::hash($field->id, 8);
        $fieldId = Str::prefix($fieldId, glsr()->prefix);
        $field->id = $fieldId;
    }

    /**
     * Set the is_hidden property of the field. This is done in the form
     * because value is based on other field values from the session.
     */
    protected function normalizeFieldIsHidden(FieldContract $field): void
    {
        $isHidden = false;
        foreach (Arr::consolidate($field->depends_on) as $path => $expectedValue) {
            $default = Arr::get(glsr()->defaults(), $path);
            $value = $this->session->values[$path] ?? $default;
            if (!is_array($expectedValue)) {
                $isHidden = $value !== $expectedValue;
            } elseif (is_array($value)) {
                $isHidden = 0 === count(array_intersect($value, $expectedValue));
            } else {
                $isHidden = !in_array($value, $expectedValue);
            }
            if ($isHidden) {
                $field->is_hidden = true;
                return;
            }
        }
    }

    /**
     * Set the value attribute of the field from the session.
     */
    protected function normalizeFieldValue(FieldContract $field): void
    {
        $value = $this->session->values[$field->original_name] ?? $field->default ?? '';
        $field->value = $value;
    }

    protected function templateData(string $group): array
    {
        $fields = $this->fieldsFor($group);
        $rows = array_reduce($fields, fn ($carry, $field) => $carry.$field->build(), '');
        return [
            'context' => compact('rows'),
        ];
    }

    protected function templateDataForAddons(string $group): array
    {
        $fields = $this->fieldsFor($group);
        $results = [];
        foreach ($fields as $field) {
            $parts = explode('.', $field->original_name);
            $addon = $parts[2] ?? '';
            $results[$addon] ??= '';
            $results[$addon] .= $field->build();
        }
        ksort($results);
        $subsubsub = array_map('ucfirst', array_keys($results));
        $subsubsub = glsr()->filterArray('addon/subsubsub', $subsubsub);
        return [
            'settings' => $results,
            'subsubsub' => $subsubsub,
        ];
    }

    protected function templateDataForIntegrations(string $group): array
    {
        $fields = $this->fieldsFor($group);
        $results = [];
        foreach ($fields as $field) {
            $parts = explode('.', $field->original_name);
            $integration = $parts[2] ?? '';
            $results[$integration] ??= '';
            $results[$integration] .= $field->build();
        }
        ksort($results);
        $subsubsub = array_map('ucfirst', array_keys($results));
        $subsubsub = glsr()->filterArray('integration/subsubsub', $subsubsub);
        return [
            'settings' => $results,
            'subsubsub' => $subsubsub,
        ];
    }

    protected function templateDataForLicenses(string $group): array
    {
        $fields = $this->fieldsFor($group);
        usort($fields, fn ($a, $b) => strnatcasecmp($a->name, $b->name));
        $rows = array_reduce($fields, fn ($carry, $field) => $carry.$field->build(), '');
        return [
            'context' => compact('rows'),
        ];
    }

    protected function templateDataForStrings(string $group): array
    {
        $strings = glsr(Translation::class)->renderAll();
        $class = empty($strings) ? 'glsr-hidden' : '';
        return [
            'context' => [
                'class' => $class,
                'database_key' => OptionManager::databaseKey(),
                'strings' => $strings,
            ],
        ];
    }
}
