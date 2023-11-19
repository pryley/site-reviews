<?php

namespace GeminiLabs\SiteReviews\Tinymce;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

abstract class TinymceGenerator
{
    public array $properties = [];
    public string $tag = '';
    protected array $errors = [];
    protected array $required = [];

    abstract public function fields(): array;

    public function register(): void
    {
        $tinymce = (new \ReflectionClass($this))->getShortName();
        $tinymce = Str::snakeCase($tinymce);
        $tinymce = str_replace('_tinymce', '', $tinymce);
        $this->tag = $tinymce;
        $this->properties = [
            'btn_close' => _x('Close', 'admin-text', 'site-reviews'),
            'btn_okay' => _x('Insert Shortcode', 'admin-text', 'site-reviews'),
            'errors' => $this->errors,
            'fields' => $this->getFields(),
            'label' => $this->title(),
            'required' => $this->required,
            'title' => $this->title(),
        ];
        glsr()->append('mce', $this->properties, $tinymce);
    }

    abstract public function title(): string;

    protected function fieldCategories(string $tooltip = ''): array
    {
        $terms = glsr(Database::class)->terms();
        if (empty($terms)) {
            return [];
        }
        return [
            'label' => _x('Category', 'admin-text', 'site-reviews'),
            'name' => 'assigned_terms',
            'options' => $terms,
            'tooltip' => $tooltip,
            'type' => 'listbox',
        ];
    }

    protected function fieldTypes(string $tooltip = ''): array
    {
        if (count($options = glsr()->retrieveAs('array', 'review_types')) < 2) {
            return [];
        }
        return [
            'label' => _x('Type', 'admin-text', 'site-reviews'),
            'name' => 'type',
            'options' => $options,
            'tooltip' => $tooltip,
            'type' => 'listbox',
        ];
    }

    protected function generateFields(array $fields): array
    {
        $generatedFields = array_map(function ($field) {
            if (empty($field)) {
                return;
            }
            $type = Arr::getAs('string', $field, 'type');
            $method = Helper::buildMethodName('normalize', $type);
            if (method_exists($this, $method)) {
                return call_user_func([$this, $method], $field);
            }
        }, $fields);
        return array_values(array_filter($generatedFields));
    }

    protected function getFields(): array
    {
        $fields = $this->generateFields($this->fields());
        if (!empty($this->errors)) {
            $errors = [];
            foreach ($this->required as $name => $alert) {
                if (false === Arr::searchByKey($name, $fields, 'name')) {
                    $errors[] = $this->errors[$name];
                }
            }
            $this->errors = $errors;
        }
        return empty($this->errors)
            ? $fields
            : $this->errors;
    }

    protected function hideOptions(): array
    {
        $classname = str_replace('Tinymce\\', 'Shortcodes\\', get_class($this));
        $classname = str_replace('Tinymce', 'Shortcode', $classname);
        $hideOptions = glsr($classname)->getHideOptions();
        $options = [];
        foreach ($hideOptions as $name => $tooltip) {
            $options[] = [
                'name' => "hide_{$name}",
                'text' => $name,
                'tooltip' => $tooltip,
                'type' => 'checkbox',
            ];
        }
        return $options;
    }

    protected function normalize(array $field, array $defaults): array
    {
        if (!$this->validate($field)) {
            return [];
        }
        $field = shortcode_atts($defaults, $field);
        return array_filter($field, fn ($value) => '' !== $value);
    }

    protected function normalizeCheckbox(array $field): array
    {
        return $this->normalize($field, [
            'checked' => false,
            'label' => '',
            'minHeight' => '',
            'minWidth' => '',
            'name' => false,
            'text' => '',
            'tooltip' => '',
            'type' => '',
            'value' => '',
        ]);
    }

    protected function normalizeContainer(array $field): array
    {
        if (!array_key_exists('html', $field) && array_key_exists('items', $field)) {
            $field['items'] = $this->generateFields($field['items']);
        }
        return $field;
    }

    protected function normalizeListbox(array $field): array
    {
        $listbox = $this->normalize($field, [
            'label' => '',
            'minWidth' => '',
            'name' => false,
            'options' => [],
            'placeholder' => esc_attr_x('- Select -', 'admin-text', 'site-reviews'),
            'tooltip' => '',
            'type' => '',
            'value' => '',
        ]);
        if (empty($listbox)) {
            return [];
        }
        if (!array_key_exists('', $listbox['options'])) {
            $listbox['options'] = Arr::prepend($listbox['options'], $listbox['placeholder'], '');
        }
        foreach ($listbox['options'] as $value => $text) {
            $listbox['values'][] = [
                'text' => $text,
                'value' => $value,
            ];
        }
        return $listbox;
    }

    protected function normalizeTextbox(array $field): array
    {
        return $this->normalize($field, [
            'hidden' => false,
            'label' => '',
            'maxLength' => '',
            'minHeight' => '',
            'minWidth' => '',
            'multiline' => false,
            'name' => false,
            'size' => '',
            'text' => '',
            'tooltip' => '',
            'type' => '',
            'value' => '',
        ]);
    }

    protected function validate(array $field): bool
    {
        $args = shortcode_atts([
            'label' => '',
            'name' => false,
            'required' => false,
        ], $field);
        if (!$args['name']) {
            return false;
        }
        return $this->validateErrors($args) && $this->validateRequired($args);
    }

    protected function validateErrors(array $args): bool
    {
        if (!isset($args['required']['error'])) {
            return true;
        }
        $this->errors[$args['name']] = $this->normalizeContainer([
            'html' => $args['required']['error'],
            'type' => 'container',
        ]);
        return false;
    }

    protected function validateRequired(array $args): bool
    {
        if (false == $args['required']) {
            return true;
        }
        $alert = _x('Some of the shortcode options are required.', 'admin-text', 'site-reviews');
        if (isset($args['required']['alert'])) {
            $alert = $args['required']['alert'];
        } elseif (!empty($args['label'])) {
            $alert = sprintf(
                _x('The "%s" option is required.', 'the option label (admin-text)', 'site-reviews'),
                str_replace(':', '', $args['label'])
            );
        }
        $this->required[$args['name']] = $alert;
        return false;
    }
}
