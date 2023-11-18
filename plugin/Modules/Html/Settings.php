<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\License;
use GeminiLabs\SiteReviews\Modules\Translation;

class Settings
{
    public function buildFields(string $id): string
    {
        $method = Helper::buildMethodName($id, 'getTemplateDataFor');
        $data = !method_exists($this, $method)
            ? $this->getTemplateData($id)
            : $this->$method($id);
        return glsr(Template::class)->build("pages/settings/{$id}", $data);
    }

    /**
     * @return mixed
     */
    protected function getFieldDefault(array $field)
    {
        return Arr::get($field, 'default');
    }

    protected function getFieldNameForDependsOn(string $path): string
    {
        $fieldName = Str::convertPathToName($path, OptionManager::databaseKey());
        return $this->isMultiDependency($path)
            ? Str::suffix($fieldName, '[]')
            : $fieldName;
    }

    protected function getSettingFields(string $path): array
    {
        return array_filter(glsr()->settings(),
            fn ($key) => str_starts_with($key, $path),
            ARRAY_FILTER_USE_KEY
        );
    }

    protected function getSettingRows(array $fields): string
    {
        $rows = '';
        foreach ($fields as $name => $field) {
            $field = wp_parse_args($field, [
                'name' => $name,
            ]);
            $rows .= new SettingField($this->normalize($field));
        }
        return $rows;
    }

    protected function getTemplateData(string $id): array
    {
        $fields = $this->getSettingFields($this->normalizeSettingPath($id));
        return [
            'context' => [
                'rows' => $this->getSettingRows($fields),
            ],
        ];
    }

    protected function getTemplateDataForAddons(string $id): array
    {
        $fields = $this->getSettingFields($this->normalizeSettingPath($id));
        $settings = Arr::convertFromDotNotation($fields);
        $settingKeys = array_keys($settings['settings']['addons']);
        $results = [];
        foreach ($settingKeys as $key) {
            $addonFields = array_filter($fields,
                fn ($path) => str_starts_with($path, "settings.addons.{$key}"),
                ARRAY_FILTER_USE_KEY
            );
            $results[$key] = $this->getSettingRows($addonFields);
        }
        ksort($results);
        $subsubsub = array_map('ucfirst', $settingKeys);
        $subsubsub = glsr()->filterArray('addon/subsubsub', $subsubsub);
        return [
            'settings' => $results,
            'subsubsub' => $subsubsub,
        ];
    }

    protected function getTemplateDataForLicenses(string $id): array
    {
        $fields = $this->getSettingFields($this->normalizeSettingPath($id));
        ksort($fields);
        return [
            'context' => [
                'rows' => $this->getSettingRows($fields),
            ],
            'license' => glsr(License::class)->status(),
        ];
    }

    protected function getTemplateDataForStrings(): array
    {
        $strings = glsr(Translation::class)->renderAll();
        $class = empty($strings)
            ? 'glsr-hidden'
            : '';
        return [
            'context' => [
                'class' => $class,
                'database_key' => OptionManager::databaseKey(),
                'strings' => $strings,
            ],
        ];
    }

    /**
     * @param string|array $expectedValue
     */
    protected function isFieldHidden(string $path, $expectedValue): bool
    {
        $optionValue = glsr(OptionManager::class)->get($path,
            Arr::get(glsr()->defaults(), $path)
        );
        if (is_array($expectedValue)) {
            return is_array($optionValue)
                ? 0 === count(array_intersect($optionValue, $expectedValue))
                : !in_array($optionValue, $expectedValue);
        }
        return $optionValue != $expectedValue;
    }

    protected function isMultiDependency(string $path): bool
    {
        $settings = glsr()->settings();
        if (isset($settings[$path])) {
            $field = $settings[$path];
            return ('checkbox' === $field['type'] && !empty($field['options']))
                || !empty($field['multiple']);
        }
        return false;
    }

    protected function normalize(array $field): array
    {
        $field = $this->normalizeDependsOn($field);
        $field = $this->normalizeLabelAndLegend($field);
        $field = $this->normalizeValue($field);
        return $field;
    }

    protected function normalizeDependsOn(array $field): array
    {
        if (!empty($field['depends_on']) && is_array($field['depends_on'])) {
            $isFieldHidden = false;
            $conditions = [];
            foreach ($field['depends_on'] as $path => $value) {
                $conditions[] = [
                    'name' => $this->getFieldNameForDependsOn($path),
                    'value' => $value,
                ];
                if ($this->isFieldHidden($path, $value)) {
                    $isFieldHidden = true;
                }
            }
            $field['data-depends'] = json_encode($conditions, JSON_HEX_APOS | JSON_HEX_QUOT);
            $field['is_hidden'] = $isFieldHidden;
        }
        return $field;
    }

    protected function normalizeLabelAndLegend(array $field): array
    {
        if (!empty($field['label'])) {
            $field['legend'] = $field['label'];
            unset($field['label']);
        } else {
            $field['is_valid'] = false;
            glsr_log()->warning('Setting field is missing a label')->debug($field);
        }
        return $field;
    }

    protected function normalizeValue(array $field): array
    {
        if (!isset($field['value'])) {
            $field['value'] = glsr(OptionManager::class)->get(
                $field['name'],
                $this->getFieldDefault($field)
            );
        }
        return $field;
    }

    protected function normalizeSettingPath(string $path): string
    {
        return Str::prefix(rtrim($path, '.'), 'settings.');
    }
}
