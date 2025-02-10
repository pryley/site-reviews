<?php

namespace GeminiLabs\SiteReviews\Tinymce;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;

abstract class TinymceGenerator
{
    public array $properties = [];
    public ShortcodeContract $shortcode;
    public string $tag = '';
    protected array $errors = [];
    protected array $required = [];

    abstract public function fields(): array;

    public function register(): void
    {
        $this->shortcode = $this->shortcode();
        $this->tag = $this->shortcode->tag;
        $fields = $this->getFields();
        $this->properties = [
            'btn_close' => _x('Close', 'admin-text', 'site-reviews'),
            'btn_okay' => _x('Insert Shortcode', 'admin-text', 'site-reviews'),
            'errors' => $this->errors,
            'fields' => $fields,
            'label' => $this->shortcode->name,
            'required' => $this->required,
            'title' => $this->shortcode->name,
        ];
        glsr()->append('mce', $this->properties, $this->tag);
    }

    abstract public function shortcode(): ShortcodeContract;

    protected function generateFields(array $fields): array
    {
        $generatedFields = array_map(function ($field) {
            if (empty($field)) {
                return;
            }
            $type = Arr::getAs('string', $field, 'type', 'textbox');
            $method = Helper::buildMethodName('normalize', $type);
            if (method_exists($this, $method)) {
                return call_user_func([$this, $method], $field);
            }
        }, $fields);
        return array_values(array_filter($generatedFields));
    }

    protected function getFields(): array
    {
        $fields = $this->fields();
        $fields = glsr()->filterArray("tinymce/fields/{$this->shortcode->tag}", $fields);
        $fields = $this->generateFields($fields);
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
        $hideOptions = $this->shortcode->options('hide');
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
            'placeholder' => esc_attr_x('— Select —', 'admin-text', 'site-reviews'),
            'tooltip' => '',
            'type' => 'listbox',
            'value' => '',
        ]);
        if (empty($listbox)) {
            return [];
        }
        if (empty($listbox['options'])) {
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
            'type' => 'textbox',
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
